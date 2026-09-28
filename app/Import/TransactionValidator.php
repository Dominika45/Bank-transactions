<?php

namespace App\Import;

class TransactionValidator
{
    public function rules(): array
    {
        return [
            'transaction_id' => ['required'],
            'account_number' => ['required', 'regex:/^PL\d{26}$/'],
            'transaction_date' => ['required', 'date'],
            'amount' => ['required', 'numeric', 'gt:0'],
            'currency' => ['required', 'regex:/^[A-Z]{3}$/'],
        ];

    }
}
