<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\CompanyBrochure;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CompanyBrochureTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $company;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $this->user = User::factory()->create(['role' => 'admin']);

        $this->company = Company::create([
            'name' => 'KMI lamination',
            'group_key' => 'hardware',
            'is_active' => true,
        ]);
    }

    public function test_brochures_index_page_is_accessible()
    {
        $response = $this->actingAs($this->user)
            ->get(route('mt.company_brochures.index'));

        $response->assertOk();
        $response->assertSee('KMI lamination');
    }

    public function test_user_can_upload_pdf_brochure()
    {
        $file = UploadedFile::fake()->create('catalog2026.pdf', 500, 'application/pdf');

        $response = $this->actingAs($this->user)
            ->post(route('mt.company_brochures.store'), [
                'company_id' => $this->company->id,
                'title' => 'KMI Rate List 2026',
                'files' => [$file],
            ]);

        $response->assertRedirect(route('mt.company_brochures.index'));

        // Assert database record exists
        $this->assertDatabaseHas('company_brochures', [
            'company_id' => $this->company->id,
            'file_name' => 'KMI Rate List 2026.pdf',
            'mime_type' => 'application/pdf',
        ]);

        $brochure = CompanyBrochure::first();
        $this->assertNotNull($brochure);

        // Assert file exists on disk
        Storage::disk('public')->assertExists($brochure->file_path);
    }

    public function test_user_can_stream_brochure_file()
    {
        $file = UploadedFile::fake()->create('image.png', 100, 'image/png');
        $path = Storage::disk('public')->putFile('brochures', $file);

        $brochure = CompanyBrochure::create([
            'company_id' => $this->company->id,
            'file_path' => $path,
            'file_name' => 'Image Catalogue.png',
            'mime_type' => 'image/png',
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('mt.company_brochures.file', $brochure));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'image/png');
    }

    public function test_user_can_delete_brochure()
    {
        $file = UploadedFile::fake()->create('delete-me.pdf', 200, 'application/pdf');
        $path = Storage::disk('public')->putFile('brochures', $file);

        $brochure = CompanyBrochure::create([
            'company_id' => $this->company->id,
            'file_path' => $path,
            'file_name' => 'Temp Brochure.pdf',
            'mime_type' => 'application/pdf',
        ]);

        Storage::disk('public')->assertExists($path);

        $response = $this->actingAs($this->user)
            ->delete(route('mt.company_brochures.destroy', $brochure));

        $response->assertRedirect(route('mt.company_brochures.index'));

        // Assert database record deleted
        $this->assertDatabaseMissing('company_brochures', [
            'id' => $brochure->id,
        ]);

        // Assert file deleted from disk
        Storage::disk('public')->assertMissing($path);
    }
}
