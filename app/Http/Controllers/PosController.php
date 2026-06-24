<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Company;
use App\Models\CompanyOrder;
use App\Models\CompanyOrderItem;
use App\Models\CompanyLedgerEntry;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\LeftoverPiece;
use App\Models\ShopProduct;
use App\Services\PricingService;
use App\Support\Inventory;
use App\Support\PaymentMethod;
use App\Support\ShopContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use App\Services\LocalSmsService;

class PosController extends Controller
{
    /**
     * Get recent customers (last 10 used)
     */
    public function recentCustomers()
    {
        $shopId = ShopContext::activeShopId();
        
        $recent = Sale::where('shop_id', $shopId)
            ->whereNotNull('customer_phone')
            ->select('customer_name', 'customer_phone', 'customer_address')
            ->distinct('customer_phone')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get()
            ->map(fn($sale) => [
                'name' => $sale->customer_name,
                'phone' => $sale->customer_phone,
                'address' => $sale->customer_address,
            ]);
        
        return response()->json($recent);
    }

    public function index(Request $request)
    {
        $q = trim((string)$request->get('q', ''));
        $g = trim((string)$request->get('g', ''));
        $c = trim((string)$request->get('c', ''));

        // Check if a specific shop is selected (not "All Shops")
        $shopId = ShopContext::activeShopId();

        // Companies dropdown: show every active company by default, and filter by the selected group only when needed.
        $companies = $this->posCompaniesForGroup($g);

        // Products list
        $productsQuery = Product::query()
            ->select([
                'id',
                'company_id',
                'category_id',
                'sheet_design_id',
                'name',
                'sku',
                'mrp',
                'selling_price_default',
                'sheet_full_w',
                'sheet_full_l',
                'width_in',
                'length_in',
                'height_in',
                'stock_qty',
                'is_active',
            ])
            ->with([
                'company',
                'category',
                'shopProducts' => function ($qq) use ($shopId) {
                    if (is_numeric($shopId)) {
                        $qq->where('shop_id', $shopId);
                    }
                },
                'sheetDesign.products.company',
                'sheetDesign.products.shopProducts' => function ($qq) use ($shopId) {
                    if (is_numeric($shopId)) {
                        $qq->where('shop_id', $shopId);
                    }
                }
            ])
            ->where('is_active', 1);

        if ($g !== '') {
            $this->applyCompanyGroupFilter($productsQuery, $g);
        }

        if ($c !== '' && is_numeric($c)) {
            $productsQuery->where('company_id', (int)$c);
        }

        if ($q !== '') {
            $qSearch = str_replace('*', '%', $q);
            $productsQuery->where(function ($qq) use ($q, $qSearch) {
                $qq->where('name', 'like', "%{$qSearch}%")
                    ->orWhere('sku', 'like', "%{$qSearch}%")
                    ->orWhere('id', $q);
            });
        }

        $products = $this->fetchPosProducts($productsQuery, $shopId, 250, $q !== '');

        // Leftovers (for leftover tab)
        $leftovers = [];
        if ($g === 'leftover') {
            $leftovers = LeftoverPiece::with(['product.company','product.category'])
                ->where('is_active', 1)
                ->where('qty', '>', 0)
                ->orderByDesc('id')
                ->limit(250)
                ->get();

            // no need to fetch product list on leftover tab
            $products = collect();
        }

        $needsShopSelection = !is_numeric($shopId);

        // Cart (only process if a specific shop is selected)
        $cartRows = [];
        $cartSummary = $this->emptyCartSummary();

        if (!$needsShopSelection) {
            $cart = session()->get('mt_cart', []);
            [$cartRows, $cartSummary] = $this->recalcCart($cart);

            // If old cart had broken rows, keep only valid ones
            $fixedCart = [];
            foreach ($cartRows as $r) {
                $fixedCart[$r['row_id']] = $r['raw_cart_item'];
            }
            session()->put('mt_cart', $fixedCart);
            session()->put('mt_cart_overall_discount', $cartSummary['overall_discount']);
        }

        return view('mt.pos.index', [
            'q' => $q,
            'g' => $g,
            'c' => $c,
            'companies' => $companies,
            'products' => $products,
            'leftovers' => $leftovers,
            'cartRows' => $cartRows,
            'cartTotal' => $cartSummary['total'],
            'cartProfit' => $cartSummary['profit'],
            'cartGrossTotal' => $cartSummary['gross_total'],
            'cartSubtotal' => $cartSummary['subtotal'],
            'cartLineDiscountTotal' => $cartSummary['line_discount_total'],
            'cartOverallDiscount' => $cartSummary['overall_discount'],
            'cartTotalDiscount' => $cartSummary['total_discount'],
            'needsShopSelection' => $needsShopSelection,
        ]);
    }

    /**
     * Typeahead suggestions for POS search
     * GET /pos/suggest?q=...&g=...&c=...
     */
    public function suggest(Request $request)
    {
        $q = trim((string)$request->get('q', ''));
        $g = trim((string)$request->get('g', ''));
        $c = trim((string)$request->get('c', ''));
        $shopId = ShopContext::activeShopId();

        if ($q === '' || mb_strlen($q) < 1) {
            return response()->json(['items' => []]);
        }

        $query = Product::query()
            ->select([
                'id',
                'company_id',
                'category_id',
                'sheet_design_id',
                'name',
                'sku',
                'mrp',
                'selling_price_default',
                'stock_qty',
                'max_discount_percent',
                'sheet_full_w',
                'sheet_full_l',
                'width_in',
                'length_in',
                'height_in',
                'is_active',
            ])
            ->with([
                'company',
                'category',
                'shopProducts' => function ($qq) use ($shopId) {
                    if (is_numeric($shopId)) {
                        $qq->where('shop_id', $shopId);
                    }
                },
                'sheetDesign.products.company',
                'sheetDesign.products.shopProducts' => function ($qq) use ($shopId) {
                    if (is_numeric($shopId)) {
                        $qq->where('shop_id', $shopId);
                    }
                }
            ])
            ->where('is_active', 1);

        $qSearch = str_replace('*', '%', $q);
        $query->where(function ($qq) use ($q, $qSearch) {
            $qq->where('name', 'like', "%{$qSearch}%")
               ->orWhere('sku', 'like', "%{$qSearch}%")
               ->orWhere('id', $q);
        });

        $items = $this->fetchPosProducts($query, $shopId, $this->suggestResultLimit(), true)->map(function ($p) use ($shopId) {
            $shopProduct = $p->shopProducts->first();
            $stockQty = $shopProduct ? (float)$shopProduct->stock_qty : (float)($p->stock_qty ?? 0);
            $sell = (float) PricingService::defaultSellPrice($p);

            $equivalents = [];
            if ($p->sheetDesign && $p->sheetDesign->products) {
                foreach ($p->sheetDesign->products as $eqProduct) {
                    if ($eqProduct->id === $p->id) {
                        continue;
                    }
                    $eqShopProduct = $eqProduct->shopProducts->first();
                    $eqStock = $eqShopProduct ? (float)$eqShopProduct->stock_qty : (float)($eqProduct->stock_qty ?? 0);
                    $eqPrice = (float)($eqProduct->selling_price_default ?? $eqProduct->mrp ?? 0);

                    $eqCode = $p->sheetDesign ? $p->sheetDesign->getCodeForCompany($eqProduct->company_id) : null;

                    $equivalents[] = [
                        'id' => $eqProduct->id,
                        'name' => $eqProduct->name,
                        'company_name' => $eqProduct->company?->name ?? '-',
                        'stock_qty' => round($eqStock, 2),
                        'sell_price' => round($eqPrice, 2),
                        'sheet_code' => $eqCode,
                    ];
                }
            } elseif (!$p->sheet_design_id && $p->category_id && ($p->length_in || $p->width_in)) {
                // Fetch same category + size equivalents (e.g. Shesham 8x4) from other companies
                $eqProducts = Product::where('category_id', $p->category_id)
                    ->where('id', '!=', $p->id)
                    ->where('length_in', $p->length_in)
                    ->where('width_in', $p->width_in)
                    ->where('height_in', $p->height_in)
                    ->where('is_active', 1)
                    ->with(['company', 'shopProducts' => function ($qq) use ($shopId) {
                        if (is_numeric($shopId)) {
                            $qq->where('shop_id', $shopId);
                        }
                    }])
                    ->get();

                foreach ($eqProducts as $eqProduct) {
                    $eqShopProduct = $eqProduct->shopProducts->first();
                    $eqStock = $eqShopProduct ? (float)$eqShopProduct->stock_qty : (float)($eqProduct->stock_qty ?? 0);
                    $eqPrice = (float)($eqProduct->selling_price_default ?? $eqProduct->mrp ?? 0);

                    $equivalents[] = [
                        'id' => $eqProduct->id,
                        'name' => $eqProduct->name,
                        'company_name' => $eqProduct->company?->name ?? '-',
                        'stock_qty' => round($eqStock, 2),
                        'sell_price' => round($eqPrice, 2),
                        'sheet_code' => null,
                    ];
                }
            }

            return [
                'id' => $p->id,
                'name' => (string) ($p->name ?? ''),
                'sku' => $p->sku,
                'company' => $p->company?->name,
                'category' => $p->category?->name,
                'sell' => round($sell, 2),
                'stock_qty' => round($stockQty, 2),
                'length_in' => $p->length_in,
                'width_in' => $p->width_in,
                'height_in' => $p->height_in,
                'equivalents' => $equivalents,
            ];
        })->values();

        return response()->json(['items' => $items]);
    }

