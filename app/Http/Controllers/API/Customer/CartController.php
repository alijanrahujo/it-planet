<?php

namespace App\Http\Controllers\API\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use App\Models\Product;
use App\Models\CartApi;

class CartController extends Controller
{
    public function add_to_cart(Request $request)
    {
        $product_id = $request->product_id;
        $product_qty = $request->product_qty;
        $user_id = auth('sanctum')->user()->id;

        if(auth('sanctum')->check())
        {
            $product = Product::where('id',$product_id)->first();
            if($product)
            {
                if(CartApi::where('user_id',$user_id)->where('product_id',$product_id)->exists())
                {

                    CartApi::where(['user_id'=>$user_id,'product_id'=>$product_id])->update(['product_qty'=>$product_qty]);
                    return response()->json([
                        'status_code' => 200,
                        'status' => 1,
                        'message' => 'Successfully updated to cart',
                    ]);
                }
                else
                {
                    $cartitem = new CartApi;
                    $cartitem->user_id = $user_id;
                    $cartitem->product_id = $product_id;
                    $cartitem->product_qty = $product_qty;
                    $cartitem->save();

                    return response()->json([
                        'status_code' => 200,
                        'status' => 1,
                        'message' => 'Successfully added to cart',
                    ]);    
                }
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
