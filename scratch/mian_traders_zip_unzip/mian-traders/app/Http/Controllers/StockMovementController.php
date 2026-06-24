<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockMovement;
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
        $products = Product::query()
            ->where('is_active', 1)
            ->orderBy('name')
            ->get();

        return view('mt.stock_movements.create', compact('products'));
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

        DB::transaction(function () use ($data, $product, $qty) {
            // Apply stock change
            if ($data['type'] === 'in') {
                $product->increment('stock_qty', $qty);
            } elseif ($data['type'] === 'out') {
                $product->decrement('stock_qty', $qty);
            } else {
                // adjust means set exact stock qty to this qty
                $product->stock_qty = $qty;
                $product->save();
            }

            StockMovement::create([
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
