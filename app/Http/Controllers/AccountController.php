<?php

namespace App\Http\Controllers;

use App\Http\Requests\AccountRequest;
use App\Models\Account;
use App\Traits\TenantAuthorization;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class AccountController extends Controller
{
    use TenantAuthorization;

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function search(Request $request)
    {
        $searchTerm = $request->input('search', '');

        $accounts = Account::query()
            ->where('name', 'like', "%{$searchTerm}%")
            ->limit(10)
            ->get(['id', 'name']);

        return response()->json(
            $accounts->map(fn ($account) => ['id' => $account->id, 'text' => $account->name])
        );
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Account::query();

            return DataTables::of($query)
                ->editColumn('sequential', fn ($account) => str_pad($account->sequential, 5, '0', STR_PAD_LEFT))
                ->editColumn('active', fn ($account) => view('partials.active', ['active' => $account->active]))
                ->addColumn('actions', fn ($account) => view('partials.actions', [
                    'id' => $account->id,
                    'sequential' => $account->sequential,
                    'entity' => 'accounts',
                ]))
                ->make(true);
        }

        return view('accounts.index');
    }

    public function store(AccountRequest $request)
    {
        $data = $request->validated();
        $data['active'] = $request->boolean('active');

        $account = Account::create($data);

        return response()->json($account, 201);
    }

    public function show(Account $account)
    {
        $this->authorizeTenantAccess($account);

        return response()->json($account);
    }

    public function update(AccountRequest $request, Account $account)
    {
        $this->authorizeTenantAccess($account);

        $data = $request->validated();
        $data['active'] = $request->boolean('active');

        $account->update($data);

        return response()->json($account);
    }

    public function destroy(Account $account)
    {
        $this->authorizeTenantAccess($account);

        $account->delete();

        return response()->json(null, 204);
    }
}
