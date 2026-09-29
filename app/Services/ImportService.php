<?php 

namespace App\Services;

use App\Import\ImportParserFactory;
use App\Import\TransactionValidator;
use App\Models\Import;
use App\Models\Transaction;
use App\Models\ImportLog;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;

class ImportService
{

    public function __construct(private ImportParserFactory $parserFactory, private TransactionValidator $transactionValidator) {
    }

    public function import(UploadedFile $file): Import
    {
        $import = Import::create([
            'file_name' => $file->getClientOriginalName(),
            'total_records' => 0,
            'successful_records' => 0,
            'failed_records' => 0,
            'status' => 'failed',
        ]);

        $extension = $file->getClientOriginalExtension();
        $parser = $this->parserFactory->make($extension);
        $records = $parser->parse($file);
        $successful_records = 0;
        $failed_records = 0;

        foreach($records as $record) {

            $validator = Validator::make(
                $record,
                $this->transactionValidator->rules()
            );

            if ($validator->fails() === false) {

                if (Transaction::where('transaction_id', '=', $record['transaction_id'])->exists()) {
                    ImportLog::create([
                        'import_id' => $import->id,
                        'transaction_id' => $record['transaction_id'] ?? null,
                        'error_message' => 'Powielona transakcja.',
                    ]);
                    $failed_records ++;
                    continue;
                }

                Transaction::create($record);
                $successful_records ++;
            }
            else {
                ImportLog::create([
                    'import_id' => $import->id,
                    'transaction_id' => $record['transaction_id'] ?? null,
                    'error_message' => $validator->errors()->first(),
                ]);
                $failed_records ++;
            }
        }

        $status = match (true) {
            $successful_records > 0 && $failed_records === 0 => 'success',
            $successful_records > 0 && $failed_records > 0 => 'partial',
            default => 'failed',
        };

        $import->update([
            'total_records' => $successful_records + $failed_records,
            'successful_records' => $successful_records,
            'failed_records' => $failed_records,
            'status' => $status,
        ]);
        
        return $import;

    }
}