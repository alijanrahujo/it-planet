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
        if(auth('sanctum')->check())
        {
            $product_id = $request->product_id;
            $product_qty = $request->product_qty;
            $user_id = auth('sanctum')->user()->id;

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
    public function getcart()
    {
        if(auth('sanctum')->check())
        {
            $user_id = auth('sanctum')->user()->id;
            $cart = CartApi::where('user_id',$user_id)->get();
            if($cart)
            {
                return response()->json([
                    'status_code' => 200,
                    'status' => 1,
                    'data' => $cart,
                ]);
            }
            else
            {
                return response()->json([
                    'status_code' => 200,
                    'status' => 1,
                    'message' => 'Cart is empty',
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

    public function delete_item($id)
    {
        if(auth('sanctum')->check())
        {
            $user_id = auth('sanctum')->user()->id;
            if(CartApi::where(['user_id'=>$user_id,'id'=>$id])->exists())
            {
                CartApi::where(['user_id'=>$user_id,'id'=>$id])->delete();
                
                return response()->json([
                    'status_code' => 200,
                    'status' => 1,
                    'message' => 'Successfully deleted from cart',
                ]);
            }
            else
            {
                return response()->json([
                    'status_code' => 200,
                    'status' => 1,
                    'message' => 'Item Not Found',
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
