<?php
namespace App\Import\Parsers;

use App\Import\Contracts\ImportParser;
use Illuminate\Http\UploadedFile;

class XmlParser implements ImportParser
{
    public function parse(UploadedFile $file): iterable
    {
        $transactions_array = [];

        $handle = file_get_contents($file->getRealPath());

        $transactions = simplexml_load_string($handle);

        foreach ($transactions->transaction as $transaction) {
            $transactions_array[] =
            [
                'transaction_id' => (string)$transaction->transaction_id,
                'account_number' => (string)$transaction->account_number,
                'transaction_date' => (string)$transaction->transaction_date,
                'amount' => (string)$transaction->amount,
                'currency' => (string)$transaction->currency,
            ];
        }

        return $transactions_array;
    }
}