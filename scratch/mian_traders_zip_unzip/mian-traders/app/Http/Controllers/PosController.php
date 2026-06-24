<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Company;
use App\Models\Customer;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\LeftoverPiece;
use App\Services\PricingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PosController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string)$request->get('q', ''));
        $g = trim((string)$request->get('g', ''));
        $c = trim((string)$request->get('c', ''));

        // Companies dropdown (by group_key)
        $companies = [];
        if ($g !== '') {
            $companies = Company::where('group_key', $g)->orderBy('name')->get();
        }

        // Products list
        $productsQuery = Product::query()
            ->with(['company', 'category'])
            ->where('is_active', 1);

        if ($g !== '') {
            $productsQuery->whereHas('company', function ($qq) use ($g) {
                $qq->where('group_key', $g);
            });
        }

        if ($c !== '' && is_numeric($c)) {
            $productsQuery->where('company_id', (int)$c);
        }

        if ($q !== '') {
            $productsQuery->where(function ($qq) use ($q) {
                $qq->where('name', 'like', "%{$q}%")
                    ->orWhere('sku', 'like', "%{$q}%")
                    ->orWhere('id', $q);
            });
        }

        $products = $productsQuery
            ->orderBy('name')
            ->limit(250)
            ->get();

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

        // Cart
        $cart = session()->get('mt_cart', []);
        [$cartRows, $cartTotal, $cartProfit] = $this->recalcCart($cart);

        // If old cart had broken rows, keep only valid ones
        $fixedCart = [];
        foreach ($cartRows as $r) {
            $fixedCart[$r['row_id']] = $r['raw_cart_item'];
        }
        session()->put('mt_cart', $fixedCart);

        return view('mt.pos.index', [
            'q' => $q,
            'g' => $g,
            'c' => $c,
            'companies' => $companies,
            'products' => $products,
            'leftovers' => $leftovers,
            'cartRows' => $cartRows,
            'cartTotal' => $cartTotal,
            'cartProfit' => $cartProfit,
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

        if ($q === '' || mb_strlen($q) < 1) {
            return response()->json(['items' => []]);
        }

        $query = Product::query()->with(['company','category'])->where('is_active', 1);

        if ($g !== '' && $g !== 'leftover') {
            $query->whereHas('company', function ($qq) use ($g) {
                $qq->where('group_key', $g);
            });
        }

        if ($c !== '' && is_numeric($c)) {
            $query->where('company_id', (int)$c);
        }

        $query->where(function ($qq) use ($q) {
            $qq->where('name', 'like', "%{$q}%")
               ->orWhere('sku', 'like', "%{$q}%")
               ->orWhere('id', $q);
        });

        $items = $query->orderBy('name')->limit(10)->get()->map(function ($p) {
            $sell = (float) PricingService::defaultSellPrice($p);

            return [
                'id' => $p->id,
                'name' => (string) ($p->name ?? ''),
                'sku' => $p->sku,
                'company' => $p->company?->name,
                'category' => $p->category?->name,
                'sell' => round($sell, 2),
            ];
        })->values();

        return response()->json(['items' => $items]);
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

        if (!isset($cart[$rowId])) {
            $cart[$rowId] = [
                'row_id' => $rowId,
                'product_id' => $p->id,
                'name' => $displayName,
                'qty' => 1, // default 1
                'price' => round($sell, 2),
                'unit_type' => 'normal',
            ];
        } else {
            $cart[$rowId]['qty'] = round(((float)($cart[$rowId]['qty'] ?? 0)) + 1, 2);
        }

        session()->put('mt_cart', $cart);

        return redirect()->route('mt.pos.index', $request->only(['g', 'q', 'c']))
            ->with('success', 'Added to cart.');
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

        $fullArea = $fullW * $fullL;
        $cutArea = $cutW * $cutL;

        $ratio = ($fullArea > 0) ? ($cutArea / $fullArea) : 1;
        $ratio = max(0, min(1, $ratio));

        $unitPrice = round($fullPrice * $ratio, 2);

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

        return redirect()->route('mt.pos.index', $request->only(['g', 'q', 'c']))
            ->with('success', 'Cut added.');
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

            'unit_type' => 'leftover_full',
            'leftover_id' => $lf->id,
            'leftover_w' => (float)$lf->width_ft,
            'leftover_l' => (float)$lf->length_ft,
            'full_w' => $fullW,
            'full_l' => $fullL,
            'ratio' => $ratio,
        ];

        session()->put('mt_cart', $cart);

        return redirect()->route('mt.pos.index', ['g' => 'leftover'])->with('success', 'Leftover added to cart.');
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

        return redirect()->route('mt.pos.index', ['g' => 'leftover'])->with('success', 'Leftover cut added to cart.');
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

        return redirect()->route('mt.pos.index', $request->only(['g', 'q', 'c']))
            ->with('success', 'Removed from cart.');
    }

    public function clear(Request $request)
    {
        session()->forget('mt_cart');

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
        }

        session()->put('mt_cart', $cart);

        [$rows, $total, $profit] = $this->recalcCart($cart);

        // keep only valid normalized items
        $fixedCart = [];
        foreach ($rows as $r) {
            $fixedCart[$r['row_id']] = $r['raw_cart_item'];
        }
        session()->put('mt_cart', $fixedCart);

        $canSeeCost = \App\Support\Authz::canSeeCost();

        $payload = [
            'ok' => true,
            'total' => $total,
        ];
        if ($canSeeCost) {
            $payload['profit'] = $profit;
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
        $data = $request->validate([
            'customer_name' => ['nullable', 'string', 'max:120'],
            'customer_phone' => ['nullable', 'string', 'max:30'],
            'customer_phone2' => ['nullable', 'string', 'max:30'],
            'customer_phone3' => ['nullable', 'string', 'max:30'],
            'customer_address' => ['nullable', 'string', 'max:255'],
            'customer_cnic' => ['nullable', 'string', 'max:30'],

            'received_cash' => ['nullable', 'numeric', 'min:0'],
            'note' => ['nullable', 'string', 'max:255'],
        ]);

        $cart = session()->get('mt_cart', []);
        [$cartRows, $total, $profitTotal] = $this->recalcCart($cart);

        if (count($cartRows) === 0) {
            return redirect()->route('mt.pos.index')->with('error', 'Cart is empty.');
        }

        $received = max(0, (float)($data['received_cash'] ?? 0));
        $paidApplied = round(min($received, $total), 2);
        $balance = round(max($total - $paidApplied, 0), 2);

        $status = ($balance <= 0 ? 'paid' : ($paidApplied > 0 ? 'partial' : 'unpaid'));
        $saleType = ($balance > 0 ? 'udhar' : 'cash');

        // UDHAR requirement
        if ($saleType === 'udhar') {
            if (trim((string)($data['customer_name'] ?? '')) === '' ||
                trim((string)($data['customer_phone'] ?? '')) === '' ||
                trim((string)($data['customer_address'] ?? '')) === '') {
                return redirect()->route('mt.pos.index')
                    ->with('error', 'For UDHAR: Name, Contact #1 and Address are required.');
            }
        }

        $saleId = null;

        DB::transaction(function () use (&$saleId, $data, $cartRows, $total, $paidApplied, $balance, $status, $saleType, $profitTotal) {

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
                'user_id' => auth()->id(),
                'customer_id' => $cust?->id,

                'customer_name' => $data['customer_name'] ?? ($cust?->name),
                'customer_phone' => $phone1 ?: ($cust?->phone_primary ?? $cust?->phone ?? null),
                'customer_phone2' => $phone2 ?: ($cust?->phone_alt_1 ?? null),
                'customer_phone3' => $phone3 ?: ($cust?->phone_alt_2 ?? null),
                'customer_address' => $data['customer_address'] ?? ($cust?->address),
                'customer_cnic' => $data['customer_cnic'] ?? ($cust?->cnic),

                'total_amount' => $total,
                'paid_amount' => $paidApplied,
                'balance_amount' => $balance,

                'sale_type' => $saleType,
                'status' => $status,
                'note' => $data['note'] ?? null,

                'profit_total' => round($profitTotal, 2),
                'profit_realized' => ($total > 0 ? round($profitTotal * ($paidApplied / $total), 2) : 0),
            ]);

            $saleId = $sale->id;

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
                    'purchase_price' => $purchaseEffective,
                    'line_total' => round($qty * $sell, 2),
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
                        Product::where('id', $productId)->decrement('stock_qty', $qty);
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
                    Product::where('id', $productId)->decrement('stock_qty', $qty);
                }

            }
        });

        session()->forget('mt_cart');

        return redirect()->route('mt.sales.show', $saleId)->with('success', "Sale completed. Bill #{$saleId}.");
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

    /**
     * Recalculate totals + profit.
     * IMPORTANT:
     * - For sheet_cut we scale purchase cost by ratio, so profit stays correct.
     * - Also returns 'raw_cart_item' so we can rewrite the session cart cleanly.
     */
    private function recalcCart(array $cart): array
    {
        $rows = [];
        $total = 0.0;
        $profit = 0.0;

        foreach ($cart as $rowId => $it) {
            $productId = (int)($it['product_id'] ?? 0);
            if ($productId <= 0) continue;

            $p = Product::with(['company', 'category'])->find($productId);
            if (!$p) continue;

            $unitType = (string)($it['unit_type'] ?? 'normal');

            $displayName = (string)($it['name'] ?? $p->name);
            // qty
            if (str_starts_with($unitType, 'leftover_')) {
                $qty = 1;
            } elseif (in_array($unitType, ['sheet_full', 'sheet_cut'], true)) {
                $qty = (int)floor((float)($it['qty'] ?? 1));
                if ($qty < 1) $qty = 1;
            } else {
                $qty = (float)($it['qty'] ?? 1);
                if ($qty < 0.01) $qty = 0.01;
                $qty = round($qty, 2);
            }

            $price = round((float)($it['price'] ?? 0), 2);

            // purchase is per full unit in your pricing service
            $purchaseFull = (float)PricingService::purchasePrice($p);

            // scale purchase for cut
            $ratio = 1.0;

            // scale purchase cost for any partial-rectangle sale
            if (in_array($unitType, ['sheet_cut','leftover_full','leftover_cut'], true)) {
                $ratio = (float)($it['ratio'] ?? 0);
                if ($ratio <= 0 || $ratio > 1) $ratio = 1.0;
            }

            $purchaseEffective = round($purchaseFull * $ratio, 2);

            $lineTotal = round($qty * $price, 2);
            $lineProfit = round(($price - $purchaseEffective) * $qty, 2);

            $total += $lineTotal;
            $profit += $lineProfit;

            // normalize item back into cart format
            $raw = $it;
            $raw['row_id'] = (string)($it['row_id'] ?? $rowId);
            $raw['product_id'] = $productId;
            $raw['name'] = $displayName;
            $raw['qty'] = $qty;
            $raw['price'] = $price;
            $raw['unit_type'] = $unitType;
            if (in_array($unitType, ['sheet_cut','leftover_full','leftover_cut'], true)) {
                $raw['ratio'] = $ratio;
            }

            $rows[] = [
                'row_id' => $rowId,
                'product_id' => $productId,
                'name' => $displayName,
                'qty' => $qty,
                'price' => $price,

                'purchase_price' => $purchaseFull,
                'purchase_effective' => $purchaseEffective,

                'line_total' => $lineTotal,
                'line_profit' => $lineProfit,

                // sheet meta for your blade labels
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

                // raw cart item to save back safely
                'raw_cart_item' => $raw,
            ];
        }

        return [$rows, round($total, 2), round($profit, 2)];
    }
}
