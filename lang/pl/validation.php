<?php

return [
    'required' => ':attribute jest wymagane.',
    'numeric' => ':attribute musi być liczbą.',
    'gt' => [
        'numeric' => ':attribute musi być większe od :value.',
    ],
    'date' => ':attribute musi zawierać prawidłową datę.',
    'iban' => ':attribute musi zawierać prawidłowy numer IBAN.',
    'regex' => ':attribute ma nieprawidłowy format.',
    'file' => ':attribute musi być plikiem.',
    'mimes' => ':attribute musi być plikiem typu: :values.',
    'attributes' => [
        'transaction_id' => 'Identyfikator transakcji',
        'account_number' => 'Numer rachunku',
        'transaction_date' => 'Data transakcji',
        'amount' => 'Kwota',
        'currency' => 'Waluta',
        'import_file' => 'Importowany plik',
    ],
];