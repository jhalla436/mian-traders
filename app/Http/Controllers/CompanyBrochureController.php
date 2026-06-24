<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\CompanyBrochure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CompanyBrochureController extends Controller
{
    /**
     * Display a listing of lamination/hardware companies and their brochures.
     */
    public function index()
    {
        $companies = Company::where('group_key', 'hardware')
            ->with('brochures')
            ->orderBy('name')
            ->get();

        return view('mt.company_brochures.index', compact('companies'));
    }

    /**
     * Store newly uploaded brochures in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'company_id' => ['required', 'integer', 'exists:companies,id'],
            'title' => ['nullable', 'string', 'max:255'],
            'files' => ['required', 'array'],
            'files.*' => ['required', 'file', 'mimes:pdf,png,jpg,jpeg', 'max:15360'],
        ]);

        $company = Company::findOrFail($request->company_id);
        $files = $request->file('files');
        $title = trim((string)$request->input('title'));

        foreach ($files as $index => $file) {
            // Store the file in public brochures directory
            $path = $file->store('brochures', 'public');

            if ($title !== '') {
                // If there are multiple files, append a suffix like " - 1", " - 2"
                $fileName = count($files) > 1 ? "{$title} - " . ($index + 1) : $title;
            } else {
                $fileName = $file->getClientOriginalName();
            }

            // If title was custom but file is PDF, append .pdf if not already present, just for consistency
            if ($title !== '' && strtolower($file->getClientOriginalExtension()) === 'pdf' && !str_ends_with(strtolower($fileName), '.pdf')) {
                $fileName .= '.pdf';
            }

            CompanyBrochure::create([
                'company_id' => $company->id,
                'file_path' => $path,
                'file_name' => $fileName,
                'mime_type' => $file->getClientMimeType(),
            ]);
        }

        $msg = count($files) > 1 ? 'Brochures uploaded successfully.' : 'Brochure uploaded successfully.';

        return redirect()->route('mt.company_brochures.index')
            ->with('success', $msg);
    }

    /**
     * Stream or display the brochure file directly.
     */
    public function showFile(CompanyBrochure $brochure)
    {
        if (!Storage::disk('public')->exists($brochure->file_path)) {
            abort(404, 'File not found in storage.');
        }

        $fullPath = Storage::disk('public')->path($brochure->file_path);

        return response()->file($fullPath, [
            'Content-Type' => $brochure->mime_type,
            'Content-Disposition' => 'inline; filename="' . basename($brochure->file_name) . '"'
        ]);
    }

    /**
     * Remove the specified brochure from storage and database.
     */
    public function destroy(CompanyBrochure $brochure)
    {
        // Delete the file from disk
        if (Storage::disk('public')->exists($brochure->file_path)) {
            Storage::disk('public')->delete($brochure->file_path);
        }

        $brochure->delete();

        return redirect()->route('mt.company_brochures.index')
            ->with('success', 'Brochure deleted successfully.');
    }
}
