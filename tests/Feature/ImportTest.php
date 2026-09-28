<?php

namespace Tests\Feature;

use App\Models\Import;
use App\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ImportTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_import_csv_file(): void
    {
        $file = UploadedFile::fake()->createWithContent(
            'transactions.csv',
            "transaction_id,account_number,transaction_date,amount,currency\n" .
            "TX001,PL61109010140000071219812874,2026-09-01,100.00,PLN\n"
        );

        $response = $this->postJson('/api/imports', [
            'import_file' => $file,
        ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('imports', [
            'file_name' => 'transactions.csv',
            'total_records' => 1,
            'successful_records' => 1,
            'failed_records' => 0,
            'status' => 'success',
        ]);

        $this->assertDatabaseHas('transactions', [
            'transaction_id' => 'TX001',
            'amount' => 100.00,
            'currency' => 'PLN',
        ]);
    }
}