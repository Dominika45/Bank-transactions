<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Import extends Model
{
	const UPDATED_AT = null;
	
    protected $fillable = ['file_name', 'total_records', 'successful_records', 'failed_records', 'status'];

    public function importLog(): HasMany
	{
		return $this->hasMany(ImportLog::class);
	}
}
