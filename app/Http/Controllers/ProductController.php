<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Guest_basket;
use App\Models\Product;
use App\Models\product_images;
use App\Models\ProductImages;
use App\Models\User_basket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        try {
            $products = Product::with('images')->latest()->get();
            return response()->json(['products' => ProductResource::collection($products)]);
        } catch (\Exception $e) {
            return response()->json(["error" =>$e->getMessage()]);
        }
    }

    public function store(ProductRequest $request)
    {
        $user = Auth::user();
        if ($user) {
            $formField = $this->getProductColumns($request);

            $product = Product::create($formField);

            try {
                for ($i = 0; $i < $request->input('imagesLength'); $i++) {
                    $image = $request->file("image.$i")->store("images", "public");
                    product_images::create([
                        'product_id' => $product->id,
                        'image' => $image
                    ]);
                }
            } catch (\Exception $e) {
                return response()->json($e->getMessage());
            }

            $newProduct = $products = Product::with('images')->where('id', $product->id)->get();

            return response()->json(ProductResource::collection($newProduct));
        }

        return response()->json('Unauthorized');
    }

    public function update(ProductRequest $request, Product $product)
    {
        if (Auth::user()) {
            $formField = [
                'title' => $request->input('title', $product->title),
                'description' => $request->input('description', $product->description),
                'isSold' => $request->boolean('isSold', $product->isSold),
                'rating' => $request->input('rating', $product->rating),
                'oldPrice' => $request->boolean('isSold', $product->isSold) ? $request->input('oldPrice') : null,
                'price' => $request->input('price', $product->price),
                'quantity' => $request->input('quantity', $product->quantity)
            ];

           if($request->hasFile('image.*'))
           {
               try {
                   product_images::where('product_id', $product->id)->delete();
                   for ($i = 0; $i < $request->input('imagesLength'); $i++) {
                       $image = $request->file("image.$i")->store("images", "public");
                       product_images::create([
                           'product_id' => $product->id,
                           'image' => $image
                       ]);
                   }
               } catch (\Exception $e) {
                   return response()->json($e->getMessage());
               }
           }

            $product->fill($formField)->save();

            $products = Product::with('images')->where('id', $product->id)->get();
            return response()->json(["product" => ProductResource::collection($products)]);
        }

        return response()->json(false);
    }

    public function destroy(Product $product)
    {
        if (Auth::user()) {
            $deleteProduct = $product->delete();
            if ($deleteProduct) {
                User_basket::where('product_id', $product->id)->delete();
                Guest_basket::where('product_id', $product->id)->delete();
                return response()->json(true);
            }
            return response()->json(false);
        }
        return response()->json(false);
    }

    private function getProductColumns($request)
    {
        return [
            'isSold' => $request->boolean('isSold'),
            'title' => $request->string('title'),
            'description' => $request->string('description'),
            'oldPrice' => $request->boolean('isSold') ? $request->integer('oldPrice') : null,
            'price' => $request->integer('price'),
            'rating' => $request->integer('rating'),
            'quantity' => $request->integer('quantity')
        ];
    }
}
