<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::select('id', 'name', 'image', 'description', 'price')->get();
        if ($products->isEmpty()) {
            return response()->json([
                'data' => '',
                'message' => 'No product found',
                'status' => false,
            ], 404);
        }
        return response()->json([
            'data' => $products,
            'message' => 'Product retrieved successfully',
            'status' => true,
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'bail|required|regex:/^[A-Za-z\s]+$/',
            'image' => 'bail|required|image',
            'description' => 'bail|required|string',
            'price' => 'bail|required|numeric'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => '',
                'message' => $validator->errors(),
                'status' => false,
            ], 400);
        }

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $name = time() . '.' . $image->getClientOriginalName();
            $filepath = 'product/' . $name;
            Storage::disk('s3')->put($filepath, file_get_contents($image));
            $url = Storage::disk('s3')->url($filepath);

            $product = new Product();
            $product->name = $request->name;
            $product->image = $url;
            $product->description = $request->description;
            $product->price = $request->price;
            $product->save();
            return response()->json(['data' => '', 'message' => 'Product details save successfully', 'status' => true], 200);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = Product::select('id', 'name', 'image', 'description', 'price')->find($id);
        if (!$product) {
            return response()->json([
                'data' => '',
                'message' => 'Product not found',
                'status' => false,
            ], 404);
        }

        return response()->json([
            'data' => $product,
            'message' => 'Product retrieved successfully',
            'status' => true,
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'bail|nullable|regex:/^[A-Za-z\s]+$/',
            'image' => 'bail|nullable|image',
            'description' => 'bail|nullable|string',
            'price' => 'bail|nullable|numeric'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => '',
                'message' => $validator->errors(),
                'status' => false,
            ], 400);
        }

        $product = Product::find($id);
        if (!$product) {
            return response()->json([
                'data' => '',
                'message' => 'Product not found',
                'status' => false,
            ], 404);
        }

        if ($request->has('name')) {
            $product->name = $request->name;
        }

        if ($request->has('description')) {
            $product->description = $request->description;
        }
        if ($request->has('price')) {
            $product->price = $request->price;
        }

        if ($request->hasFile('image')) {
            // Delete the old image from S3
            if ($product->image) {
                $parsedUrl = parse_url($product->image);
                if (isset($parsedUrl['path'])) {
                    $oldFilepath = ltrim($parsedUrl['path'], '/');
                    Storage::disk('s3')->delete($oldFilepath);
                }
            }

            // Upload the new image to S3
            $image = $request->file('image');
            $name = time() . '.' . $image->getClientOriginalName();
            $filepath = 'product/' . $name;
            Storage::disk('s3')->put($filepath, file_get_contents($image));
            $url = Storage::disk('s3')->url($filepath);
            $product->image = $url;
        }

        $product->save();

        return response()->json([
            'data' => '',
            'message' => 'Product updated successfully',
            'status' => true,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json([
                'data' => '',
                'message' => 'Product not found',
                'status' => false,
            ], 404);
        }

        if ($product->image) {
            $parsedUrl = parse_url($product->image);
            if (isset($parsedUrl['path'])) {
                $oldFilepath = ltrim($parsedUrl['path'], '/');
                Storage::disk('s3')->delete($oldFilepath);
            }
        }

        $product->delete();

        return response()->json([
            'data' => '',
            'message' => 'Product deleted successfully',
            'status' => true,
        ], 200);
    }
}
