<?php

namespace App\Import\Contracts;

use Illuminate\Http\UploadedFile;

interface ImportParser
{
    public function parse(UploadedFile $file): iterable;
}