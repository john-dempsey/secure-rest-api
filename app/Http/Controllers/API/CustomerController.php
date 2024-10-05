<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Http\Resources\CustomerResource;
use App\Models\Customer;

use Validator;

class CustomerController extends BaseController
{
    public function index(): JsonResponse
    {
        $customers = Customer::all();
    
        return $this->sendResponse(CustomerResource::collection($customers), 'Customers retrieved successfully.');
    }

    public function store(Request $request): JsonResponse
    {
        $input = $request->all();
   
        $validator = Validator::make($input, [
            'name' => 'required',
            'address' => 'required',
            'phone' => 'required',
            'email' => 'required|email'
        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }
   
        $customer = Customer::create($input);
   
        return $this->sendResponse(new CustomerResource($customer), 'Customer created successfully.');
    }

    public function show(string $id): JsonResponse
    {
        $customer = Customer::find($id);
  
        if (is_null($customer)) {
            return $this->sendError('Customer not found.');
        }
   
        return $this->sendResponse(new CustomerResource($customer), 'Customer retrieved successfully.');
    }

    public function update(Request $request, Customer $customer): JsonResponse
    {
        $input = $request->all();
   
        $validator = Validator::make($input, [
            'name' => 'required',
            'address' => 'required',
            'phone' => 'required',
            'email' => 'required|email'
        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }
   
        $customer->name = $input['name'];
        $customer->address = $input['address'];
        $customer->phone = $input['phone'];
        $customer->email = $input['email'];
        $customer->save();
   
        return $this->sendResponse(new CustomerResource($customer), 'Customer updated successfully.');
    }

    public function destroy(Customer $customer): JsonResponse
    {
        $customer->delete();

        return $this->sendResponse([], 'Customer deleted successfully.');
    }
}