    /**
     * AJAX endpoint: returns products / leftovers / companies for a given group.
     * GET /pos/products?g=...&q=...&c=...
     */
    public function products(Request $request)
    {
        $q = trim((string)$request->get('q', ''));
        $g = trim((string)$request->get('g', ''));
        $c = trim((string)$request->get('c', ''));
        $shopId = ShopContext::activeShopId();

        $canSeeCost = \App\Support\Authz::canSeeCost();

        // Companies dropdown: always return the active company list, filtered by the selected group when applicable.
        $companies = [];
        if ($g !== 'leftover') {
            $companies = $this->posCompaniesForGroup($g)
                ->map(fn($co) => ['id' => $co->id, 'name' => $co->name]);
        }

        // Leftovers
        if ($g === 'leftover') {
            $leftovers = LeftoverPiece::with(['product.company', 'product.category'])
                ->where('is_active', 1)
                ->where('qty', '>', 0)
                ->orderByDesc('id')
                ->limit(250)
                ->get()
                ->map(function ($lf) {
                    $p = $lf->product;
                    $w = (float)$lf->width_ft;
                    $l = (float)$lf->length_ft;
                    $isHardwareLeft = abs($l - 4.0) < 0.01;
                    $isFoamLeft = abs($l - 3.0) < 0.01;
                    $cutList = [];
                    if ($isHardwareLeft) { foreach ([2,4,6] as $cw) if ($cw <= $w + 0.0001) $cutList[] = $cw; }
                    elseif ($isFoamLeft) { foreach ([1,2,3,4,5] as $cw) if ($cw <= $w + 0.0001) $cutList[] = $cw; }
                    else { for ($cw=1; $cw<=floor($w); $cw++) $cutList[] = $cw; }

                    return [
                        'id' => $lf->id,
                        'product_name' => $p?->name ?? 'Unknown',
                        'company_name' => $p?->company?->name ?? '-',
                        'category_name' => $p?->category?->name ?? '-',
                        'width' => number_format($w, 2),
                        'length' => number_format($l, 2),
                        'length_raw' => $l,
                        'cut_list' => $cutList,
                    ];
                });

            return response()->json([
                'g' => $g,
                'is_leftover' => true,
                'companies' => [],
                'products' => [],
                'leftovers' => $leftovers,
            ]);
        }

        // Products list
        $productsQuery = Product::query()
            ->select([
                'id',
                'company_id',
                'category_id',
                'sheet_design_id',
                'name',
                'sku',
                'mrp',
                'selling_price_default',
                'stock_qty',
                'max_discount_percent',
                'sheet_full_w',
                'sheet_full_l',
                'width_in',
                'length_in',
                'height_in',
                'is_active',
            ])
            ->with([
                'company',
                'category',
                'shopProducts' => function ($qq) use ($shopId) {
                    if (is_numeric($shopId)) {
                        $qq->where('shop_id', $shopId);
                    }
                },
                'sheetDesign.products.company',
                'sheetDesign.products.shopProducts' => function ($qq) use ($shopId) {
                    if (is_numeric($shopId)) {
                        $qq->where('shop_id', $shopId);
                    }
                }
            ])
            ->where('is_active', 1);

        if ($g !== '') {
            $this->applyCompanyGroupFilter($productsQuery, $g);
        }

        if ($c !== '' && is_numeric($c)) {
            $productsQuery->where('company_id', (int)$c);
        }

        if ($q !== '') {
            $qSearch = str_replace('*', '%', $q);
            $productsQuery->where(function ($qq) use ($q, $qSearch) {
                $qq->where('name', 'like', "%{$qSearch}%")
                    ->orWhere('sku', 'like', "%{$qSearch}%")
                    ->orWhere('id', $q);
            });
        }

        $groups = [
            '' => 'All', 'foam' => 'Foam', 'uncovered_foam' => 'Uncovered Foam', 'hardware' => 'Hardware',
            'fabric' => 'Fabric', 'spring' => 'Spring', 'accessories' => 'Accessories', 'other' => 'Other',
            'leftover' => 'Leftover',
        ];

        $isSheetCandidate = function($p) {
            $w = (float)($p->sheet_full_w ?? 0);
            $l = (float)($p->sheet_full_l ?? 0);
            if ($w > 0 && $l > 0) return true;
            $n = strtolower((string)$p->name);
            $cat = strtolower((string)($p->category?->name ?? ''));
            return str_contains($n,'sheet') || str_contains($cat,'sheet') ||
                   str_contains($n,'slab')  || str_contains($cat,'slab')  ||
                   str_contains($n,'lamination') || str_contains($n,'chipboard') ||
                   str_contains($n,'shesham') || str_contains($n,'lasani') || str_contains($n,'commercial');
        };

        $sheetDefaults = function($p) use ($g, $isSheetCandidate) {
            $w = (float)($p->sheet_full_w ?? 0);
            $l = (float)($p->sheet_full_l ?? 0);
            if ($w > 0 && $l > 0) { $a = max($w, $l); $b = min($w, $l); return [$a, $b]; }
            if ($isSheetCandidate($p)) {
              if ($g === 'hardware') return [8.0, 4.0];
              if ($g === 'foam') return [6.0, 3.0];
            }
            return [0.0, 0.0];
        };

        $cutOptionsForGroup = function($g) {
            if ($g === 'hardware') return [2,4,6];
            if ($g === 'foam') return [1,2,3,4,5];
            return [];
        };

        $products = $this->fetchPosProducts($productsQuery, $shopId, 250, $q !== '')
            ->map(function ($p) use ($isSheetCandidate, $sheetDefaults, $cutOptionsForGroup, $g, $shopId) {
                $shopProduct = $p->shopProducts->first();
                $stockQty = $shopProduct ? (float)$shopProduct->stock_qty : (float)($p->stock_qty ?? 0);
                $sell = (float)($p->selling_price_default ?? $p->mrp ?? 0);
                $maxDiscountPercent = round(max(0, $p->effectiveMaxDiscountPercent()), 2);
                $minSell = round(max($sell * (1 - ($maxDiscountPercent / 100)), 0), 2);
                $sheet = $isSheetCandidate($p);
                [$fullW, $fullL] = $sheetDefaults($p);
                $showSheetButtons = $sheet && $fullW > 0 && $fullL > 0 && in_array($g, ['hardware','foam'], true);
                $cutWidths = $showSheetButtons ? $cutOptionsForGroup($g) : [];

                $equivalents = [];
                if ($p->sheetDesign && $p->sheetDesign->products) {
                    foreach ($p->sheetDesign->products as $eqProduct) {
                        if ($eqProduct->id === $p->id) {
                            continue;
                        }
                        $eqShopProduct = $eqProduct->shopProducts->first();
                        $eqStock = $eqShopProduct ? (float)$eqShopProduct->stock_qty : (float)($eqProduct->stock_qty ?? 0);
                        $eqPrice = (float)($eqProduct->selling_price_default ?? $eqProduct->mrp ?? 0);

                        $eqCode = $p->sheetDesign ? $p->sheetDesign->getCodeForCompany($eqProduct->company_id) : null;

                        $equivalents[] = [
                            'id' => $eqProduct->id,
                            'name' => $eqProduct->name,
                            'company_name' => $eqProduct->company?->name ?? '-',
                            'stock_qty' => round($eqStock, 2),
                            'sell_price' => round($eqPrice, 2),
                            'sheet_code' => $eqCode,
                        ];
                    }
                } elseif (!$p->sheet_design_id && $p->category_id && ($p->length_in || $p->width_in)) {
                    // Fetch same category + size equivalents (e.g. Shesham 8x4) from other companies
                    $eqProducts = Product::where('category_id', $p->category_id)
                        ->where('id', '!=', $p->id)
                        ->where('length_in', $p->length_in)
                        ->where('width_in', $p->width_in)
                        ->where('height_in', $p->height_in)
                        ->where('is_active', 1)
                        ->with(['company', 'shopProducts' => function ($qq) use ($shopId) {
                            if (is_numeric($shopId)) {
                                $qq->where('shop_id', $shopId);
                            }
                        }])
                        ->get();

                    foreach ($eqProducts as $eqProduct) {
                        $eqShopProduct = $eqProduct->shopProducts->first();
                        $eqStock = $eqShopProduct ? (float)$eqShopProduct->stock_qty : (float)($eqProduct->stock_qty ?? 0);
                        $eqPrice = (float)($eqProduct->selling_price_default ?? $eqProduct->mrp ?? 0);

                        $equivalents[] = [
                            'id' => $eqProduct->id,
                            'name' => $eqProduct->name,
                            'company_name' => $eqProduct->company?->name ?? '-',
                            'stock_qty' => round($eqStock, 2),
                            'sell_price' => round($eqPrice, 2),
                            'sheet_code' => null,
                        ];
                    }
                }

                return [
                    'id' => $p->id,
                    'name' => $p->name,
                    'company_name' => $p->company?->name ?? '-',
                    'category_name' => $p->category?->name ?? '-',
                    'sell' => number_format($sell, 2),
                    'sell_raw' => round($sell, 2),
                    'min_sell' => $minSell,
                    'max_discount_percent' => $maxDiscountPercent,
                    'is_sheet' => $showSheetButtons,
                    'full_w' => $fullW,
                    'full_l' => $fullL,
                    'cut_widths' => $cutWidths,
                    'stock_qty' => round($stockQty, 2),
                    'out_of_stock' => $stockQty <= 0,
                    'length_in' => $p->length_in,
                    'width_in' => $p->width_in,
                    'height_in' => $p->height_in,
                    'equivalents' => $equivalents,
                ];
            });

        return response()->json([
            'g' => $g,
            'is_leftover' => false,
            'companies' => $companies,
            'products' => $products,
            'leftovers' => [],
        ]);
    }

