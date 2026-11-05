<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Order;
use App\Http\Resources\OrderResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
class CustomerController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $q = $request->query('q', '');
        $customers = Customer::where('email', 'like', '%' . $q . '%')
            ->orWhere('name', 'like', '%' . $q . '%')
            ->orderBy('email')->limit(20)->get(['id','name','email']);
        return response()->json(['customers' => $customers]);
    }
    
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required','string','max:255'],
            'email' => ['required','email','max:255','unique:customers,email'],
        ]);
        $customer = Customer::create($validated);
        return response()->json(['customer' => $customer], 201);
    }

    public function orders(string $email): JsonResponse
    {
        $customer = Customer::where('email', $email)->first();
        if (!$customer) {
            return response()->json(['message' => 'Customer not found.'], 404);
        }
        $orders = $customer->orders()->with('orderItems.product')->latest()->get();
        return response()->json([
            'customer' => ['name' => $customer->name, 'email' => $customer->email],
            'orders' => OrderResource::collection($orders),
        ]);
    }

    public function orderHistoryPage(Customer $customer)
    {
        return redirect('/order-history');
    }

    public function orderDetailsPage(Customer $customer, Order $order)
    {
        if ($order->customer_id !== $customer->id) abort(404);
        $order->load('orderItems.product','customer');
        return view('order-details', compact('customer','order'));
    }
}
