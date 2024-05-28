<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Models\Feedback;
use App\Models\Product;

class FeedbackController extends Controller
{
    public function feedback(Request $request)
    {
        // if($request->has('image')){
        //     $image=$request->image;
        // //    $extension= $image->getClientOriginalExtension();
        // $name =time().'.'. $image->getClientOriginalExtension(); 
        // $path =public_path('upload');
        // $image->move($path,$name);
        // return response()->json(['data'=>'','message'=>'image uploaded successfully','status'=>true],200);
        // }

        $validator = Validator::make($request->all(), [
            'name' => 'required|regex:/^[A-Za-z\s]+$/',
            'image' => 'nullable|image',
            'comment' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => '',
                'message' => $validator->errors(),
                'status' => false,
            ], 400);
        }
        $url = null;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $name = time() . '.' . $image->getClientOriginalName();
            $filepath = 'comment/' . $name;
            Storage::disk('s3')->put($filepath, file_get_contents($image));
            $url = Storage::disk('s3')->url($filepath);
        }

        $feedback = new Feedback();
        $feedback->name = $request->name;
        $feedback->image = $url;
        $feedback->comment = $request->comment;
        $feedback->save();
        return response()->json(['data' => '', 'message' => 'save successfully', 'status' => true], 200);
    }

    public function show(Request $request)
    {
        $feedbacks = Feedback::select('name', 'image', 'comment')->get();
        if ($feedbacks->isEmpty()) {
            return response()->json([
                'data' => '',
                'message' => 'No feedback records found',
                'status' => false,
            ], 404);
        }
        return response()->json([
            'data' => $feedbacks,
            'message' => 'Feedback records retrieved successfully',
            'status' => true,
        ], 200);
    }

    public function saveProduct(Request $request)
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
            return response()->json(['data' => '', 'message' => 'product detail save successfully', 'status' => true], 200);
        }
    }

    public function showProduct(Request $request)
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
}
