<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Category;
use App\Models\SheetDesign;
use App\Models\User;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class SheetDesignMappingTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $companyKmi;
    protected $companyAlnoor;
    protected $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create(['role' => 'admin']);

        // Create the hardware/lamination companies
        $this->companyKmi = Company::create([
            'name' => 'KMI lamination',
            'group_key' => 'hardware',
            'is_active' => true,
        ]);

        $this->companyAlnoor = Company::create([
            'name' => 'AL-Noor lamination',
            'group_key' => 'hardware',
            'is_active' => true,
        ]);

        $this->category = Category::create([
            'name' => 'Lamination',
            'group_key' => 'hardware',
        ]);
    }

    public function test_sheet_design_model_mapping_methods()
    {
        $design = SheetDesign::create([
            'name' => 'White',
            'finish' => 'Glossy',
            'color_group' => 'White',
        ]);

        $design->setCodeForCompany($this->companyKmi->id, '135');
        $design->setCodeForCompany($this->companyAlnoor->id, '4321');
        $design->save();

        // Verify mapped codes
        $this->assertEquals('135', $design->getCodeForCompany($this->companyKmi->id));
        $this->assertEquals('4321', $design->getCodeForCompany($this->companyAlnoor->id));

        // Test static lookup helper
        $resolvedKmi = SheetDesign::findByCompanyCode($this->companyKmi->id, '135');
        $this->assertNotNull($resolvedKmi);
        $this->assertEquals($design->id, $resolvedKmi->id);

        $resolvedAlnoor = SheetDesign::findByCompanyCode($this->companyAlnoor->id, '4321');
        $this->assertNotNull($resolvedAlnoor);
        $this->assertEquals($design->id, $resolvedAlnoor->id);

        // Test resolving non-existent code
        $this->assertNull(SheetDesign::findByCompanyCode($this->companyKmi->id, '9999'));
    }

    public function test_sheet_design_controller_actions()
    {
        // 1. Create a sheet design via store action with mapping codes
        $response = $this->actingAs($this->user)
            ->post(route('mt.sheet_designs.store'), [
                'name' => 'Oak Wood',
                'finish' => 'Textured',
                'color_group' => 'Brown',
                'sheet_codes' => [
                    $this->companyKmi->id => ' KMI-100 ', // Testing whitespace trim
                    $this->companyAlnoor->id => 'AL-200',
                ],
            ]);

        $response->assertRedirect(route('mt.sheet_designs.index'));

        $design = SheetDesign::where('name', 'Oak Wood')->first();
        $this->assertNotNull($design);
        $this->assertEquals('KMI-100', $design->getCodeForCompany($this->companyKmi->id));
        $this->assertEquals('AL-200', $design->getCodeForCompany($this->companyAlnoor->id));

        // 2. Load edit view and ensure companies and saved codes are displayed
        $response = $this->actingAs($this->user)
            ->get(route('mt.sheet_designs.edit', $design));
        $response->assertOk();
        $response->assertSee('KMI lamination');
        $response->assertSee('AL-Noor lamination');
        $response->assertSee('KMI-100');

        // 3. Update the sheet design (remove AL-Noor code, update KMI code)
        $response = $this->actingAs($this->user)
            ->put(route('mt.sheet_designs.update', $design), [
                'name' => 'Oak Wood (Updated)',
                'finish' => 'Textured',
                'color_group' => 'Brown',
                'sheet_codes' => [
                    $this->companyKmi->id => 'KMI-101',
                    $this->companyAlnoor->id => '', // Clearing mapping
                ],
            ]);

        $response->assertRedirect(route('mt.sheet_designs.index'));

        $design->refresh();
        $this->assertEquals('Oak Wood (Updated)', $design->name);
        $this->assertEquals('KMI-101', $design->getCodeForCompany($this->companyKmi->id));
        $this->assertNull($design->getCodeForCompany($this->companyAlnoor->id));
    }

    public function test_sheet_design_index_displays_codes()
    {
        $design = SheetDesign::create([
            'name' => 'Ash Wood',
            'sheet_codes' => [
                $this->companyKmi->id => 'ASH-99',
            ],
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('mt.sheet_designs.index'));

        $response->assertOk();
        $response->assertSee('KMI:');
        $response->assertSee('ASH-99');
    }

    public function test_csv_import_resolves_mapped_sheet_designs()
    {
        // Set up the mapped sheet design
        $design = SheetDesign::create([
            'name' => 'Super White',
            'sheet_codes' => [
                $this->companyKmi->id => '135',
                $this->companyAlnoor->id => '4321',
            ],
        ]);

        // CSV data using design codes instead of design names
        $csvContent = "name,sku,category,company,sheet_design\n"
                    . "KMI White Sheet,KMI-WS-123,Lamination,KMI lamination,135\n"
                    . "AlNoor White Sheet,AN-WS-123,Lamination,AL-Noor lamination,4321\n";

        $file = UploadedFile::fake()->createWithContent('products.csv', $csvContent);

        $response = $this->actingAs($this->user)
            ->post(route('mt.products.import_process'), [
                'file' => $file,
                'mode' => 'upsert',
            ]);

        $response->assertRedirect();
        
        // Assert products were created and linked to the same design
        $productKmi = Product::where('sku', 'KMI-WS-123')->first();
        $productAlnoor = Product::where('sku', 'AN-WS-123')->first();

        $this->assertNotNull($productKmi, "KMI product was not imported");
        $this->assertNotNull($productAlnoor, "Al-Noor product was not imported");

        $this->assertEquals($design->id, $productKmi->sheet_design_id, "KMI product not linked to mapped design");
        $this->assertEquals($design->id, $productAlnoor->sheet_design_id, "Al-Noor product not linked to mapped design");
    }

    public function test_csv_import_applies_company_default_lamination_rate()
    {
        // Configure company with default lamination rate
        $this->companyKmi->update([
            'default_lamination_rate' => 4000.00
        ]);

        // CSV data with lamination sheet containing no price/mrp (empty, 0, or omitted)
        $csvContent = "name,sku,category,company,mrp\n"
                    . "KMI White Sheet Default Rate,KMI-DR-111,Lamination,KMI lamination,\n"
                    . "KMI Glossy Sheet Zero Rate,KMI-DR-222,Lamination,KMI lamination,0\n";

        $file = UploadedFile::fake()->createWithContent('products_default.csv', $csvContent);

        $response = $this->actingAs($this->user)
            ->post(route('mt.products.import_process'), [
                'file' => $file,
                'mode' => 'upsert',
            ]);

        $response->assertRedirect();

        $product1 = Product::where('sku', 'KMI-DR-111')->first();
        $product2 = Product::where('sku', 'KMI-DR-222')->first();

        $this->assertNotNull($product1);
        $this->assertNotNull($product2);

        // Verify that the company's default rate of 4000.00 was applied
        $this->assertEquals(4000.00, (float)$product1->mrp, "Empty MRP did not fall back to default lamination rate");
        $this->assertEquals(4000.00, (float)$product2->mrp, "Zero MRP did not fall back to default lamination rate");
    }

    public function test_non_lamination_products_resolve_dimension_equivalents()
    {
        // Create a new shop context
        $shop = \App\Models\Shop::create(['name' => 'POS Test Shop', 'is_active' => true]);
        session(['shop_id' => $shop->id]);

        $sheshamCat = Category::create([
            'name' => 'Shesham',
            'group_key' => 'hardware',
        ]);

        // Create Shesham 8x4 from KMI
        $prodKmi = Product::create([
            'name' => 'KMI Shesham 8x4',
            'sku' => 'KMI-SH-84',
            'company_id' => $this->companyKmi->id,
            'category_id' => $sheshamCat->id,
            'length_in' => 8.00,
            'width_in' => 4.00,
            'mrp' => 1000,
            'is_active' => true,
        ]);

        // Create Shesham 8x4 from Al-Noor
        $prodAlnoor = Product::create([
            'name' => 'AlNoor Shesham 8x4',
            'sku' => 'AN-SH-84',
            'company_id' => $this->companyAlnoor->id,
            'category_id' => $sheshamCat->id,
            'length_in' => 8.00,
            'width_in' => 4.00,
            'mrp' => 1100,
            'is_active' => true,
        ]);

        // Fetch products via POS suggest or products API
        $response = $this->actingAs($this->user)
            ->get(route('mt.pos.products', [
                'g' => 'hardware',
                'q' => 'Shesham 8x4',
            ]));

        $response->assertOk();
        
        // Assert that in the returned products list, the Al-Noor equivalent lists KMI as equivalent and vice versa
        $data = $response->json();
        $products = $data['products'] ?? [];
        
        $this->assertNotEmpty($products);

        // Find the AlNoor product and verify it lists KMI product in equivalents
        $anResponseItem = collect($products)->firstWhere('id', $prodAlnoor->id);
        $this->assertNotNull($anResponseItem);
        $this->assertNotEmpty($anResponseItem['equivalents']);
        $this->assertEquals($prodKmi->id, $anResponseItem['equivalents'][0]['id']);
        $this->assertEquals('KMI lamination', $anResponseItem['equivalents'][0]['company_name']);
    }

    public function test_bulk_sheet_design_and_product_creator()
    {
        // Define active shop context
        $shop = \App\Models\Shop::create(['name' => 'POS Test Shop', 'is_active' => true]);
        $this->user->shops()->attach($shop);
        session(['shop_id' => $shop->id]);

        // Mock company default lamination rates
        $this->companyKmi->update(['default_lamination_rate' => 4000.00]);
        $this->companyAlnoor->update(['default_lamination_rate' => 4100.00]);

        // Raw CSV content mapping booklet codes
        $pastedData = "design_name,finish,color_group,KMI,AL-Noor lamination\n"
                    . "Teak Wood,Glossy,Brown,135,4321\n"
                    . "Pure White,Texture,White,136,\n";

        // POST request to bulk creator endpoint
        $response = $this->actingAs($this->user)
            ->post(route('mt.sheet_designs.bulk_process'), [
                'category_id' => $this->category->id,
                'sizes' => ['8x4', '6x4'],
                'custom_sizes' => '',
                'pasted_data' => $pastedData,
            ]);

        $response->assertRedirect(route('mt.sheet_designs.index'));

        // Assert SheetDesign records were created
        $design1 = SheetDesign::where('name', 'Teak Wood')->first();
        $this->assertNotNull($design1);
        $this->assertEquals('Glossy', $design1->finish);
        $this->assertEquals('135', $design1->getCodeForCompany($this->companyKmi->id));
        $this->assertEquals('4321', $design1->getCodeForCompany($this->companyAlnoor->id));

        $design2 = SheetDesign::where('name', 'Pure White')->first();
        $this->assertNotNull($design2);
        $this->assertEquals('Texture', $design2->finish);
        $this->assertEquals('136', $design2->getCodeForCompany($this->companyKmi->id));
        $this->assertNull($design2->getCodeForCompany($this->companyAlnoor->id));

        // Assert products were generated automatically in correct sizes (8x4 -> 96x48 inches, 6x4 -> 72x48 inches)
        $prodKmi84 = Product::where('company_id', $this->companyKmi->id)
            ->where('sheet_design_id', $design1->id)
            ->where('length_in', 96.0)
            ->where('width_in', 48.0)
            ->first();
        $this->assertNotNull($prodKmi84);
        $this->assertEquals('KMI-135-8x4', $prodKmi84->sku);
        $this->assertEquals(4000.00, (float)$prodKmi84->mrp);

        $prodAlnoor64 = Product::where('company_id', $this->companyAlnoor->id)
            ->where('sheet_design_id', $design1->id)
            ->where('length_in', 72.0)
            ->where('width_in', 48.0)
            ->first();
        $this->assertNotNull($prodAlnoor64);
        $this->assertEquals('AL-NOOR-4321-6x4', $prodAlnoor64->sku);
        $this->assertEquals(4100.00, (float)$prodAlnoor64->mrp);

        // Design 2 has KMI products, but Al-Noor has no mapping, so Al-Noor products shouldn't be created for Design 2
        $prodKmiWhite84 = Product::where('company_id', $this->companyKmi->id)
            ->where('sheet_design_id', $design2->id)
            ->where('length_in', 96.0)
            ->first();
        $this->assertNotNull($prodKmiWhite84);

        $prodAlnoorWhite84 = Product::where('company_id', $this->companyAlnoor->id)
            ->where('sheet_design_id', $design2->id)
            ->first();
        $this->assertNull($prodAlnoorWhite84);

        // Assert ShopProduct entries are automatically generated
        $shopProduct = \App\Models\ShopProduct::where('shop_id', $shop->id)
            ->where('product_id', $prodKmi84->id)
            ->first();
        $this->assertNotNull($shopProduct);
        $this->assertEquals(4000.00, (float)$shopProduct->selling_price);
    }
}
