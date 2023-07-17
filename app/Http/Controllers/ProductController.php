<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    //
    // $table->id();
    // $table->string('name', 100)->unique()->nullable();
    // $table->string('slug', 100)->unique()->nullable();
    // $table->text('description')->nullable();
    // $table->string('image', 100)->nullable();
    // $table->decimal('price', 8, 2)->nullable();
    // $table->enum('status', ['active', 'inactive'])->default('active');
    // // category just a string
    // $table->string('category', 100)->nullable();
    // $table->timestamps();

    // this is an api controller

    // return all products with pagination and search
    public function index(Request $request)
    {
        $products = Product::orderBy('id', 'desc')->paginate(10);
        if ($request->has('search')) {
            $products = Product::where('name', 'like', '%' . $request->search . '%')
                ->orWhere('description', 'like', '%' . $request->search . '%')
                ->orWhere('category', 'like', '%' . $request->search . '%')
                ->orderBy('id', 'desc')
                ->paginate(10);
        }

        return $this->apiResponse($products);
    }


    // return single product
    public function show($id)
    {
        $product = Product::find($id);
        if (!$product) {
            return $this->apiResponse(null, 'Product not found', 404);
        }
        return $this->apiResponse($product);
    }

    // create new product
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:products|max:100',
            'description' => 'required',
            'price' => 'required|numeric',
            'category' => 'required',
            'status' => 'required|in:active,inactive',
            'image' => 'required|image|mimes:jpg,png,jpeg|max:2048'
        ]);

        $product = new Product();
        $product->name = $request->name;
        $product->slug = $this->slugify($request->name);
        $product->description = $request->description;
        $product->price = $request->price;
        $product->category = $request->category;
        $product->status = $request->status;

        // upload image
        $image = $request->file('image');
        $image_name = time() . '.' . $image->extension();
        $image->move(public_path('images'), $image_name);
        $product->image = $image_name;

        $product->save();

        return $this->apiResponse($product, null, 201);
    }

    // update product
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|max:100|unique:products,name,' . $id,
            'description' => 'required',
            'price' => 'required|numeric',
            'category' => 'required',
            'status' => 'required|in:active,inactive',
            'image' => 'nullable|image|mimes:jpg,png,jpeg|max:2048'
        ]);

        $product = Product::find($id);
        if (!$product) {
            return $this->apiResponse(null, 'Product not found', 404);
        }

        $product->name = $request->name;
        $product->slug = $this->slugify($request->name);
        $product->description = $request->description;
        $product->price = $request->price;
        $product->category = $request->category;
        $product->status = $request->status;

        // upload image
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $image_name = time() . '.' . $image->extension();
            $image->move(public_path('images'), $image_name);
            $product->image = $image_name;
        }

        $product->save();

        return $this->apiResponse($product, null, 200);
    }

    // delete product
    public function destroy($id)
    {
        $product = Product::find($id);
        if (!$product) {
            return $this->apiResponse(null, 'Product not found', 404);
        }

        $product->delete();

        return $this->apiResponse(true, null, 200);
    }

}
