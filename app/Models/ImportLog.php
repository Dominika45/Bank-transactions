<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImportLog extends Model
{
	const UPDATED_AT = null;
	
    protected $fillable = ['import_id', 'transaction_id', 'error_message'];

    public function transaction(): BelongsTo
	{
		return $this->belongsTo(Transaction::class);
	}

    public function import(): BelongsTo
	{
		return $this->belongsTo(Import::class, 'import_id');
	}
}
