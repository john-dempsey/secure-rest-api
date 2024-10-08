<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Http\Resources\SupplierResource;
use App\Models\Supplier;

use Validator;

class SupplierController extends BaseController
{
    public function index(): JsonResponse
    {
        if (Gate::denies('viewAny', Supplier::class)) {
            return $this->sendError(
                'Permission denied.', 
                ['You are not authorized to perform this action.'],
                403
            );
        }
        $suppliers = Supplier::all();
    
        return $this->sendResponse(
            SupplierResource::collection($suppliers), 
            'Suppliers retrieved successfully.'
        );
    }

    public function store(Request $request): JsonResponse
    {
        if (Gate::denies('create', Supplier::class)) {
            return $this->sendError(
                'Permission denied.', 
                ['You are not authorized to perform this action.'],
                403
            );
        }
        $input = $request->all();
   
        $validator = Validator::make($input, [
            'name' => 'required',
            'address' => 'required',
            'phone' => 'required',
            'email' => 'required|email'
        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }
   
        $supplier = Supplier::create($input);
   
        return $this->sendResponse(new SupplierResource($supplier), 'Supplier created successfully.');
    }

    public function show(string $id): JsonResponse
    {
        $supplier = Supplier::find($id);
          if (is_null($supplier)) {
            return $this->sendError('Supplier not found.');
        }
   
        if (Gate::denies('view', $supplier)) {
            return $this->sendError(
                'Permission denied.', 
                ['You are not authorized to perform this action.'],
                403
            );
        }

        return $this->sendResponse(new SupplierResource($supplier), 'Supplier retrieved successfully.');
    }

    public function update(Request $request, Supplier $supplier): JsonResponse
    {
        if (Gate::denies('update', $supplier)) {
            return $this->sendError(
                'Permission denied.', 
                ['You are not authorized to perform this action.'],
                403
            );
        }
        $input = $request->all();
   
        $validator = Validator::make($input, [
            'name' => 'required',
            'address' => 'required',
            'phone' => 'required',
            'email' => 'required|email'
        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }
   
        $supplier->name = $input['name'];
        $supplier->address = $input['address'];
        $supplier->phone = $input['phone'];
        $supplier->email = $input['email'];
        $supplier->save();
   
        return $this->sendResponse(new SupplierResource($supplier), 'Supplier updated successfully.');
    }

    public function destroy(Supplier $supplier): JsonResponse
    {
        if (Gate::denies('delete', $supplier)) {
            return $this->sendError(
                'Permission denied.', 
                ['You are not authorized to perform this action.'],
                403
            );
        }
        $supplier->delete();

        return $this->sendResponse([], 'Supplier deleted successfully.');
    }
}
