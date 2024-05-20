<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Models\Feedback; 

class FeedbackController extends Controller
{
    public function imageUpload(Request $request){
// if($request->has('image')){
//     $image=$request->image;
// //    $extension= $image->getClientOriginalExtension();
// $name =time().'.'. $image->getClientOriginalExtension(); 
// $path =public_path('upload');
// $image->move($path,$name);
// return response()->json(['data'=>'','message'=>'image uploaded successfully','status'=>true],200);
// }

$validator=Validator::make($request->all(),[
    'name' => 'required|regex:/^[A-Za-z\s]+$/',
    'image'=>'required|image',
    'comment'=>'required|string'
]);

if ($validator->fails()) {
    return response()->json([
        'data' => '',
        'message' => $validator->errors(),
        'status' => false,
    ], 400);
}

if($request->hasFile('image')){
    $image=$request->file('image');
    $name =time().'.'. $image->getClientOriginalName(); 
    $filepath=$name;
    Storage::disk('s3')->put($filepath,file_get_contents($image));
    $url = Storage::disk('s3')->url($filepath);

    $feedback = new Feedback();
    $feedback->name=$request->name;
    $feedback->image = $url;
    $feedback->comment=$request->comment;
    $feedback->save();
    return response()->json(['data'=>'','message'=>'save successfully','status'=>true],200);
    }
    }
}
