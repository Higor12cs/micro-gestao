<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomerRequest;
use App\Models\Customer;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class CustomerController extends Controller
{
    public function search(Request $request)
    {
        $customers = Customer::query()
            ->where('tenant_id', auth()->user()->tenant->id)
            ->where(function ($query) use ($request) {
                $query->where('first_name', 'like', "%{$request->input('search')}%")
                    ->orWhere('last_name', 'like', "%{$request->input('search')}%")
                    ->orWhere('legal_name', 'like', "%{$request->input('search')}%");
            })
            ->limit(10)
            ->get();

        $customers = $customers->map(function ($customer) {
            return [
                'id' => $customer->id,
                'text' => $customer->first_name,
            ];
        });

        return response()->json($customers);
    }

    public function index()
    {
        $query = Customer::where('tenant_id', auth()->user()->tenant->id);

        return DataTables::make($query)
            ->editColumn('sequential', function ($customer) {
                return str_pad($customer->sequential, 5, '0', STR_PAD_LEFT);
            })
            ->addColumn('actions', function ($customer) {
                return view('partials.actions', [
                    'id' => $customer->id,
                    'entity' => 'customers',
                ]);
            })
            ->make(true);
    }

    public function store(CustomerRequest $request)
    {
        $data = $request->validated();
        $data['tenant_id'] = auth()->user()->tenant->id;

        $customer = Customer::create($data);

        return response()->json($customer, 201);
    }

    public function show(Customer $customer)
    {
        return response()->json($customer);
    }

    public function update(CustomerRequest $request, Customer $customer)
    {
        $customer->update($request->validated());

        return response()->json($customer);
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();

        return response()->json(null, 204);
    }
}
