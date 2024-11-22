<?php

namespace App\Http\Controllers;

use App\Http\Requests\GroupRequest;
use App\Models\Group;
use App\Traits\TenantAuthorization;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class GroupController extends Controller
{
    use TenantAuthorization;

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function search(Request $request)
    {
        $searchTerm = $request->input('search', '');

        $groups = Group::where('tenant_id', auth()->user()->tenant->id)
            ->where('name', 'like', "%{$searchTerm}%")
            ->limit(10)
            ->get(['id', 'name']);

        return response()->json(
            $groups->map(fn ($group) => ['id' => $group->id, 'text' => $group->name])
        );
    }

    public function index()
    {
        if (request()->ajax()) {
            $query = Group::where('tenant_id', auth()->user()->tenant->id);

            return DataTables::of($query)
                ->editColumn('sequential', fn ($group) => str_pad($group->sequential, 5, '0', STR_PAD_LEFT))
                ->editColumn('active', fn ($group) => view('partials.active', ['active' => $group->active]))
                ->addColumn('actions', fn ($group) => view('partials.actions', ['id' => $group->id, 'entity' => 'groups']))
                ->make(true);
        }

        return view('groups.index');
    }

    public function store(GroupRequest $request)
    {
        $data = $request->validated();
        $data['tenant_id'] = auth()->user()->tenant->id;
        $data['active'] = $request->boolean('active');

        $group = Group::create($data);

        return response()->json($group, 201);
    }

    public function show(Group $group)
    {
        $this->authorizeTenantAccess($group);

        return response()->json($group);
    }

    public function update(GroupRequest $request, Group $group)
    {
        $this->authorizeTenantAccess($group);

        $data = $request->validated();
        $data['active'] = $request->boolean('active');

        $group->update($data);

        return response()->json($group);
    }

    public function destroy(Group $group)
    {
        $this->authorizeTenantAccess($group);

        $group->delete();

        return response()->json(null, 204);
    }
}