    private function suggestResultLimit(): int
    {
        return 250;
    }

    private function posCompaniesForGroup(string $groupKey = '')
    {
        $query = Company::query()->where('is_active', 1)->orderBy('name');

        if ($groupKey !== '' && $groupKey !== 'leftover') {
            $this->applyCompanyGroupFilterToQuery($query, $groupKey);
        }

        return $query->get();
    }

    private function applyCompanyGroupFilter($query, string $groupKey): void
    {
        $query->whereHas('company', function ($qq) use ($groupKey) {
            $this->applyCompanyGroupFilterToQuery($qq, $groupKey);
        });
    }

    private function applyCompanyGroupFilterToQuery($query, string $groupKey): void
    {
        if ($groupKey === '') {
            return;
        }

        if ($groupKey === 'uncovered_foam') {
            $query->where(function ($sub) {
                $sub->where('group_key', 'uncovered_foam')
                    ->orWhereRaw('LOWER(name) LIKE ?', ['%uncovered%'])
                    ->orWhereRaw('LOWER(name) LIKE ?', ['%uncoverd%']);
            });

            return;
        }

        if ($groupKey === 'foam') {
            $query->where('group_key', 'foam')
                ->whereNot(function ($sub) {
                    $sub->whereRaw('LOWER(name) LIKE ?', ['%uncovered%'])
                        ->orWhereRaw('LOWER(name) LIKE ?', ['%uncoverd%']);
                });

            return;
        }

        $query->where('group_key', $groupKey);
    }

    private function fetchPosProducts($baseQuery, int|string|null $shopId, int $limit, bool $skipStockFilter = false)
    {
        $query = clone $baseQuery;

        if (is_numeric($shopId) && !$skipStockFilter) {
            $this->applyPosStockFilter($query, $shopId);
        }

        return $query
            ->get()
            ->sort(function ($a, $b) {
                $isSlabA = false;
                if ($a->category) {
                    $catLower = strtolower($a->category->name);
                    if (str_contains($catLower, 'slab') || str_contains($catLower, 'sheet')) {
                        $isSlabA = true;
                    }
                }

                $ha = (float)($a->height_in ?? 0);
                $hb = (float)($b->height_in ?? 0);
                if ($isSlabA) {
                    if ($ha != $hb) return $hb <=> $ha; // descending height for slab sheets
                } else {
                    if ($ha != $hb) return $ha <=> $hb; // ascending height for standard
                }

                $la = (float)($a->length_in ?? 0);
                $lb = (float)($b->length_in ?? 0);
                if ($la != $lb) return $la <=> $lb; // ascending length

                $wa = (float)($a->width_in ?? 0);
                $wb = (float)($b->width_in ?? 0);
                if ($wa != $wb) return $wa <=> $wb; // ascending width

                return strcasecmp($a->name, $b->name);
            })
            ->values()
            ->take($limit);
    }

    private function sizeSortKey($product): string
    {
        $height = (float)($product->height_in ?? 0);
        $width = (float)($product->width_in ?? 0);
        $length = (float)($product->length_in ?? 0);
        $name = strtolower((string)($product->name ?? ''));

        return sprintf('%08.2f-%08.2f-%08.2f-%s', $height, $length, $width, $name);
    }

    private function applyPosStockFilter($query, int|string|null $shopId): void
    {
        if (!is_numeric($shopId)) {
            return;
        }

        $shopId = (int) $shopId;

        $query->where(function ($stockQuery) use ($shopId) {
            $stockQuery
                ->whereHas('shopProducts', function ($shopProductsQuery) use ($shopId) {
                    $shopProductsQuery
                        ->where('shop_id', $shopId)
                        ->where('stock_qty', '>', 0);
                })
                ->orWhere(function ($fallbackQuery) use ($shopId) {
                    $fallbackQuery
                        ->whereDoesntHave('shopProducts', function ($shopProductsQuery) use ($shopId) {
                            $shopProductsQuery->where('shop_id', $shopId);
                        })
                        ->where('stock_qty', '>', 0);
                });
        });
    }

    private function posAjaxCartResponse(): array
    {
        $cart = session()->get('mt_cart', []);
        [$rows, $summary] = $this->recalcCart($cart);

        $fixedCart = [];
        foreach ($rows as $r) {
            $fixedCart[$r['row_id']] = $r['raw_cart_item'];
        }

        session()->put('mt_cart', $fixedCart);
        session()->put('mt_cart_overall_discount', $summary['overall_discount']);

        return [
            'ok' => true,
            'rows' => $rows,
            'summary' => $summary,
            'message' => 'Cart updated',
        ];
    }

    private function createCheckoutBackorders(array $cartRows, int $shopId, int $saleId): array
    {
        $requestedByProduct = [];

        foreach ($cartRows as $row) {
            $unitType = (string)($row['unit_type'] ?? 'normal');
            if (in_array($unitType, ['leftover_full', 'leftover_cut'], true)) {
                continue;
            }

            $productId = (int)($row['product_id'] ?? 0);
            $qty = (float)($row['qty'] ?? 0);
            if ($productId <= 0 || $qty <= 0) {
                continue;
            }

            if (!isset($requestedByProduct[$productId])) {
                $requestedByProduct[$productId] = 0.0;
            }
            $requestedByProduct[$productId] += $qty;
        }

        if (empty($requestedByProduct)) {
            return [];
        }

        $ordersByCompany = [];

        foreach ($requestedByProduct as $productId => $totalQty) {
            $product = Product::with('company')->find($productId);
            if (!$product || !$product->company_id) {
                continue;
            }

            $shopProduct = ShopProduct::where('shop_id', $shopId)
                ->where('product_id', $productId)
                ->lockForUpdate()
                ->first();

            $availableQty = $shopProduct
                ? max(0.0, (float)$shopProduct->stock_qty)
                : max(0.0, (float)$product->stock_qty);

            $shortageQty = round(max(0.0, $totalQty - $availableQty), 4);
            if ($shortageQty <= 0) {
                continue;
            }

            $companyId = (int)$product->company_id;
            $unitCost = max(0.0, (float) PricingService::purchasePrice($product));
            $lineTotal = round($shortageQty * $unitCost, 2);

            if (!isset($ordersByCompany[$companyId])) {
                $ordersByCompany[$companyId] = [
                    'company_id' => $companyId,
                    'items' => [],
                    'goods_total' => 0.0,
                ];
            }

            $ordersByCompany[$companyId]['items'][] = [
                'product_id' => $productId,
                'qty' => $shortageQty,
                'unit_cost' => $unitCost,
                'line_total' => $lineTotal,
            ];
            $ordersByCompany[$companyId]['goods_total'] += $lineTotal;
        }

        if (empty($ordersByCompany)) {
            return [];
        }

        $createdOrderIds = [];
        foreach ($ordersByCompany as $companyOrderData) {
            $order = CompanyOrder::create([
                'company_id' => $companyOrderData['company_id'],
                'order_date' => now()->toDateString(),
                'status' => 'open',
                'goods_total' => round($companyOrderData['goods_total'], 2),
                'note' => 'Auto-generated backorder from sale #' . $saleId,
                'created_by' => auth()->id(),
            ]);

            foreach ($companyOrderData['items'] as $item) {
                CompanyOrderItem::create([
                    'company_order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'qty' => $item['qty'],
                    'unit_cost' => $item['unit_cost'],
                    'line_total' => $item['line_total'],
                ]);
            }

            CompanyLedgerEntry::create([
                'company_id' => $companyOrderData['company_id'],
                'entry_date' => $order->order_date,
                'entry_type' => 'order',
                'direction' => 'debit',
                'amount' => round($companyOrderData['goods_total'], 2),
                'description' => 'Auto backorder for sale #' . $saleId,
                'ref_type' => 'company_order',
                'ref_id' => $order->id,
                'user_id' => auth()->id(),
            ]);

            $createdOrderIds[] = $order->id;
        }

        return $createdOrderIds;
    }

    /**
     * Add normal item OR sheet full/cut (backward compatible with your blade that posts mode=sheet_full/sheet_cut).
     */
    public function add(Request $request)
    {
        $data = $request->validate([
            'product_id' => ['required', 'integer'],
            'mode' => ['nullable', 'string'], // sheet_full / sheet_cut
            'cut_w' => ['nullable', 'numeric', 'min:0.01'],
            'cut_l' => ['nullable', 'numeric', 'min:0.01'],
            'selling_price' => ['nullable', 'numeric', 'min:0'],
        ]);

        $mode = (string)($data['mode'] ?? '');

        // If sheet mode is requested, handle as sheet logic
        if (in_array($mode, ['sheet_full', 'sheet_cut'], true)) {
            return $this->addSheetViaAddRoute($request, $mode);
        }

        // Normal product add
        $p = Product::with(['company', 'category'])->findOrFail((int)$data['product_id']);
        $displayName = (string)$p->name;

        $cart = session()->get('mt_cart', []);

        // unique per product
        $rowId = 'p' . $p->id;

        $sell = (float)PricingService::defaultSellPrice($p);
        $requestedSell = array_key_exists('selling_price', $data)
            ? (float)$data['selling_price']
            : $sell;
        $linePricing = $this->normalizeCartLinePricing([
            'price' => $requestedSell,
            'discount_source' => 'price',
        ], $p, 'normal', round($sell, 2));

        if (!isset($cart[$rowId])) {
            $cart[$rowId] = [
                'row_id' => $rowId,
                'product_id' => $p->id,
                'name' => $displayName,
                'qty' => 1, // default 1
                'price' => $linePricing['price'],
                'base_price' => $linePricing['base_price'],
                'discount_percent' => $linePricing['discount_percent'],
                'discount_source' => abs($linePricing['price'] - $linePricing['base_price']) > 0.009 ? 'price' : 'none',
                'unit_type' => 'normal',
            ];
        } else {
            $cart[$rowId]['qty'] = round(((float)($cart[$rowId]['qty'] ?? 0)) + 1, 2);
        }

        session()->put('mt_cart', $cart);

        if ($request->expectsJson()) {
            return response()->json($this->posAjaxCartResponse());
        }

        return redirect()->route('mt.pos.index', $request->only(['g', 'q', 'c']));
    }

