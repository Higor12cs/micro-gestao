<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomerRequest;
use App\Models\Customer;
use App\Traits\TenantAuthorization;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class CustomerController extends Controller
{
    use TenantAuthorization;

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function search(Request $request)
    {
        $searchTerm = $request->input('search', '');

        $customers = Customer::query()
            ->where(function ($query) use ($searchTerm) {
                $query->where('first_name', 'like', "%{$searchTerm}%")
                    ->orWhere('last_name', 'like', "%{$searchTerm}%")
                    ->orWhere('legal_name', 'like', "%{$searchTerm}%");
            })
            ->limit(10)
            ->get(['id', 'first_name']);

        return response()->json(
            $customers->map(fn($customer) => ['id' => $customer->id, 'text' => $customer->first_name])
        );
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Customer::query();

            return DataTables::of($query)
                ->editColumn('sequential', fn($customer) => str_pad($customer->sequential, 5, '0', STR_PAD_LEFT))
                ->addColumn('actions', fn($customer) => view('partials.actions', [
                    'id' => $customer->id,
                    'sequential' => $customer->sequential,
                    'entity' => 'customers',
                ]))
                ->make(true);
        }

        return view('customers.index');
    }

    public function store(CustomerRequest $request)
    {
        $customer = Customer::create($request->validated());

        return response()->json($customer, 201);
    }

    public function show(Customer $customer)
    {
        $this->authorizeTenantAccess($customer);

        return response()->json($customer);
    }

    public function update(CustomerRequest $request, Customer $customer)
    {
        $this->authorizeTenantAccess($customer);

        $customer->update($request->validated());

        return response()->json($customer);
    }

    public function destroy(Customer $customer)
    {
        $this->authorizeTenantAccess($customer);

        if ($customer->orders()->exists()) {
            return response()->json(['message' => 'Este cliente possui registros associados e não pode ser excluído.'], 400);
        }

        $customer->delete();

        return response()->json(null, 204);
    }
}
