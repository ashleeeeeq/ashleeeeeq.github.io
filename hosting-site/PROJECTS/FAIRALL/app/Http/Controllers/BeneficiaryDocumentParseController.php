<?php

namespace App\Http\Controllers;

use App\Models\Program;
use App\Services\DocumentParsingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BeneficiaryDocumentParseController extends Controller
{
    public function __invoke(Request $request, DocumentParsingService $parser): JsonResponse
    {
        $request->validate([
            'document' => ['required', 'array', 'min:1'],
            'document.*' => ['required', 'file', 'mimes:jpg,jpeg,png,gif,webp', 'max:5120'],
            'program_id' => ['required', 'integer', 'exists:programs,id'],
        ]);

        $program = Program::findOrFail($request->integer('program_id'));
        $programType = $this->resolveProgramType($program);

        try {
            $parsed = $parser->parse($request->file('document', []), $programType);

            return response()->json($parsed->toArray());

        } catch (\Throwable $e) {
            Log::error('Document parsing failed', [
                'error' => $e->getMessage(),
                'program_id' => $request->integer('program_id'),
            ]);

            return response()->json([
                'message' => $e instanceof \InvalidArgumentException
                    ? $e->getMessage()
                    : 'Could not parse document. Please fill the form manually.',
            ], 422);
        }
    }

    private function resolveProgramType(Program $program): string
    {
        $name = strtolower($program->program_name);

        if (str_contains($name, 'sports')) {
            return 'sports';
        }

        return 'education';
    }
}
