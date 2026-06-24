<?php

namespace App\Http\Controllers;

use App\Models\CompanyGroup;
use Illuminate\Http\Request;

class CompanyGroupController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:admin,manager');
    }

    public function index(Request $request)
    {
        $q = trim((string)$request->get('q', ''));

        $rows = CompanyGroup::query()
            ->when($q !== '', function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                      ->orWhere('key', 'like', "%{$q}%")
                      ->orWhere('id', $q);
            })
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(25)
            ->withQueryString();

        return view('mt.company_groups.index', compact('rows', 'q'));
    }

    public function create()
    {
        $group = new CompanyGroup();
        return view('mt.company_groups.create', compact('group'));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['is_active'] = $request->has('is_active') ? 1 : 0;

        CompanyGroup::create($data);

        return redirect()->route('mt.company_groups.index')->with('success', 'Group created.');
    }

    public function edit(CompanyGroup $company_group)
    {
        $group = $company_group;
        return view('mt.company_groups.edit', compact('group'));
    }

    public function update(Request $request, CompanyGroup $company_group)
    {
        $data = $this->validated($request);
        $data['is_active'] = $request->has('is_active') ? 1 : 0;

        $company_group->update($data);

        return redirect()->route('mt.company_groups.index')->with('success', 'Group updated.');
    }

    public function destroy(CompanyGroup $company_group)
    {
        $company_group->delete();
        return redirect()->route('mt.company_groups.index')->with('success', 'Group deleted.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required','string','max:80'],
            'key' => ['required','string','max:40','regex:/^[a-z0-9_]+$/i'], // safe key
            'sort_order' => ['nullable','integer','min:0'],
        ]);
    }
}
