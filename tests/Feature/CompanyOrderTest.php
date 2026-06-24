<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Product;
use App\Models\User;
use App\Models\Shop;
use App\Models\CompanyOrder;
use App\Models\CompanyOrderItem;
use App\Models\CompanyLedgerEntry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompanyOrderTest extends TestCase
{
    use RefreshDatabase;

    protected $shop;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a shop since activeShop middleware requires it
        $this->shop = Shop::create([
            'name' => 'Test Shop',
            'is_active' => true,
        ]);
        
        session(['shop_id' => $this->shop->id]);
    }

    public function test_can_load_company_products_via_api()
    {
        $user = User::factory()->create(['role' => 'admin']);
        $company = Company::create(['name' => 'Dura Foam', 'default_original_discount' => 18, 'default_extra_discount' => 10]);
        $category = \App\Models\Category::create(['name' => 'Mattresses']);
        $product = Product::create([
            'company_id' => $company->id,
            'category_id' => $category->id,
            'name' => 'Comfort 72x36x4',
            'sku' => 'COMF72364',
            'mrp' => 1000,
            'is_active' => true,
            'pricing_mode' => 'mrp',
        ]);

        $response = $this->actingAs($user)
            ->get(route('mt.company_orders.products', ['company_id' => $company->id]));

        $response->assertOk();
        $response->assertJson([
            'ok' => true,
            'default_original_discount' => 18.0,
            'default_extra_discount' => 10.0,
        ]);
    }

    public function test_can_create_company_order()
    {
        $user = User::factory()->create(['role' => 'admin']);
        $company = Company::create(['name' => 'Style Foam']);
        $category = \App\Models\Category::create(['name' => 'Mattresses']);
        $product = Product::create([
            'company_id' => $company->id,
            'category_id' => $category->id,
            'name' => 'Style Max 72x36x4',
            'mrp' => 2000,
            'is_active' => true,
            'pricing_mode' => 'mrp',
        ]);

        $response = $this->actingAs($user)
            ->post(route('mt.company_orders.store'), [
                'company_id' => $company->id,
                'order_date' => now()->toDateString(),
                'note' => 'Test note',
                'global_original_discount' => 15,
                'global_extra_discount' => 5,
                'product_id' => [$product->id],
                'qty' => [2],
                'original_discount' => [15],
                'extra_discount' => [5],
                'unit_cost' => [1615], // sequential discount: 2000 * 0.85 * 0.95 = 1615
            ]);

        $this->assertDatabaseHas('company_orders', [
            'company_id' => $company->id,
            'goods_total' => 3230.00, // 1615 * 2
            'original_discount' => 15,
            'extra_discount' => 5,
            'payment_status' => 'unpaid',
        ]);

        $this->assertDatabaseHas('company_ledger_entries', [
            'company_id' => $company->id,
            'entry_type' => 'order',
            'direction' => 'debit',
            'amount' => 3230.00,
        ]);
    }

    public function test_can_record_payment_against_order()
    {
        $user = User::factory()->create(['role' => 'admin']);
        $company = Company::create(['name' => 'Style Foam']);
        
        $order = CompanyOrder::create([
            'company_id' => $company->id,
            'order_date' => now(),
            'status' => 'open',
            'original_discount' => 18.00,
            'extra_discount' => 10.00,
            'goods_total' => 10000.00,
            'paid_amount' => 0.00,
            'payment_status' => 'unpaid',
        ]);

        $response = $this->actingAs($user)
            ->from(route('mt.company_orders.show', $order))
            ->post(route('mt.company_orders.payments', $order), [
                'payment_date' => now()->toDateString(),
                'amount' => 4000.00,
                'description' => 'Test partial payment',
            ]);

        $response->assertRedirect(route('mt.company_orders.show', $order));

        $order->refresh();
        $this->assertEquals(4000.00, $order->paid_amount);
        $this->assertEquals('partial', $order->payment_status);
        $this->assertEquals(6000.00, $order->balance);

        // Record full payment
        $response = $this->actingAs($user)
            ->from(route('mt.company_orders.show', $order))
            ->post(route('mt.company_orders.payments', $order), [
                'payment_date' => now()->toDateString(),
                'amount' => 6000.00,
                'description' => 'Test final payment',
            ]);

        $order->refresh();
        $this->assertEquals(10000.00, $order->paid_amount);
        $this->assertEquals('paid', $order->payment_status);
        $this->assertEquals(0, $order->balance);
    }

    public function test_can_delete_open_order()
    {
        $user = User::factory()->create(['role' => 'admin']);
        $company = Company::create(['name' => 'Style Foam']);
        
        $order = CompanyOrder::create([
            'company_id' => $company->id,
            'order_date' => now(),
            'status' => 'open',
            'goods_total' => 1000.00,
            'paid_amount' => 0.00,
            'payment_status' => 'unpaid',
        ]);

        $ledger = CompanyLedgerEntry::create([
            'company_id' => $company->id,
            'entry_date' => now(),
            'entry_type' => 'order',
            'direction' => 'debit',
            'amount' => 1000.00,
            'ref_type' => 'company_order',
            'ref_id' => $order->id,
        ]);

        $response = $this->actingAs($user)
            ->delete(route('mt.company_orders.destroy', $order));

        $response->assertRedirect(route('mt.company_orders.index'));

        $this->assertDatabaseMissing('company_orders', ['id' => $order->id]);
        $this->assertDatabaseMissing('company_ledger_entries', ['id' => $ledger->id]);
    }
}
