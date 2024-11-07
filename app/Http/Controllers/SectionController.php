<?php

namespace App\Http\Controllers;

use App\Http\Requests\SectionRequest;
use App\Models\Section;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class SectionController extends Controller
{
    public function search(Request $request)
    {
        $sections = Section::query()
            ->where('tenant_id', auth()->user()->tenant->id)
            ->where('name', 'like', "%{$request->input('search')}%")
            ->limit(10)
            ->get();

        $sections = $sections->map(function ($section) {
            return [
                'id' => $section->id,
                'text' => $section->name,
            ];
        });

        return response()->json($sections);
    }

    public function index()
    {
        $query = Section::query()
            ->where('tenant_id', auth()->user()->tenant->id);

        return DataTables::make($query)
            ->editColumn('sequential', function ($section) {
                return str_pad($section->sequential, 5, '0', STR_PAD_LEFT);
            })
            ->editColumn('active', function ($section) {
                return view('partials.active', [
                    'active' => $section->active,
                ]);
            })
            ->addColumn('actions', function ($section) {
                return view('partials.actions', [
                    'id' => $section->id,
                    'entity' => 'sections',
                ]);
            })
            ->make(true);
    }

    public function store(SectionRequest $request)
    {
        $data = $request->validated();
        $data['tenant_id'] = auth()->user()->tenant->id;
        $data['active'] = $request->has('active') && $request->input('active') === 'on' ? true : false;

        $section = Section::create($data);

        return response()->json($section, 201);
    }

    public function show(Section $section)
    {
        return response()->json($section);
    }

    public function update(SectionRequest $request, Section $section)
    {
        $data = $request->validated();
        $data['active'] = $request->has('active') && $request->input('active') === 'on' ? true : false;

        $section->update($data);

        return response()->json($section);
    }

    public function destroy(Section $section)
    {
        $section->delete();

        return response()->json(null, 204);
    }
}
