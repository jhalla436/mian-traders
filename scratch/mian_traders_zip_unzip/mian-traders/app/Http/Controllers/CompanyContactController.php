<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\CompanyContact;
use Illuminate\Http\Request;

class CompanyContactController extends Controller
{
    public function __construct()
    {
        // Anyone logged in can VIEW contacts.
        // Only admin/manager can create/edit/delete contacts.
        $this->middleware('role:admin,manager')->except(['index']);
    }

    public function index(Request $request, Company $company)
    {
        $q = trim((string)$request->get('q', ''));

        $contacts = CompanyContact::query()
            ->where('company_id', $company->id)
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($qq) use ($q) {
                    $qq->where('name', 'like', "%{$q}%")
                        ->orWhere('role_title', 'like', "%{$q}%")
                        ->orWhere('phone_primary', 'like', "%{$q}%")
                        ->orWhere('phone_alt', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%");
                });
            })
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        // eager load reportsTo for display
        $contacts->load('reportsTo');

        return view('mt.companies.contacts.index', compact('company', 'contacts', 'q'));
    }

    public function create(Company $company)
    {
        $reportToOptions = CompanyContact::query()
            ->where('company_id', $company->id)
            ->orderBy('name')
            ->get();

        $contact = new CompanyContact([
            'is_active' => 1,
            'sort_order' => 0,
        ]);

        return view('mt.companies.contacts.create', compact('company', 'contact', 'reportToOptions'));
    }

    public function store(Request $request, Company $company)
    {
        $data = $this->validateData($request, $company);
        $data['company_id'] = $company->id;
        $data['is_active'] = $request->has('is_active') ? 1 : 0;

        CompanyContact::create($data);

        return redirect()->route('mt.company_contacts.index', $company)->with('success', 'Contact added.');
    }

    public function edit(Company $company, CompanyContact $contact)
    {
        abort_unless($contact->company_id === $company->id, 404);

        $reportToOptions = CompanyContact::query()
            ->where('company_id', $company->id)
            ->where('id', '!=', $contact->id)
            ->orderBy('name')
            ->get();

        return view('mt.companies.contacts.edit', compact('company', 'contact', 'reportToOptions'));
    }

    public function update(Request $request, Company $company, CompanyContact $contact)
    {
        abort_unless($contact->company_id === $company->id, 404);

        $data = $this->validateData($request, $company, $contact->id);
        $data['is_active'] = $request->has('is_active') ? 1 : 0;

        $contact->update($data);

        return redirect()->route('mt.company_contacts.index', $company)->with('success', 'Contact updated.');
    }

    public function destroy(Company $company, CompanyContact $contact)
    {
        abort_unless($contact->company_id === $company->id, 404);
        $contact->delete();

        return redirect()->route('mt.company_contacts.index', $company)->with('success', 'Contact deleted.');
    }

    private function validateData(Request $request, Company $company, ?int $excludeId = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'role_title' => ['nullable', 'string', 'max:120'],
            'phone_primary' => ['nullable', 'string', 'max:40'],
            'phone_alt' => ['nullable', 'string', 'max:40'],
            'email' => ['nullable', 'string', 'max:120'],
            'address' => ['nullable', 'string', 'max:255'],
            'note' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'reports_to_contact_id' => ['nullable', 'integer'],
        ]);
    }
}
