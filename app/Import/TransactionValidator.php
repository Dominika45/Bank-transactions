<?php

namespace App\Import;
use App\Rules\ValidAmount;

class TransactionValidator
{
	
    public function rules(): array
    {
		
        return [
            'transaction_id' => ['required'],
            'account_number' => ['required', 'iban'],
            'transaction_date' => ['required', 'date'],
            'amount' => ['required', new ValidAmount()],
            'currency' => ['required', 'regex:/^[A-Z]{3}$/'],
        ];

    }
	
}
