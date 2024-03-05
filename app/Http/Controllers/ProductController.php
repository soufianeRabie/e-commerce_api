<?php

namespace App\Http\Controllers;

use App\Enums\UserRoleEnum;
use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    public function index(Request $request)
    {

        try {
            $products = Product::latest()->get();
            return response()->json(['products' =>ProductResource::collection($products)]);
        }catch (\Exception $e){
        return response()->json(["error" =>"you dont have permission to access this operation"]);
        }


    }

    public function store(ProductRequest $request)
    {
      $user = Auth::user();
      if($user)
      {
          $formField = $this->GetProductColumn($request);
          try {
              $formField['image'] = $request->file('image')->store("images" , "public");
          }catch (\Exception $e) {
              Log::error($e);
              // Return a generic error response
              return response()->json(['message' => 'Error uploading image'], 500);
          }
          Product::create($formField);
          return response()->json(true);
      }
      return response()->json('Unauthorized');
    }

    public function show(Product $product)
    {
        //
    }

    public function update(ProductRequest $request, Product $product)
    {

        if(Auth::user())
        {
            $formField['title'] = $request->input('title')??$product->title;
            $formField['description'] = $request->input('description')??$product->description;
            $formField['isSold'] = $request->input('isSold')==="true"??$product->isSold;
            $formField['rating'] = $request->input('rating')??$product->rating;
            $formField['oldPrice'] = $request->input('isSold')==="true"? $request->input('oldPrice'):null;
            $formField['price'] = $request->input('price')??$product->price;

            if( $request->file('image'))
            {
                try {
                    $formField['image'] = $request->file('image')->store("images" , "public")??$product->image;
                }catch (\Exception $e) {
                    Log::error($e);
                    // Return a generic error response
                    return response()->json(['message' => 'Error uploading image'], 500);
                }
            }

            $product->fill($formField)->save();

            return response()->json(true);
        }

     return response()->json(false);
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
     if( Auth::user())
     {
        $deleteProduct =  $product->delete();
        if($deleteProduct)
        {
            return response()->json(true);
        }

         return response()->json(false);
     }
     return response()->json(false);
    }

    private function GetProductColumn($request)
    {
        $formField['isSold'] = $request->boolean('isSold') ;
        $formField['title'] = $request->string('title') ;
        $formField['description'] = $request-> string('description') ;
        $formField['oldPrice'] =$formField['isSold']? $request-> integer('oldPrice') :null;
        $formField['image'] = $request-> file('image');
        $formField['price'] = $request-> integer('price');
        $formField['rating'] = $request-> integer('rating');

        return $formField;
    }
}