    /**
     * Optional separate endpoint if you want route('/pos/add-cut') like earlier.
     * You can use this from blade too.
     */
    public function addCut(Request $request)
    {
        $data = $request->validate([
            'product_id' => ['required', 'integer'],
            'full_w_ft' => ['required', 'numeric', 'min:0.01'],
            'full_l_ft' => ['required', 'numeric', 'min:0.01'],
            'cut_w_ft' => ['required', 'numeric', 'min:0.01'],
            'cut_l_ft' => ['required', 'numeric', 'min:0.01'],
            'selling_price' => ['nullable', 'numeric', 'min:0'],
        ]);

        $p = Product::with(['company', 'category'])->findOrFail((int)$data['product_id']);
        $displayName = (string)$p->name;

        $fullW = (float)$data['full_w_ft'];
        $fullL = (float)$data['full_l_ft'];
        $cutW  = (float)$data['cut_w_ft'];
        $cutL  = (float)$data['cut_l_ft'];

        // cap
        if ($cutW > $fullW) $cutW = $fullW;
        if ($cutL > $fullL) $cutL = $fullL;

        $fullPrice = (float)PricingService::defaultSellPrice($p);
        $requestedFullPrice = array_key_exists('selling_price', $data)
            ? (float)$data['selling_price']
            : $fullPrice;
        $fullLinePricing = $this->normalizeCartLinePricing([
            'price' => $requestedFullPrice,
            'discount_source' => 'price',
        ], $p, 'sheet_full', round($fullPrice, 2));

        $fullArea = $fullW * $fullL;
        $cutArea = $cutW * $cutL;

        $ratio = ($fullArea > 0) ? ($cutArea / $fullArea) : 1;
        $ratio = max(0, min(1, $ratio));

        $unitPrice = round($fullLinePricing['price'] * $ratio, 2);
        $baseUnitPrice = round($fullLinePricing['base_price'] * $ratio, 2);

        $cart = session()->get('mt_cart', []);

        // separate row per cut size
        $rowId = 'p' . $p->id . ':cut:' . $cutW . 'x' . $cutL;

        // nicer label in cart
        if (abs($cutW - $fullW) < 0.0001 && abs($cutL - $fullL) < 0.0001) {
            $displayName = $displayName . ' (Full ' . $fullW . '×' . $fullL . ')';
        } else {
            $displayName = $displayName . ' (Cut ' . $cutW . '×' . $cutL . ')';
        }

        if (!isset($cart[$rowId])) {
            $cart[$rowId] = [
                'row_id' => $rowId,
                'product_id' => $p->id,
                'name' => $displayName,
                'qty' => 1,
                'price' => $unitPrice,
                'base_price' => $baseUnitPrice,
                'discount_percent' => 0,
                'discount_source' => abs($unitPrice - $baseUnitPrice) > 0.009 ? 'price' : 'none',

                'unit_type' => 'sheet_cut',
                'sheet_full_w' => $fullW,
                'sheet_full_l' => $fullL,
                'cut_w' => $cutW,
                'cut_l' => $cutL,
                'ratio' => $ratio,
                'full_price' => $fullPrice,
            ];
        } else {
            $cart[$rowId]['qty'] = (int)($cart[$rowId]['qty'] ?? 0) + 1;
        }

        session()->put('mt_cart', $cart);

        if ($request->expectsJson()) {
            return response()->json($this->posAjaxCartResponse());
        }

        return redirect()->route('mt.pos.index', $request->only(['g', 'q', 'c']));
    }

    
    // ----------------------------
    // Leftover add to cart
    // ----------------------------

    public function addLeftoverFull(Request $request)
    {
        $data = $request->validate([
            'leftover_id' => ['required','integer'],
        ]);

        $lf = LeftoverPiece::with(['product.company','product.category'])->findOrFail((int)$data['leftover_id']);
        if (!(float)$lf->qty > 0) {
            return redirect()->route('mt.pos.index', ['g' => 'leftover'])->with('error', 'Leftover not available.');
        }

        $p = $lf->product;
        if (!$p) {
            return redirect()->route('mt.pos.index', ['g' => 'leftover'])->with('error', 'Product not found.');
        }

        [$fullW, $fullL] = $this->inferFullSheetDims($p, (float)$lf->length_ft);
        $fullArea = max(0.0001, $fullW * $fullL);
        $pieceArea = max(0.0001, (float)$lf->width_ft * (float)$lf->length_ft);
        $ratio = max(0.0, min(1.0, $pieceArea / $fullArea));

        $fullPrice = (float)PricingService::defaultSellPrice($p);
        $unitPrice = round($fullPrice * $ratio, 2);

        $cart = session()->get('mt_cart', []);
        $rowId = 'lf' . $lf->id . ':full';

        $cart[$rowId] = [
            'row_id' => $rowId,
            'product_id' => $p->id,
            'name' => $p->name . ' (Leftover)',
            'qty' => 1,
            'price' => $unitPrice,
            'base_price' => $unitPrice,
            'discount_percent' => 0,
            'discount_source' => 'none',

            'unit_type' => 'leftover_full',
            'leftover_id' => $lf->id,
            'leftover_w' => (float)$lf->width_ft,
            'leftover_l' => (float)$lf->length_ft,
            'full_w' => $fullW,
            'full_l' => $fullL,
            'ratio' => $ratio,
        ];

        session()->put('mt_cart', $cart);

        if ($request->expectsJson()) {
            return response()->json($this->posAjaxCartResponse());
        }

        return redirect()->route('mt.pos.index', ['g' => 'leftover']);
    }

    public function addLeftoverCut(Request $request)
    {
        $data = $request->validate([
            'leftover_id' => ['required','integer'],
            'cut_w_ft' => ['required','numeric','min:0.01'],
        ]);

        $lf = LeftoverPiece::with(['product.company','product.category'])->findOrFail((int)$data['leftover_id']);
        if (!(float)$lf->qty > 0) {
            return redirect()->route('mt.pos.index', ['g' => 'leftover'])->with('error', 'Leftover not available.');
        }

        $p = $lf->product;
        if (!$p) {
            return redirect()->route('mt.pos.index', ['g' => 'leftover'])->with('error', 'Product not found.');
        }

        $cutW = (float)$data['cut_w_ft'];
        $cutL = (float)$lf->length_ft;

        if ($cutW > (float)$lf->width_ft) {
            $cutW = (float)$lf->width_ft;
        }

        [$fullW, $fullL] = $this->inferFullSheetDims($p, (float)$lf->length_ft);
        $fullArea = max(0.0001, $fullW * $fullL);
        $pieceArea = max(0.0001, $cutW * $cutL);
        $ratio = max(0.0, min(1.0, $pieceArea / $fullArea));

        $fullPrice = (float)PricingService::defaultSellPrice($p);
        $unitPrice = round($fullPrice * $ratio, 2);

        $cart = session()->get('mt_cart', []);
        $rowId = 'lf' . $lf->id . ':cut:' . $cutW . 'x' . $cutL;

        $cart[$rowId] = [
            'row_id' => $rowId,
            'product_id' => $p->id,
            'name' => $p->name . ' (Leftover Cut)',
            'qty' => 1,
            'price' => $unitPrice,
            'base_price' => $unitPrice,
            'discount_percent' => 0,
            'discount_source' => 'none',

            'unit_type' => 'leftover_cut',
            'leftover_id' => $lf->id,
            'leftover_w' => (float)$lf->width_ft,
            'leftover_l' => (float)$lf->length_ft,
            'full_w' => $fullW,
            'full_l' => $fullL,
            'cut_w' => $cutW,
            'cut_l' => $cutL,
            'ratio' => $ratio,
        ];

        session()->put('mt_cart', $cart);

        if ($request->expectsJson()) {
            return response()->json($this->posAjaxCartResponse());
        }

        return redirect()->route('mt.pos.index', ['g' => 'leftover']);
    }

