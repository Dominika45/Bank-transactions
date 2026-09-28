<?php
namespace App\Import\Parsers;

use App\Import\Contracts\ImportParser;
use Illuminate\Http\UploadedFile;

class JsonParser implements ImportParser
{
    public function parse(UploadedFile $file): iterable
    {
        $transactions_array = [];

        $handle = file_get_contents($file->getRealPath());

        $transactions = json_decode($handle, true, 512, JSON_THROW_ON_ERROR);

        foreach ($transactions as $transaction) {
            $transactions_array[] =
            [
                'transaction_id' => $transaction['transaction_id'],
                'account_number' => $transaction['account_number'],
                'transaction_date' => $transaction['transaction_date'],
                'amount' => $transaction['amount'],
                'currency' => $transaction['currency'],
            ];
        }

        return $transactions_array;
    }
}