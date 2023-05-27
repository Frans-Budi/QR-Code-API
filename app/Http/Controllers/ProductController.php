<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\Product;
use Error;
use Exception;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $data = Product::all();

        return $this->success($data);
    }

    public function show($code)
    {
        $data = Product::where("code", $code)->firstOr(function () {
            return false;
        });

        if ($data) {
            return $this->success($data);
        } else {
            return $this->error("Data tidak ditemukan!");
        }
    }

    public function store(ProductRequest $request)
    {
        $data = $request->validated();
        // dd($data);

        $product = Product::create($data);

        return $this->success($product);
    }

    public function update(Request $request, $id)
    {
        $rules = [
            "code" => "required|numeric|max_digits:10",
            "name" => "required|string|max:255",
            "quantity" => "required|integer",
        ];
        $product = Product::findOrFail($id);

        if ($product["code"] != $request["code"]) {
            $rules["code"] =
                "required|numeric|unique:products,code|max_digits:10";
        }

        $validatedData = $request->validate($rules);

        // dd($validatedData);

        $product->update($validatedData);

        return $this->success($product);
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return $this->success($product);
    }
}
