<?php

namespace App\Http\Controllers;

use App\Http\Requests\GroupRequest;
use App\Models\Group;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class GroupController extends Controller
{
    public function search(Request $request)
    {
        $groups = Group::query()
            ->where('tenant_id', auth()->user()->tenant->id)
            ->where('name', 'like', "%{$request->input('search')}%")
            ->limit(10)
            ->get();

        $groups = $groups->map(function ($group) {
            return [
                'id' => $group->id,
                'text' => $group->name,
            ];
        });

        return response()->json($groups);
    }

    public function index()
    {
        $query = Group::query()
            ->where('tenant_id', auth()->user()->tenant->id);

        return DataTables::make($query)
            ->editColumn('sequential', function ($group) {
                return str_pad($group->sequential, 5, '0', STR_PAD_LEFT);
            })
            ->editColumn('active', function ($group) {
                return view('partials.active', [
                    'active' => $group->active,
                ]);
            })
            ->addColumn('actions', function ($group) {
                return view('partials.actions', [
                    'id' => $group->id,
                    'entity' => 'groups',
                ]);
            })
            ->make(true);
    }

    public function store(GroupRequest $request)
    {
        $data = $request->validated();
        $data['tenant_id'] = auth()->user()->tenant->id;
        $data['active'] = $request->has('active') && $request->input('active') === 'on' ? true : false;

        $group = Group::create($data);

        return response()->json($group, 201);
    }

    public function show(Group $group)
    {
        return response()->json($group);
    }

    public function update(GroupRequest $request, Group $group)
    {
        $data = $request->validated();
        $data['active'] = $request->has('active') && $request->input('active') === 'on' ? true : false;

        $group->update($data);

        return response()->json($group);
    }

    public function destroy(Group $group)
    {
        $group->delete();

        return response()->json(null, 204);
    }
}