    private function inferFullSheetDims(Product $p, float $lenHint): array
    {
        $fullW = (float)($p->sheet_full_w ?? 0);
        $fullL = (float)($p->sheet_full_l ?? 0);

        if ($fullW > 0 && $fullL > 0) {
            return [$fullW, $fullL];
        }

        // infer from typical sizes
        if (abs($lenHint - 4.0) < 0.02) return [8.0, 4.0];
        if (abs($lenHint - 3.0) < 0.02) return [6.0, 3.0];

        return [8.0, 4.0];
    }

public function remove(Request $request)
    {
        $data = $request->validate([
            'row_id' => ['required', 'string'],
        ]);

        $cart = session()->get('mt_cart', []);
        unset($cart[$data['row_id']]); // remove only one item

        session()->put('mt_cart', $cart);

        if ($request->expectsJson()) {
            return response()->json($this->posAjaxCartResponse());
        }

        return redirect()->route('mt.pos.index', $request->only(['g', 'q', 'c']));
    }

    public function clear(Request $request)
    {
        session()->forget('mt_cart');
        session()->forget('mt_cart_overall_discount');
        session()->forget('mt_cart_overall_discount');

        if ($request->expectsJson()) {
            return response()->json([
                'ok' => true,
                'rows' => [],
                'summary' => $this->emptyCartSummary(),
                'message' => 'Cart cleared.',
            ]);
        }

        return redirect()->route('mt.pos.index', $request->only(['g', 'q', 'c']))
            ->with('success', 'Cart cleared.');
    }

    /**
     * Live update qty/price (realtime)
     * Route name: mt.pos.update_item
     */
    public function updateItem(Request $request)
    {
        $data = $request->validate([
            'row_id' => ['required', 'string'],
            'qty' => ['nullable'],
            'price' => ['nullable'],
            'discount_percent' => ['nullable'],
            'discount_source' => ['nullable', 'string', Rule::in(['none', 'price', 'percent'])],
        ]);

        $rowId = (string)$data['row_id'];

        $cart = session()->get('mt_cart', []);
        if (!isset($cart[$rowId])) {
            return response()->json(['ok' => false, 'msg' => 'Row not found']);
        }

        $unitType = (string)($cart[$rowId]['unit_type'] ?? 'normal');

        // leftover items should always be 1 per click (they represent a specific rectangle)
        if (str_starts_with($unitType, 'leftover_')) {
            $cart[$rowId]['qty'] = 1;
        }

        // Qty
        if (isset($data['qty'])) {
            if (str_starts_with($unitType, 'leftover_')) {
                $cart[$rowId]['qty'] = 1;
            } else
            if (in_array($unitType, ['sheet_full', 'sheet_cut'], true)) {
                $qty = (int)floor((float)$data['qty']);
                if ($qty < 1) $qty = 1;
                $cart[$rowId]['qty'] = $qty;
            } else {
                $qty = (float)$data['qty'];
                if ($qty < 0.01) $qty = 0.01;
                $cart[$rowId]['qty'] = round($qty, 2);
            }
        }

        // Price
        if (isset($data['price'])) {
            $price = (float)$data['price'];
            if ($price < 0) $price = 0;
            $cart[$rowId]['price'] = round($price, 2);
            if (!isset($data['discount_source'])) {
                $cart[$rowId]['discount_source'] = 'price';
            }
        }

        if (array_key_exists('discount_percent', $data)) {
            $discountPercent = (float)$data['discount_percent'];
            if ($discountPercent < 0) $discountPercent = 0;
            $cart[$rowId]['discount_percent'] = round($discountPercent, 2);
            if (!isset($data['discount_source'])) {
                $cart[$rowId]['discount_source'] = 'percent';
            }
        }

        if (isset($data['discount_source'])) {
            $cart[$rowId]['discount_source'] = (string)$data['discount_source'];
        }

        session()->put('mt_cart', $cart);

        [$rows, $summary] = $this->recalcCart($cart);

        // keep only valid normalized items
        $fixedCart = [];
        $updatedRow = null;
        foreach ($rows as $r) {
            $fixedCart[$r['row_id']] = $r['raw_cart_item'];
            if ($r['row_id'] === $rowId) {
                $updatedRow = $r;
            }
        }
        session()->put('mt_cart', $fixedCart);
        session()->put('mt_cart_overall_discount', $summary['overall_discount']);

        $canSeeCost = \App\Support\Authz::canSeeCost();

        $payload = [
            'ok' => true,
            'total' => $summary['total'],
            'summary' => $summary,
            'row' => $updatedRow ? [
                'row_id' => $updatedRow['row_id'],
                'qty' => $updatedRow['qty'],
                'price' => $updatedRow['price'],
                'discount_percent' => $updatedRow['discount_percent'],
                'discount_source' => $updatedRow['discount_source'],
                'discount_amount' => $updatedRow['discount_amount'],
                'line_total' => $updatedRow['line_total'],
                'line_profit' => $updatedRow['line_profit'],
            ] : null,
        ];
        if ($canSeeCost) {
            $payload['profit'] = $summary['profit'];
        }

        return response()->json($payload);
    }

    public function updateOverallDiscount(Request $request)
    {
        $data = $request->validate([
            'overall_discount' => ['nullable', 'numeric', 'min:0'],
        ]);

        $cart = session()->get('mt_cart', []);
        [$rows, $summary] = $this->recalcCart($cart, (float)($data['overall_discount'] ?? 0));

        $fixedCart = [];
        foreach ($rows as $r) {
            $fixedCart[$r['row_id']] = $r['raw_cart_item'];
        }

        session()->put('mt_cart', $fixedCart);
        session()->put('mt_cart_overall_discount', $summary['overall_discount']);

        $payload = [
            'ok' => true,
            'total' => $summary['total'],
            'summary' => $summary,
            'overall_discount' => $summary['overall_discount'],
        ];

        if (\App\Support\Authz::canSeeCost()) {
            $payload['profit'] = $summary['profit'];
        }

        return response()->json($payload);
    }

