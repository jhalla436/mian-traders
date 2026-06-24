<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockMovement;
use App\Support\Inventory;
use App\Support\ShopContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockMovementController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:admin,manager');
    }

    public function index(Request $request)
    {
        $q = trim((string)$request->get('q', ''));

        $rows = StockMovement::query()
            ->with(['product', 'user'])
            ->when($q !== '', function ($query) use ($q) {
                $query->where('note', 'like', "%{$q}%")
                    ->orWhere('type', 'like', "%{$q}%")
                    ->orWhereHas('product', function ($p) use ($q) {
                        $p->where('name', 'like', "%{$q}%")
                          ->orWhere('sku', 'like', "%{$q}%")
                          ->orWhere('id', $q);
                    });
            })
            ->orderByDesc('id')
            ->paginate(50)
            ->withQueryString();

        return view('mt.stock_movements.index', compact('rows', 'q'));
    }

    public function create()
    {
        $shopId = ShopContext::activeShopId();

        $products = Product::query()
            ->select(['products.*'])
            ->when(is_numeric($shopId), function ($query) use ($shopId) {
                $query->leftJoin('shop_products as sp', function ($join) use ($shopId) {
                    $join->on('sp.product_id', '=', 'products.id')
                        ->where('sp.shop_id', '=', (int) $shopId);
                })->addSelect(DB::raw('COALESCE(sp.stock_qty, products.stock_qty) as shop_stock_qty'));
            })
            ->where('is_active', 1)
            ->orderBy('name')
            ->get();

        // types used in the form; labels match helper text in blade
        $types = [
            'in' => 'IN',
            'out' => 'OUT',
            'adjust' => 'SET',
        ];

        // when creating we don't have an existing model
        $product = null;

        return view('mt.stock_movements.create', compact('products', 'types', 'product'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'product_id' => ['required', 'integer'],
            'type' => ['required', 'in:in,out,adjust'],
            'qty' => ['required', 'numeric', 'min:0.01'],
            'note' => ['nullable', 'string', 'max:255'],
        ]);

        $product = Product::findOrFail((int)$data['product_id']);
        $qty = (float)$data['qty'];
        $shopId = ShopContext::requireSingleShopId();

        DB::transaction(function () use ($data, $product, $qty, $shopId) {
            // Apply stock change
            if ($data['type'] === 'in') {
                Inventory::increment($shopId, (int) $product->id, $qty, 0);
            } elseif ($data['type'] === 'out') {
                Inventory::decrement($shopId, (int) $product->id, $qty);
            } else {
                Inventory::set($shopId, (int) $product->id, $qty);
            }

            StockMovement::create([
                'shop_id' => $shopId,
                'product_id' => $product->id,
                'user_id' => auth()->id(),
                'type' => $data['type'],
                'qty' => $qty,
                'note' => $data['note'] ?? null,
            ]);
        });

        return redirect()->route('mt.stock_movements.index')->with('success', 'Stock movement saved.');
    }
}
