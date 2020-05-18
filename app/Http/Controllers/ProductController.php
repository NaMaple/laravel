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
        $data = $request->post();
        $value = serialize($data);
        var_dump($name);
        Redis::set('user:1', $value);
        return Redis::get('user:1');
    }
}
