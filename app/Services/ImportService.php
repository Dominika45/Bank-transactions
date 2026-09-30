<?php

namespace App\Services;

use App\Import\ImportParserFactory;
use App\Import\TransactionValidator;
use App\Models\Import;
use App\Models\Transaction;
use App\Models\ImportLog;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use RuntimeException;

class ImportService
{
	
    public function __construct(
        private ImportParserFactory $parserFactory,
        private TransactionValidator $transactionValidator
    ) {
    }

    public function import(UploadedFile $file, bool $allOrNothing = false): Import
    {
        $parser = $this->parserFactory->make(
            strtolower($file->getClientOriginalExtension())
        );
        $records = $parser->parse($file);

        $import = Import::create([
            'file_name' => $file->getClientOriginalName(),
            'total_records' => 0,
            'successful_records' => 0,
            'failed_records' => 0,
            'status' => 'failed',
        ]);

        return $allOrNothing
            ? $this->importAllOrNothing($import, $records)
            : $this->importNormally($import, $records);
    }

    private function importAllOrNothing(Import $import, array $records): Import
    {
        $total = count($records);
        $currentId = null;

        try {
            DB::transaction(function () use ($records, &$currentId) {
                foreach ($records as $record) {
                    $currentId = $record['transaction_id'] ?? null;

                    if ($error = $this->recordError($record)) {
                        throw new RuntimeException($error);
                    }

                    Transaction::create($record);
                }
            });
        } catch (RuntimeException $e) {
            ImportLog::create([
                'import_id' => $import->id,
                'transaction_id' => $currentId,
                'error_message' => 'Import w trybie "Wszystko albo nic" '.'nie został wykonany. Powód: '.$e->getMessage(),
            ]);

            $import->update([
                'total_records' => $total,
                'successful_records' => 0,
                'failed_records' => 1,
                'status' => 'failed',
            ]);

            return $import;
        }

        $import->update([
            'total_records' => $total,
            'successful_records' => $total,
            'failed_records' => 0,
            'status' => 'success',
        ]);

        return $import;
    }

    private function importNormally(Import $import, array $records): Import
    {
        $successful_records = 0;
        $failed_records = 0;

        foreach ($records as $record) {
            if ($error = $this->recordError($record)) {
                ImportLog::create([
                    'import_id' => $import->id,
                    'transaction_id' => $record['transaction_id'] ?? null,
                    'error_message' => $error,
                ]);

                $failed_records++;
                continue;
            }

            Transaction::create($record);
            $successful_records++;
        }

        $import->update([
            'total_records' => $successful_records + $failed_records,
            'successful_records' => $successful_records,
            'failed_records' => $failed_records,
            'status' => match (true) {
                $failed_records === 0 && $successful_records > 0 => 'success',
                $successful_records > 0 => 'partial',
                default => 'failed',
            },
        ]);

        return $import;
    }

    private function recordError(array $record): ?string
    {
        $validator = Validator::make(
            $record,
            $this->transactionValidator->rules()
        );

        if ($validator->fails()) {
            return $validator->errors()->first();
        }

        if (Transaction::where('transaction_id', $record['transaction_id'])->exists()) {
            return 'Powielona transakcja.';
        }

        return null;
    }
	
}