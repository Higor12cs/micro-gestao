<?php

namespace App\Http\Controllers;

use App\Http\Requests\SectionRequest;
use App\Models\Section;
use App\Traits\TenantAuthorization;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class SectionController extends Controller
{
    use TenantAuthorization;

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function search(Request $request)
    {
        $sections = Section::query()
            ->where('name', 'like', "%{$request->input('search')}%")
            ->limit(10)
            ->get()
            ->map(fn ($section) => ['id' => $section->id, 'text' => $section->name]);

        return response()->json($sections);
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Section::query();

            return DataTables::of($query)
                ->editColumn('sequential', fn ($section) => str_pad($section->sequential, 5, '0', STR_PAD_LEFT))
                ->editColumn('active', fn ($section) => view('partials.active', ['active' => $section->active]))
                ->addColumn('actions', fn ($section) => view('partials.actions', [
                    'id' => $section->id,
                    'sequential' => $section->sequential,
                    'entity' => 'sections',
                ]))
                ->make(true);
        }

        return view('sections.index');
    }

    public function store(SectionRequest $request)
    {
        $data = $request->validated();
        $data['active'] = $request->boolean('active');

        $section = Section::create($data);

        return response()->json($section, 201);
    }

    public function show(Section $section)
    {
        $this->authorizeTenantAccess($section);

        return response()->json($section);
    }

    public function update(SectionRequest $request, Section $section)
    {
        $this->authorizeTenantAccess($section);

        $data = $request->validated();
        $data['active'] = $request->boolean('active');

        $section->update($data);

        return response()->json($section);
    }

    public function destroy(Section $section)
    {
        $this->authorizeTenantAccess($section);

        $section->delete();

        return response()->json(null, 204);
    }
}
