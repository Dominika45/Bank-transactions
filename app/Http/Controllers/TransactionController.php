<?php

namespace App\Http\Controllers;

use App\Models\Transaction;

class TransactionController extends Controller 
{

    public function index()
    {
        $transactions = Transaction::orderBy('created_at', 'desc')->paginate(10);

        return response()->json($transactions);
    }

}
