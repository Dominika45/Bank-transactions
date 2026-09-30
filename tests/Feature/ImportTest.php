<?php

namespace Tests\Feature;

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

    public function test_can_import_json_file(): void
    {
        $file = UploadedFile::fake()->createWithContent(
            'transactions.json',
            '[
                {
                    "transaction_id": "TX001",
                    "account_number": "PL61109010140000071219812874",
                    "transaction_date": "2026-09-01",
                    "amount": 100.00,
                    "currency": "PLN"
                }
            ]'
        );

        $response = $this->postJson('/api/imports', [
            'import_file' => $file,
        ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('imports', [
            'file_name' => 'transactions.json',
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

    public function test_can_import_xml_file(): void
    {
        $file = UploadedFile::fake()->createWithContent(
            'transactions.xml',
            '<transactions>
                <transaction>
                    <transaction_id>TX001</transaction_id>
                    <account_number>PL61109010140000071219812874</account_number>
                    <transaction_date>2026-09-01</transaction_date>
                    <amount>100.00</amount>
                    <currency>PLN</currency>
                </transaction>
            </transactions>'
        );

        $response = $this->postJson('/api/imports', [
            'import_file' => $file,
        ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('imports', [
            'file_name' => 'transactions.xml',
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

    public function test_invalid_iban_is_not_imported(): void
    {
        $file = UploadedFile::fake()->createWithContent(
            'transactions.csv',
            "transaction_id,account_number,transaction_date,amount,currency\n" .
            "TX001,PL61109010140000071219812875,2026-09-01,100.00,PLN\n"
        );

        $this->postJson('/api/imports', [
            'import_file' => $file,
        ]);

        $this->assertDatabaseHas('imports', [
            'total_records' => 1,
            'successful_records' => 0,
            'failed_records' => 1,
        ]);

        $this->assertDatabaseMissing('transactions', [
            'transaction_id' => 'TX001',
        ]);
    }

    public function test_import_without_file_returns_error(): void
    {
        $response = $this->postJson('/api/imports', []);

        $response->assertStatus(422);
    }
	
}