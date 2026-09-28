<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImportRequest;
use App\Services\ImportService;

class ImportController extends Controller 
{

    public function __construct(private ImportService $importService) {}

    public function store(ImportRequest $request)
    {
       $import = $this->importService->import($request->file('import_file'));
       return response()->json($import, 201);
    }

    public function index()
    {
        //
    }

}
