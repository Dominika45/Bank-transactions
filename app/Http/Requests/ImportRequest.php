<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ImportRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'import_file' => 'required|file|extensions:csv,json,xml',
            'all_or_nothing' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'import_file.required' => 'Wybierz plik.',
            'import_file.file' => 'Nieprawidłowy plik.',
            'import_file.extensions' => 'Plik musi mieć rozszerzenie CSV, JSON lub XML.',
            'all_or_nothing.boolean' => 'Nieprawidłowa wartość opcji "Wszystko albo nic".',
        ];
    }
	
}
