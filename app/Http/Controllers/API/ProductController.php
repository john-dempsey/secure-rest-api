<?php
   
namespace App\Http\Controllers\API;
   
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Http\Resources\ProductResource;
use App\Models\Product;

use Validator;
   
class ProductController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(): JsonResponse
    {
        if (Gate::denies('viewAny', Product::class)) {
            return $this->sendError(
                'Permission denied.', 
                ['You are not authorized to perform this action.'],
                403
            );
        }
        $products = Product::all();
    
        return $this->sendResponse(ProductResource::collection($products), 'Products retrieved successfully.');
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request): JsonResponse
    {
        if (Gate::denies('create', Product::class)) {
            return $this->sendError(
                'Permission denied.', 
                ['You are not authorized to perform this action.'],
                403
            );
        }
        
        $input = $request->all();
   
        $validator = Validator::make($input, [
            'name' => 'required',
            'description' => 'required',
            'price' => 'required|numeric|between:0,999.99',
            'supplier_id' => 'required|exists:suppliers,id'
        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }
   
        $product = Product::create($input);
   
        return $this->sendResponse(new ProductResource($product), 'Product created successfully.');
    } 
   
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id): JsonResponse
    {
        $product = Product::find($id);
        if (is_null($product)) {
            return $this->sendError('Product not found.');
        }

        if (Gate::denies('view', $product)) {
            return $this->sendError(
                'Permission denied.', 
                ['You are not authorized to perform this action.'],
                403
            );
        }
        
        return $this->sendResponse(new ProductResource($product), 'Product retrieved successfully.');
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product): JsonResponse
    {
        if (Gate::denies('update', $product)) {
            return $this->sendError(
                'Permission denied.', 
                ['You are not authorized to perform this action.'],
                403
            );
        }

        $input = $request->all();
        $validator = Validator::make($input, [
            'name' => 'required',
            'description' => 'required',
            'price' => 'required|numeric|between:0,999.99',
            'supplier_id' => 'required|exists:suppliers,id'
        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }
   
        $product->name = $input['name'];
        $product->description = $input['description'];
        $product->price = $input['price'];
        $product->supplier_id = $input['supplier_id'];
        $product->save();
   
        return $this->sendResponse(new ProductResource($product), 'Product updated successfully.');
    }
   
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product): JsonResponse
    {
        if (Gate::denies('delete', $product)) {
            return $this->sendError(
                'Permission denied.', 
                ['You are not authorized to perform this action.'],
                403
            );
        }
        $product->delete();
   
        return $this->sendResponse([], 'Product deleted successfully.');
    }
}
