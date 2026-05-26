<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\Company;
use App\Models\CompanyBarcode;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompanyBarcodeControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs(User::factory()->create());
    }

    /** @test */
    public function it_creates_a_company_barcode_and_logs_activity()
    {
        $this->withoutExceptionHandling();

        $response = $this->post(route('company-barcodes.store'), [
            'company_name' => 'Test Company',
            'items' => [
                [
                    'part_name' => 'Part A',
                    'qty' => 10,
                ],
            ],
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Barcode perusahaan berhasil dibuat.');

        $this->assertDatabaseHas('companies', ['name' => 'Test Company']);
        $company = Company::where('name', 'Test Company')->first();
        $this->assertNotNull($company);

        $this->assertDatabaseHas('company_barcodes', ['company_id' => $company->id]);

        $this->assertDatabaseHas('activity_logs', [
            'subject_type' => 'Perusahaan',
            'activity_type' => 'Buat',
            'description' => 'Membuat barcode perusahaan: Test Company',
        ]);
    }

    /** @test */
    public function it_deletes_a_company_barcode_and_logs_activity()
    {
        $this->withoutExceptionHandling();

        $company = Company::factory()->create(['name' => 'Company to Delete']);
        $companyBarcode = CompanyBarcode::factory()->create(['company_id' => $company->id]);

        $response = $this->delete(route('company-barcodes.destroy', $companyBarcode));

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Data perusahaan dan barcode terkait dihapus.');

        $this->assertDatabaseMissing('companies', ['id' => $company->id]);
        $this->assertDatabaseMissing('company_barcodes', ['id' => $companyBarcode->id]);

        $this->assertDatabaseHas('activity_logs', [
            'subject_type' => 'Perusahaan',
            'activity_type' => 'Hapus',
            'description' => 'Menghapus perusahaan: Company to Delete',
        ]);
    }

    /** @test */
    public function it_deletes_a_company_by_id_and_logs_activity()
    {
        $this->withoutExceptionHandling();

        $company = Company::factory()->create(['name' => 'Company to Delete by ID']);
        $companyBarcode = CompanyBarcode::factory()->create(['company_id' => $company->id]);

        $response = $this->delete(route('company-barcodes.destroyCompany', $company->id));

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Data perusahaan dihapus.');

        $this->assertDatabaseMissing('companies', ['id' => $company->id]);
        $this->assertDatabaseMissing('company_barcodes', ['id' => $companyBarcode->id]);

        $this->assertDatabaseHas('activity_logs', [
            'subject_type' => 'Perusahaan',
            'activity_type' => 'Hapus',
            'description' => 'Menghapus perusahaan: Company to Delete by ID',
        ]);
    }
}
