<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use Illuminate\Validation\Rule;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::query();

        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%$s%")
                    ->orWhere('phone', 'like', "%$s%");
            });
        }

        $customers = $query->latest()->paginate(10);

        if ($request->expectsJson()) {
            return response()->json($customers);
        }

        return view('pages.admin.customers.index', compact('customers'));
    }
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'phone' => [
                'required',
                'regex:/^[0-9]{10,15}$/',
                'unique:customers,phone',
            ],
            'email' => 'nullable|email|unique:customers,email',
        ]);

        Customer::create($data);

        return response()->json([
            'message' => 'Customer berhasil ditambahkan'
        ]);
    }
    public function update(Request $request, Customer $customer)
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'phone' => [
                'required',
                'regex:/^[0-9]{10,15}$/',
                Rule::unique('customers')->ignore($customer->id),
            ],
            'email' => [
                'nullable',
                'email',
                Rule::unique('customers')->ignore($customer->id),
            ],
        ]);

        $customer->update($data);

        return response()->json([
            'message' => 'Customer diperbarui'
        ]);
    }
    public function destroy(Customer $customer)
    {
        $customer->delete();

        return response()->json([
            'message' => 'Customer dihapus'
        ]);
    }
}
