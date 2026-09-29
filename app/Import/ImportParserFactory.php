<?php

namespace App\Import;

use App\Import\Contracts\ImportParser;
use App\Import\Parsers\CsvParser;
use App\Import\Parsers\JsonParser;
use App\Import\Parsers\XmlParser;

class ImportParserFactory
{
    public function make(string $extension): ImportParser
    {
        return match ($extension) {
            'csv' => new CsvParser(),
            'json' => new JsonParser(),
            'xml' => new XmlParser(),
            default => throw new \InvalidArgumentException(
                "Nieobsługiwany format pliku: {$extension}"
            ),
        };
    }
}