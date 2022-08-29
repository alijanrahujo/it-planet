<?php

namespace App\Http\Controllers\API\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use App\Models\Product;

class CartController extends Controller
{
    public function add_to_cart(Request $request)
    {
        if(auth('sanctum')->check())
        {
            $product = Product::where('id',$request->product_id)->first();
            if($product)
            {
                $data = [
                    "product_id" => $request->product_id,
                    "product_qty" => $request->product_qty,
                    "user_id" => auth('sanctum')->user()->id
                ];
        
                return response()->json([
                    'status_code' => 200,
                    'status' => 1,
                    'message' => $data,
                ]);
            }
            else
            {
                return response()->json([
                    'status_code' => 404,
                    'status' => 1,
                    'message' => 'Product Not Found',
                ]);
            }
        }
        else
        {
            return response()->json([
                'status_code' => 401,
                'status' => 1,
                'message' => 'Login to add to cart',
              ]);
        }
    }
}
