<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Product;
use App\Models\Category;
use App\Models\User;
use App\Models\Shop;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PosSearchTest extends TestCase
{
    use RefreshDatabase;

    protected $shop;
    protected $user;
    protected $company;
    protected $category;
    protected $product;

    protected function setUp(): void
    {
        parent::setUp();

        // Create active shop
        $this->shop = Shop::create([
            'name' => 'Test POS Shop',
            'is_active' => true,
        ]);
        session(['shop_id' => $this->shop->id]);

        $this->user = User::factory()->create(['role' => 'admin']);
        $this->company = Company::create(['name' => 'Dura Foam']);
        $this->category = Category::create(['name' => 'Mattresses']);

        $this->product = Product::create([
            'company_id' => $this->company->id,
            'category_id' => $this->category->id,
            'name' => 'Comfort 78x36x4',
            'sku' => 'COMF78364',
            'mrp' => 1000,
            'is_active' => true,
            'pricing_mode' => 'mrp',
            'length_in' => 78.00,
            'width_in' => 36.00,
            'height_in' => 4.00,
        ]);
    }

    public function test_pos_suggest_supports_asterisk_searching()
    {
        // Search for '78*' (which gets converted to '78%')
        $response = $this->actingAs($this->user)
            ->get(route('mt.pos.suggest', ['q' => '78*']));

        $response->assertOk();
        $response->assertJsonPath('items.0.name', 'Comfort 78x36x4');

        // Search for '78*36*' (which gets converted to '78%36%')
        $response = $this->actingAs($this->user)
            ->get(route('mt.pos.suggest', ['q' => '78*36*']));

        $response->assertOk();
        $response->assertJsonPath('items.0.name', 'Comfort 78x36x4');
    }

    public function test_pos_products_route_supports_asterisk_searching()
    {
        // Search for '78*36*4' (which gets converted to '78%36%4')
        $response = $this->actingAs($this->user)
            ->get(route('mt.pos.products', ['q' => '78*36*4']));

        $response->assertOk();
        $response->assertJsonPath('products.0.name', 'Comfort 78x36x4');
    }
}
