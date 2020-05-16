<?php

namespace App\Http\Controllers;

use App\Product;
use App\Http\Resources\Product as ProductResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class ProductController extends Controller
{
    public function show ($id)
    {
        return new ProductResource(Product::find($id));
    }

    public function add(Request $request) {
        $name = $request->input('name');
        var_dump($name);
        Redis::set('name', $name);
        return Redis::get('name');
    }
}
