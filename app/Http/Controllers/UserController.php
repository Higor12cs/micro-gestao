<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Traits\TenantAuthorization;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    use TenantAuthorization;

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function search(Request $request)
    {
        $users = User::query()
            ->where('tenant_id', auth()->user()->tenant_id)
            ->where('name', 'like', "%{$request->input('search')}%")
            ->limit(10)
            ->get()
            ->map(fn($user) => [
                'id' => $user->id,
                'text' => $user->name,
            ]);

        return response()->json($users);
    }

    public function index()
    {
        $query = User::query()
            ->where('tenant_id', auth()->user()->tenant_id);

        return DataTables::of($query)
            ->editColumn('sequential', fn($user) => str_pad($user->sequential, 5, '0', STR_PAD_LEFT))
            ->editColumn('active', fn($user) => view('partials.active', ['active' => $user->active]))
            ->addColumn('actions', fn($user) => view('partials.actions', [
                'id' => $user->id,
                'sequential' => $user->sequential,
                'entity' => 'users',
            ]))
            ->make(true);
    }

    public function store(UserRequest $request)
    {
        $data = $request->validated();

        if (empty($data['password'])) {
            throw ValidationException::withMessages(['password' => 'O campo é obrigatório.']);
        }

        $data['active'] = $request->boolean('active');
        $data['tenant_id'] = auth()->user()->tenant_id;

        $user = User::create($data);

        return response()->json($user, 201);
    }

    public function show(User $user)
    {
        $this->authorizeTenantAccess($user);

        return response()->json($user);
    }

    public function update(UserRequest $request, User $user)
    {
        $this->authorizeTenantAccess($user);

        $data = $request->validated();
        $data['active'] = $request->boolean('active');

        if (empty($data['password'])) {
            unset($data['password']);
        }

        $user->update($data);

        return response()->json($user);
    }

    public function destroy(User $user)
    {
        // $this->authorizeTenantAccess($user);

        // $user->delete();

        // return response()->json(null, 204);

        return response()->json([
            'message' => 'Esta funcionalidade ainda nao foi implementada.'
        ], 501);
    }
}
