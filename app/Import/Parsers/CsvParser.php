<?php
namespace App\Import\Parsers;

use App\Import\Contracts\ImportParser;
use Illuminate\Http\UploadedFile;
use RuntimeException;

class CsvParser implements ImportParser
{
	
    public function parse(UploadedFile $file): iterable
    {
		
        $transactions_array = [];

        $handle = fopen($file->getRealPath(), 'r');

        $headers = fgetcsv($handle, 0, ',');

        $requiredHeaders = [
            'transaction_id',
            'account_number',
            'transaction_date',
            'amount',
            'currency',
        ];

        if ($headers === false || array_diff($requiredHeaders, $headers)) {
            fclose($handle);

            throw new RuntimeException('Nieprawidłowe nagłówki pliku CSV.');
        }

        while (($row = fgetcsv($handle, 0, ',')) !== false) {
            $data = array_combine($headers, $row);

            $transactions_array[] =
            [
                'transaction_id' => $data['transaction_id'],
                'account_number' => $data['account_number'],
                'transaction_date' => $data['transaction_date'],
                'amount' => $data['amount'],
                'currency' => $data['currency'],
            ];
        }

        fclose($handle);

        return $transactions_array;
		
    }
	
}