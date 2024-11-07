<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Yajra\DataTables\DataTables;

class UserController extends Controller
{
    public function search(Request $request)
    {
        $users = User::query()
            ->where('tenant_id', auth()->user()->tenant->id)
            ->where('name', 'like', "%{$request->input('search')}%")
            ->limit(10)
            ->get();

        $users = $users->map(function ($user) {
            return [
                'id' => $user->id,
                'text' => $user->name,
            ];
        });

        return response()->json($users);
    }

    public function index()
    {
        $query = User::query()
            ->where('tenant_id', auth()->user()->tenant->id);

        return DataTables::make($query)
            ->editColumn('sequential', function ($user) {
                return str_pad($user->sequential, 5, '0', STR_PAD_LEFT);
            })
            ->editColumn('active', function ($user) {
                return view('partials.active', [
                    'active' => $user->active,
                ]);
            })
            ->addColumn('actions', function ($user) {
                return view('partials.actions', [
                    'id' => $user->id,
                    'entity' => 'users',
                ]);
            })
            ->make(true);
    }

    public function store(UserRequest $request)
    {
        $data = $request->validated();

        if (empty($data['password'])) {
            throw ValidationException::withMessages(['password' => 'O campo é obrigatório.']);
        }

        $data['tenant_id'] = auth()->user()->tenant->id;
        $data['active'] = $request->has('active') && $request->input('active') === 'on' ? true : false;

        $user = User::create($data);

        return response()->json($user, 201);
    }

    public function show(User $user)
    {
        return response()->json($user);
    }

    public function update(UserRequest $request, User $user)
    {
        $data = $request->validated();
        $data['active'] = $request->has('active') && $request->input('active') === 'on' ? true : false;

        if (empty($data['password'])) {
            unset($data['password']);
        }

        $user->update($data);

        return response()->json($user);
    }

    public function destroy(User $user)
    {
        $user->delete();

        return response()->json(null, 204);
    }
}
