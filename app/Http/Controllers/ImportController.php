<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImportRequest;
use App\Services\ImportService;
use App\Models\Import;

class ImportController extends Controller 
{

    public function __construct(private ImportService $importService) {}

    public function store(ImportRequest $request)
    {
        try {
            $import = $this->importService->import(
                $request->file('import_file'),
                $request->boolean('all_or_nothing')
            );
            return response()->json($import, 201);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }
       
    }

    public function show(int $id)
    {
        $import = Import::with('importLog')->findOrFail($id);

        return response()->json($import);
    }

    public function index()
    {
        $import = Import::orderBy('created_at', 'desc')->paginate(10);

        return response()->json($import);
    }

}