    /**
     * Checkout (UDHAR included). Uses your current cart prices (editable).
     * Also fixes customer insert for BOTH schemas:
     * - customers.phone (required)
     * - OR customers.phone_primary/phone_alt_1/phone_alt_2
     */
    public function checkout(Request $request)
    {
        $isAjax = $request->expectsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest';

        $data = $request->validate([
            'customer_name' => ['nullable', 'string', 'max:120'],
            'customer_phone' => ['nullable', 'string', 'max:30'],
            'customer_phone2' => ['nullable', 'string', 'max:30'],
            'customer_phone3' => ['nullable', 'string', 'max:30'],
            'customer_address' => ['nullable', 'string', 'max:255'],
            'customer_cnic' => ['nullable', 'string', 'max:30'],

            'received_cash' => ['nullable', 'numeric', 'min:0'],
            'overall_discount' => ['nullable', 'numeric', 'min:0'],
            'payment_method' => ['nullable', 'string', Rule::in(PaymentMethod::keys())],
            'note' => ['nullable', 'string', 'max:255'],
        ]);

        $cart = session()->get('mt_cart', []);
        [$cartRows, $cartSummary] = $this->recalcCart($cart, (float)($data['overall_discount'] ?? session()->get('mt_cart_overall_discount', 0)));

        if (count($cartRows) === 0) {
            $emptyMsg = 'Cart is empty.';
            if ($isAjax) {
                return response()->json(['success' => false, 'error' => $emptyMsg], 400);
            }
            return redirect()->route('mt.pos.index')->with('error', $emptyMsg);
        }

        session()->put('mt_cart_overall_discount', $cartSummary['overall_discount']);

        $total = (float)$cartSummary['total'];
        $profitTotal = (float)$cartSummary['profit'];
        $subtotalAmount = (float)$cartSummary['subtotal'];
        $itemDiscountTotal = (float)$cartSummary['line_discount_total'];
        $overallDiscountAmount = (float)$cartSummary['overall_discount'];

        $received = max(0, (float)($data['received_cash'] ?? 0));
        $paidApplied = round(min($received, $total), 2);
        $changeReturned = round(max($received - $paidApplied, 0), 2);
        $balance = round(max($total - $paidApplied, 0), 2);
        $paymentMethod = $paidApplied > 0
            ? (PaymentMethod::normalize($data['payment_method'] ?? null) ?? PaymentMethod::HARD_CASH)
            : null;

        $status = ($balance <= 0 ? 'paid' : ($paidApplied > 0 ? 'partial' : 'unpaid'));
        $saleType = ($balance > 0 ? 'udhar' : 'cash');

        // UDHAR requirement
        if ($saleType === 'udhar') {
            if (trim((string)($data['customer_name'] ?? '')) === '' ||
                trim((string)($data['customer_phone'] ?? '')) === '' ||
                trim((string)($data['customer_address'] ?? '')) === '') {
                $udharMsg = 'For UDHAR: Name, Contact #1 and Address are required.';
                if ($isAjax) {
                    return response()->json(['success' => false, 'error' => $udharMsg], 400);
                }
                return redirect()->route('mt.pos.index')->with('error', $udharMsg);
            }
        }

        $saleId = null;
        $backorderOrderIds = [];

        DB::transaction(function () use (&$saleId, &$backorderOrderIds, $data, $cartRows, $total, $paidApplied, $received, $changeReturned, $balance, $status, $saleType, $profitTotal, $paymentMethod, $subtotalAmount, $itemDiscountTotal, $overallDiscountAmount) {

            $phone1 = trim((string)($data['customer_phone'] ?? ''));
            $phone2 = trim((string)($data['customer_phone2'] ?? ''));
            $phone3 = trim((string)($data['customer_phone3'] ?? ''));

            // ---- Customer create/find (support both schemas) ----
            $cust = null;

            if (class_exists(Customer::class)) {
                // try helper if exists
                if ($phone1 !== '' && method_exists(Customer::class, 'findByAnyPhone')) {
                    $cust = Customer::findByAnyPhone($phone1);
                }
                if (!$cust && $phone2 !== '' && method_exists(Customer::class, 'findByAnyPhone')) {
                    $cust = Customer::findByAnyPhone($phone2);
                }
                if (!$cust && $phone3 !== '' && method_exists(Customer::class, 'findByAnyPhone')) {
                    $cust = Customer::findByAnyPhone($phone3);
                }

                // Decide customer table columns
                $hasPhone = Schema::hasColumn('customers', 'phone'); // your DB error shows this exists+required
                $hasPrimary = Schema::hasColumn('customers', 'phone_primary');

                if (!$cust && ($phone1 !== '' || $phone2 !== '' || $phone3 !== '')) {

                    if ($hasPhone) {
                        // your table wants `phone`
                        $cust = Customer::create([
                            'name' => $data['customer_name'] ?? null,
                            'phone' => $phone1 ?: ($phone2 ?: $phone3), // ✅ FIX
                            'address' => $data['customer_address'] ?? null,
                            'cnic' => $data['customer_cnic'] ?? null,
                        ]);
                    } elseif ($hasPrimary) {
                        // phone_primary schema
                        $cust = Customer::create([
                            'name' => $data['customer_name'] ?? null,
                            'phone_primary' => $phone1 ?: ($phone2 ?: $phone3),
                            'phone_alt_1' => $phone1 && $phone2 ? $phone2 : null,
                            'phone_alt_2' => $phone3 ?: null,
                            'address' => $data['customer_address'] ?? null,
                            'cnic' => $data['customer_cnic'] ?? null,
                        ]);
                    }
                }
            }

            // ---- Create sale ----
            $sale = Sale::create([
                'shop_id' => ShopContext::requireSingleShopId(),
                'user_id' => auth()->id(),
                'customer_id' => $cust?->id,

                'customer_name' => $data['customer_name'] ?? ($cust?->name),
                'customer_phone' => $phone1 ?: ($cust?->phone_primary ?? $cust?->phone ?? null),
                'customer_phone2' => $phone2 ?: ($cust?->phone_alt_1 ?? null),
                'customer_phone3' => $phone3 ?: ($cust?->phone_alt_2 ?? null),
                'customer_address' => $data['customer_address'] ?? ($cust?->address),
                'customer_cnic' => $data['customer_cnic'] ?? ($cust?->cnic),

                'total_amount' => $total,
                'subtotal_amount' => $subtotalAmount,
                'item_discount_total' => $itemDiscountTotal,
                'overall_discount_amount' => $overallDiscountAmount,
                'paid_amount' => $paidApplied,
                'received_amount' => $received,
                'change_returned' => $changeReturned,
                'balance_amount' => $balance,

                'sale_type' => $saleType,
                'status' => $status,
                'note' => $data['note'] ?? null,

                'profit_total' => round($profitTotal, 2),
                'profit_realized' => ($total > 0 ? round($profitTotal * ($paidApplied / $total), 2) : 0),
            ]);

            $saleId = $sale->id;
            $backorderOrderIds = $this->createCheckoutBackorders($cartRows, ShopContext::requireSingleShopId(), $saleId);

            if ($paidApplied > 0) {
                Payment::create([
                    'sale_id' => $sale->id,
                    'user_id' => auth()->id(),
                    'amount' => $paidApplied,
                    'method' => $paymentMethod,
                    'note' => null,
                ]);
            }

            // ---- Save items + decrement stock correctly (cut decrements partial sheet) ----
            foreach ($cartRows as $r) {

                $productId = (int)$r['product_id'];
                $qty = (float)$r['qty'];
                $sell = (float)$r['price'];

                // purchase_full is full-sheet / full-unit cost, purchase_effective is already scaled for cut
                $purchaseEffective = (float)($r['purchase_effective'] ?? $r['purchase_price'] ?? 0);

                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $productId,
                    'product_name' => $r['name'],
                    'qty' => $qty,
                    'price' => $sell,
                    'base_price' => (float)($r['base_price'] ?? $sell),
                    'discount_percent' => (float)($r['discount_percent'] ?? 0),
                    'discount_amount' => (float)($r['discount_amount'] ?? 0),
                    'base_line_total' => (float)($r['base_line_total'] ?? round($qty * $sell, 2)),
                    'purchase_price' => $purchaseEffective,
                    'line_total' => (float)($r['line_total'] ?? round($qty * $sell, 2)),
                ]);            // Stock / leftover handling
                $unitType = (string)($r['unit_type'] ?? 'normal');

                // 1) Selling from leftover stock (no product stock decrement)
                if ($unitType === 'leftover_full' || $unitType === 'leftover_cut') {
                    $leftoverId = (int)($r['leftover_id'] ?? 0);
                    $cutW = (float)($r['cut_w'] ?? 0);
                    $cutL = (float)($r['cut_l'] ?? 0);

                    for ($i = 0; $i < (int)$qty; $i++) {
                        $lf = LeftoverPiece::where('id', $leftoverId)->lockForUpdate()->first();
                        if (!$lf || (float)$lf->qty <= 0) {
                            continue;
                        }

                        // consume 1 leftover rectangle
                        $lf->qty = round(((float)$lf->qty) - 1, 4);
                        if ($lf->qty <= 0.0001) {
                            $lf->qty = 0;
                            $lf->is_active = 0;
                        }
                        $lf->save();

                        if ($unitType === 'leftover_cut') {
                            $remW = round(((float)$lf->width_ft) - $cutW, 4);
                            $remL = (float)$lf->length_ft;
                            if ($remW > 0.01) {
                                $existingLf = LeftoverPiece::where('product_id', $productId)
                                    ->where('is_active', 1)
                                    ->where('width_ft', $remW)
                                    ->where('length_ft', $remL)
                                    ->first();

                                if ($existingLf) {
                                    $existingLf->increment('qty', 1);
                                } else {
                                    LeftoverPiece::create([
                                        'product_id' => $productId,
                                        'width_ft' => $remW,
                                        'length_ft' => $remL,
                                        'qty' => 1,
                                        'is_active' => 1,
                                        'note' => 'Created from leftover cut',
                                    ]);
                                }
                            }
                        }
                    }

                    continue;
                }

                // 2) New sheet cut: consumes 1 full sheet and creates leftover
                if ($unitType === 'sheet_cut') {
                    $fullW = (float)($r['sheet_full_w'] ?? 0);
                    $fullL = (float)($r['sheet_full_l'] ?? 0);
                    $cutW = (float)($r['cut_w'] ?? 0);
                    $cutL = (float)($r['cut_l'] ?? 0);

                    if ($fullW <= 0 || $fullL <= 0) {
                        // fallback defaults
                        $fullW = 8.0;
                        $fullL = 4.0;
                    }

                    // Each qty means one fresh sheet is opened
                    if ($qty > 0) {
                        Inventory::decrement(ShopContext::requireSingleShopId(), $productId, (float)$qty);
                    }

                    // create leftovers per cut
                    for ($i = 0; $i < (int)$qty; $i++) {
                        // We assume the cut happens along width for our POS buttons (1x4 from 8x4, 1x3 from 6x3)
                        $remW = round($fullW - $cutW, 4);
                        $remL = $fullL;

                        // If cut takes full width but shorter length, handle length leftover
                        if ($remW <= 0.01 && $cutW >= $fullW - 0.01) {
                            $remW = $fullW;
                            $remL = round($fullL - $cutL, 4);
                        }

                        if ($remW > 0.01 && $remL > 0.01) {
                            $existingLf = LeftoverPiece::where('product_id', $productId)
                                ->where('is_active', 1)
                                ->where('width_ft', $remW)
                                ->where('length_ft', $remL)
                                ->first();

                            if ($existingLf) {
                                $existingLf->increment('qty', 1);
                            } else {
                                LeftoverPiece::create([
                                    'product_id' => $productId,
                                    'width_ft' => $remW,
                                    'length_ft' => $remL,
                                    'qty' => 1,
                                    'is_active' => 1,
                                    'note' => 'Created from new sheet cut',
                                ]);
                            }
                        }
                    }

                    continue;
                }

                // 3) Normal / full sheet items
                if ($qty > 0) {
                    Inventory::decrement(ShopContext::requireSingleShopId(), $productId, (float)$qty);
                }

            }
        });

        // Store last sale ID for reprint functionality
        session()->put('mt_last_sale_id', $saleId);

        // clear cart immediately
        session()->forget('mt_cart');

        // generate a PDF backup for the invoice every time a sale is created
        $sale = null;
        try {
            $sale = Sale::with(['items.product','shop','payments'])->find($saleId);
            if ($sale) {
                $pdf = Pdf::loadView('mt.sales.invoice_pdf', ['sale' => $sale])->setPaper('a4');
                $filename = 'invoices/invoice_' . $sale->id . '.pdf';
                Storage::disk('public')->put($filename, $pdf->output());
            }
        } catch (\Throwable $e) {
            // ignore backup errors, sales have already been recorded
            logger()->error('Failed to create invoice PDF backup: ' . $e->getMessage());
        }

        $action = $request->input('checkout_action', 'save');
        $isAjax = $request->expectsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest';

        // Prepare response data
        $response = [
            'success' => true,
            'message' => "Sale completed. Bill #{$saleId}.",
            'sale_id' => $saleId,
            'sale_url' => route('mt.sales.show', $saleId),
            'should_print' => false,
            'should_sms' => false,
            'should_whatsapp' => false,
            'sms_message' => '',
        ];

        if (!empty($backorderOrderIds)) {
            $response['message'] .= ' Backorder order' . (count($backorderOrderIds) > 1 ? 's' : '') . ': #' . implode(', #', $backorderOrderIds) . '.';
        }

        switch ($action) {
            case 'print':
                $response['should_print'] = true;
                break;

            case 'sms':
                // send SMS immediately
                $smsFlash = '';
                if (!$sale || !$sale->customer_phone) {
                    $smsFlash = ' No customer phone number.';
                } elseif (!LocalSmsService::configured()) {
                    $smsFlash = ' SMS provider not configured.';
                } else {
                    try {
                        $msg =
                            "Invoice #{$sale->id} | " .
                            ($sale->shop?->name ?? 'Shop') .
                            " | Total: {$sale->total_amount} | Paid: " . ($sale->paid_amount ?? 0) .
                            " | Balance: " . ($sale->balance_amount ?? 0);
                        LocalSmsService::send($sale->customer_phone, $msg);
                        $smsFlash = ' SMS sent.';
                        $response['should_sms'] = true;
                    } catch (\Throwable $e) {
                        $smsFlash = ' SMS failed: ' . $e->getMessage();
                    }
                }
                $response['message'] .= $smsFlash;
                break;

            case 'whatsapp':
                $response['should_whatsapp'] = true;
                $response['whatsapp_url'] = route('mt.sales.whatsapp', $saleId);
                break;

            case 'all':
                // print + whatsapp + sms (if enabled)
                $response['should_print'] = true;
                $response['should_whatsapp'] = true;

                $includeSms = (bool) $request->input('include_sms', true);
                if ($includeSms) {
                    $smsFlash = '';
                    if (!$sale || !$sale->customer_phone) {
                        $smsFlash = ' No customer phone number for SMS.';
                    } elseif (!LocalSmsService::configured()) {
                        $smsFlash = ' SMS provider not configured.';
                    } else {
                        try {
                            $msg =
                                "Invoice #{$sale->id} | " .
                                ($sale->shop?->name ?? 'Shop') .
                                " | Total: {$sale->total_amount} | Paid: " . ($sale->paid_amount ?? 0) .
                                " | Balance: " . ($sale->balance_amount ?? 0);
                            LocalSmsService::send($sale->customer_phone, $msg);
                            $smsFlash = ' SMS sent.';
                            $response['should_sms'] = true;
                        } catch (\Throwable $e) {
                            $smsFlash = ' SMS failed: ' . $e->getMessage();
                        }
                    }
                    $response['message'] .= $smsFlash;
                }
                $response['whatsapp_url'] = route('mt.sales.whatsapp', $saleId);
                break;

            default: // 'save'
                // Just save, no additional actions
                break;
        }

        // Return JSON for AJAX requests
        if ($isAjax) {
            return response()->json($response);
        }

        // Fallback redirect for non-AJAX
        session()->flash('success', $response['message']);
        if ($response['should_print']) {
            session()->flash('auto_print', true);
        }
        if ($response['should_whatsapp']) {
            session()->flash('send_whatsapp', true);
        }

        return redirect()->route('mt.sales.show', $saleId);
    }

    // ----------------------------
    // Internal helpers
    // ----------------------------

    /**
     * Adds sheet using the SAME /pos/add route, based on `g`:
     * - hardware sheets default full = 8x4
     * - foam sheets default full = 6x3
     *
     * Your blade currently sends cut_l + cut_w for cut. We use those.
     */
    private function addSheetViaAddRoute(Request $request, string $mode)
    {
        $data = $request->validate([
            'product_id' => ['required', 'integer'],
            'g' => ['nullable', 'string'],
            'cut_w' => ['nullable', 'numeric', 'min:0.01'],
            'cut_l' => ['nullable', 'numeric', 'min:0.01'],
        ]);

        $g = trim((string)($request->get('g', '')));

        // Default full sheet size by group
        // hardware => 8x4
        // foam => 6x3
        $fullL = 0.0;
        $fullW = 0.0;

        if ($g === 'hardware') {
            $fullW = 8.0;
            $fullL = 4.0;
        } elseif ($g === 'foam') {
            $fullW = 6.0;
            $fullL = 3.0;
        } else {
            // fallback if group not set
            $fullW = 8.0;
            $fullL = 4.0;
        }

        $p = Product::with(['company', 'category'])->findOrFail((int)$data['product_id']);
        $displayName = (string)$p->name;

        $fullPrice = (float)PricingService::defaultSellPrice($p);

        // Determine cut vs full
        if ($mode === 'sheet_full') {
            $cutL = $fullL;
            $cutW = $fullW;
            $ratio = 1.0;

            $unitPrice = round($fullPrice, 2);
            $rowId = 'p' . $p->id . ':full';

            $cart = session()->get('mt_cart', []);
            if (!isset($cart[$rowId])) {
                $cart[$rowId] = [
                    'row_id' => $rowId,
                    'product_id' => $p->id,
                    'name' => $displayName . ' (Full ' . $fullW . '×' . $fullL . ')',
                    'qty' => 1,
                    'price' => $unitPrice,

                    'unit_type' => 'sheet_full',
                    'sheet_full_w' => $fullW,
                    'sheet_full_l' => $fullL,
                    'cut_w' => $cutW,
                    'cut_l' => $cutL,
                    'ratio' => $ratio,
                    'full_price' => $fullPrice,
                ];
            } else {
                $cart[$rowId]['qty'] = (int)($cart[$rowId]['qty'] ?? 0) + 1;
            }

            session()->put('mt_cart', $cart);

            return redirect()->route('mt.pos.index', $request->only(['g', 'q', 'c']))->with('success', 'Full sheet added.');
        }

        // sheet_cut
        $cutW = (float)($data['cut_w'] ?? 0);
        $cutL = (float)($data['cut_l'] ?? 0);

        if ($cutW <= 0 || $cutL <= 0) {
            return redirect()->route('mt.pos.index', $request->only(['g', 'q', 'c']))
                ->with('error', 'Cut size missing.');
        }

        if ($cutW > $fullW) $cutW = $fullW;
        if ($cutL > $fullL) $cutL = $fullL;

        $fullArea = $fullW * $fullL;
        $cutArea = $cutW * $cutL;

        $ratio = ($fullArea > 0) ? ($cutArea / $fullArea) : 1;
        $ratio = max(0, min(1, $ratio));

        $unitPrice = round($fullPrice * $ratio, 2);

        $cart = session()->get('mt_cart', []);
        $rowId = 'p' . $p->id . ':cut:' . $cutW . 'x' . $cutL;

        // nicer label in cart
        $cutLabel = $displayName . ' (Cut ' . $cutW . '×' . $cutL . ')';

        if (!isset($cart[$rowId])) {
            $cart[$rowId] = [
                'row_id' => $rowId,
                'product_id' => $p->id,
                'name' => $cutLabel,
                'qty' => 1,
                'price' => $unitPrice, // ✅ proportional

                'unit_type' => 'sheet_cut',
                'sheet_full_w' => $fullW,
                'sheet_full_l' => $fullL,
                'cut_w' => $cutW,
                'cut_l' => $cutL,
                'ratio' => $ratio,
                'full_price' => $fullPrice,
            ];
        } else {
            $cart[$rowId]['qty'] = (int)($cart[$rowId]['qty'] ?? 0) + 1;
        }

        session()->put('mt_cart', $cart);

        return redirect()->route('mt.pos.index', $request->only(['g', 'q', 'c']))->with('success', 'Cut sheet added.');
    }

    private function emptyCartSummary(): array
    {
        return [
            'gross_total' => 0.0,
            'line_discount_total' => 0.0,
            'subtotal' => 0.0,
            'overall_discount' => 0.0,
            'total_discount' => 0.0,
            'total' => 0.0,
            'profit_before_overall' => 0.0,
            'profit' => 0.0,
        ];
    }

    private function resolveCartBasePrice(array $item, Product $product, string $unitType): float
    {
        $storedBase = round((float)($item['base_price'] ?? 0), 2);
        if ($storedBase > 0) {
            return $storedBase;
        }

        $ratio = (float)($item['ratio'] ?? 1);
        if ($ratio <= 0 || $ratio > 1) {
            $ratio = 1.0;
        }

        if (isset($item['full_price']) && (float)$item['full_price'] > 0) {
            $fullPrice = (float)$item['full_price'];
            if (in_array($unitType, ['sheet_cut', 'leftover_full', 'leftover_cut'], true)) {
                return round($fullPrice * $ratio, 2);
            }

            return round($fullPrice, 2);
        }

        $defaultSell = (float)PricingService::defaultSellPrice($product);
        if (in_array($unitType, ['sheet_cut', 'leftover_full', 'leftover_cut'], true)) {
            return round($defaultSell * $ratio, 2);
        }

        if ($defaultSell > 0) {
            return round($defaultSell, 2);
        }

        return round((float)($item['price'] ?? 0), 2);
    }

    private function normalizeCartLinePricing(array $item, Product $product, string $unitType, float $basePrice): array
    {
        $maxDiscountPercent = round(max(0, $product->effectiveMaxDiscountPercent()), 2);
        $hasDiscountCap = $maxDiscountPercent > 0;
        $requestedPrice = round(max(0, (float)($item['price'] ?? $basePrice)), 2);
        $requestedDiscountPercent = round(max(0, (float)($item['discount_percent'] ?? 0)), 2);
        $source = (string)($item['discount_source'] ?? '');

        if ($source === '') {
            if ($requestedDiscountPercent > 0) {
                $source = 'percent';
            } elseif ($basePrice > 0 && abs($requestedPrice - $basePrice) > 0.009) {
                $source = 'price';
            } else {
                $source = 'none';
            }
        }

        $minAllowedPrice = $hasDiscountCap && $basePrice > 0
            ? round($basePrice * (1 - ($maxDiscountPercent / 100)), 2)
            : 0.0;

        if ($source === 'percent') {
            $discountPercent = round($hasDiscountCap ? min($requestedDiscountPercent, $maxDiscountPercent) : $requestedDiscountPercent, 2);
            $price = $basePrice > 0
                ? round($basePrice * (1 - ($discountPercent / 100)), 2)
                : $requestedPrice;
        } else {
            $price = $requestedPrice;
            if ($hasDiscountCap && $basePrice > 0 && $price < $minAllowedPrice) {
                $price = $minAllowedPrice;
            }

            $discountPercent = ($basePrice > 0 && $price < $basePrice)
                ? round((($basePrice - $price) / $basePrice) * 100, 2)
                : 0.0;

            if ($hasDiscountCap && $discountPercent > $maxDiscountPercent) {
                $discountPercent = $maxDiscountPercent;
            }
        }

        if ($basePrice > 0 && $price > $basePrice) {
            $discountPercent = 0.0;
        }

        return [
            'price' => round($price, 2),
            'base_price' => round($basePrice, 2),
            'discount_percent' => round($discountPercent, 2),
            'discount_source' => $source,
            'max_discount_percent' => $maxDiscountPercent,
        ];
    }

    /**
     * Recalculate totals + profit.
     * - Per-line discount is capped by products.max_discount_percent.
     * - Overall discount is applied after line totals and reduces profit directly.
     * - Also returns normalized raw cart items so session state stays clean.
     */
    private function recalcCart(array $cart, ?float $overallDiscountOverride = null): array
    {
        $rows = [];
        $summary = $this->emptyCartSummary();
        $shopId = \App\Support\ShopContext::requireSingleShopId();
        $productIds = collect($cart)
            ->pluck('product_id')
            ->filter(fn ($productId) => (int) $productId > 0)
            ->map(fn ($productId) => (int) $productId)
            ->unique()
            ->values();

        $products = Product::query()
            ->with(['company', 'category'])
            ->whereIn('id', $productIds)
            ->get()
            ->keyBy('id');

        $shopProducts = \App\Models\ShopProduct::query()
            ->where('shop_id', $shopId)
            ->whereIn('product_id', $productIds)
            ->get()
            ->keyBy('product_id');

        foreach ($cart as $rowId => $it) {
            $productId = (int)($it['product_id'] ?? 0);
            if ($productId <= 0) {
                continue;
            }

            $p = $products->get($productId);
            if (!$p) {
                continue;
            }

            $unitType = (string)($it['unit_type'] ?? 'normal');
            $catLower = strtolower($p->category?->name ?? '');
            $isSlab = str_contains($catLower, 'slab') || str_contains($catLower, 'sheet') || str_contains(strtolower($p->name), 'slab') || str_contains(strtolower($p->name), 'sheet');
            if ($isSlab && $p->length_in > 0 && $p->width_in > 0 && $p->height_in > 0) {
                $len = (float)$p->length_in;
                $wid = (float)$p->width_in;
                $hei = (float)$p->height_in;
                $familyName = $this->deriveProductFamilyName($p->name);
                $displayName = "{$familyName} {$len}-{$wid}-{$hei}";
            } else {
                $displayName = (string)($it['name'] ?? $p->name);
            }

            if (str_starts_with($unitType, 'leftover_')) {
                $qty = 1;
            } elseif (in_array($unitType, ['sheet_full', 'sheet_cut'], true)) {
                $qty = (int)floor((float)($it['qty'] ?? 1));
                if ($qty < 1) {
                    $qty = 1;
                }
            } else {
                $qty = (float)($it['qty'] ?? 1);
                if ($qty < 0.01) {
                    $qty = 0.01;
                }
                $qty = round($qty, 2);
            }

            $ratio = 1.0;
            if (in_array($unitType, ['sheet_cut', 'leftover_full', 'leftover_cut'], true)) {
                $ratio = (float)($it['ratio'] ?? 0);
                if ($ratio <= 0 || $ratio > 1) {
                    $ratio = 1.0;
                }
            }

            $basePrice = $this->resolveCartBasePrice($it, $p, $unitType);
            $linePricing = $this->normalizeCartLinePricing($it, $p, $unitType, $basePrice);
            $price = $linePricing['price'];

            $sp = $shopProducts->get($p->id);
            $purchaseFull = $sp && (float)$sp->avg_cost > 0
                ? (float)$sp->avg_cost
                : (float)PricingService::purchasePrice($p);
            $purchaseEffective = round($purchaseFull * $ratio, 2);

            $baseLineTotal = round($qty * $linePricing['base_price'], 2);
            $lineTotal = round($qty * $price, 2);
            $discountAmount = round(max($baseLineTotal - $lineTotal, 0), 2);
            $lineProfit = round(($price - $purchaseEffective) * $qty, 2);

            $summary['gross_total'] += $baseLineTotal;
            $summary['line_discount_total'] += $discountAmount;
            $summary['subtotal'] += $lineTotal;
            $summary['profit_before_overall'] += $lineProfit;

            $raw = $it;
            $raw['row_id'] = (string)($it['row_id'] ?? $rowId);
            $raw['product_id'] = $productId;
            $raw['name'] = $displayName;
            $raw['qty'] = $qty;
            $raw['price'] = $price;
            $raw['base_price'] = $linePricing['base_price'];
            $raw['discount_percent'] = $linePricing['discount_percent'];
            $raw['discount_source'] = $linePricing['discount_source'];
            $raw['unit_type'] = $unitType;
            if (in_array($unitType, ['sheet_cut', 'leftover_full', 'leftover_cut'], true)) {
                $raw['ratio'] = $ratio;
            }

            $rows[] = [
                'row_id' => $rowId,
                'product_id' => $productId,
                'name' => $displayName,
                'qty' => $qty,
                'price' => $price,
                'base_price' => $linePricing['base_price'],
                'max_discount_percent' => $linePricing['max_discount_percent'],
                'discount_percent' => $linePricing['discount_percent'],
                'discount_source' => $linePricing['discount_source'],
                'discount_amount' => $discountAmount,
                'base_line_total' => $baseLineTotal,

                'purchase_price' => $purchaseFull,
                'purchase_effective' => $purchaseEffective,

                'line_total' => $lineTotal,
                'line_profit' => $lineProfit,

                'unit_type' => $unitType,
                'sheet_full_w' => $it['sheet_full_w'] ?? null,
                'sheet_full_l' => $it['sheet_full_l'] ?? null,
                'cut_w' => $it['cut_w'] ?? null,
                'cut_l' => $it['cut_l'] ?? null,
                'ratio' => $ratio,

                'leftover_id' => $it['leftover_id'] ?? null,
                'leftover_w' => $it['leftover_w'] ?? null,
                'leftover_l' => $it['leftover_l'] ?? null,
                'full_w' => $it['full_w'] ?? null,
                'full_l' => $it['full_l'] ?? null,

                'raw_cart_item' => $raw,
            ];
        }

        $requestedOverallDiscount = $overallDiscountOverride;
        if ($requestedOverallDiscount === null) {
            $requestedOverallDiscount = (float)session()->get('mt_cart_overall_discount', 0);
        }

        $requestedOverallDiscount = max(0, (float)$requestedOverallDiscount);
        $summary['gross_total'] = round($summary['gross_total'], 2);
        $summary['line_discount_total'] = round($summary['line_discount_total'], 2);
        $summary['subtotal'] = round($summary['subtotal'], 2);
        $summary['profit_before_overall'] = round($summary['profit_before_overall'], 2);
        $summary['overall_discount'] = round(min($requestedOverallDiscount, $summary['subtotal']), 2);
        $summary['total_discount'] = round($summary['line_discount_total'] + $summary['overall_discount'], 2);
        $summary['total'] = round(max($summary['subtotal'] - $summary['overall_discount'], 0), 2);
        $summary['profit'] = round($summary['profit_before_overall'] - $summary['overall_discount'], 2);

        return [$rows, $summary];
    }

    private function deriveProductFamilyName(string $name): string
    {
        $trimmed = trim((string) $name);
        if ($trimmed === '') {
            return '';
        }

        $patterns = [
            '/\s+(?:\d+(?:\.\d+)?(?:\s*[x×]\s*\d+(?:\.\d+)?)+)(?:\s*(?:ft|feet|in|inch|inches|["\']+))?$/i',
            '/\s+\d+(?:\.\d+)?(?:\s*(?:ft|feet|in|inch|inches|["\']+))?$/i',
        ];

        foreach ($patterns as $pattern) {
            $family = preg_replace($pattern, '', $trimmed);
            if ($family !== null && trim($family) !== $trimmed) {
                $trimmed = trim((string) $family);
                break;
            }
        }

        // Additional cleanup for slab sheets
        $lower = strtolower($trimmed);
        if (str_contains($lower, 'slab') || str_contains($lower, 'sheet')) {
            $trimmed = preg_replace('/\s*(?:\.|0\.|1\.|2\.)?\d+(?:\.\d+)?\s*$/i', '', $trimmed);
            $trimmed = trim($trimmed);
        }

        return $trimmed;
    }
}
