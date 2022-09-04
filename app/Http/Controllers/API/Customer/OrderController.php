<?php

namespace App\Http\Controllers\API\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Generalsetting;
use Illuminate\Support\Str;
use App\Models\Order;
use App\Models\Currency;
use App\Models\Notification;
use App\Models\FrenchiseNotification;
use App\Models\UserNotification;
use App\Models\Coupon;
use App\Models\Vendororder;

class OrderController extends Controller
{
    public function order(Request $request)
    {
        $totalQty = 0;
        $shipping_service = $request->shipping_service;
        $curr = Currency::where('is_default', '=', 1)->first();
        $shippment_charges = 0;
        
        $cart = array();
        foreach ($request->items as $item) 
        {            
          $id = $item['id'];
          $qty = $item['qty'];
          $size = $item['size'];
          $color = $item['color'];

          $totalQty +=$qty;
        
          $prod = Product::where('id', '=', $id)->first(['id', 'user_id', 'name', 'photo', 'size', 'color', 'cprice', 'stock', 'type', 'file', 'link', 'license', 'license_qty', 'measure']);
          $shippment_charges += $prod['shipping_charges']??0;

          if ($prod->license_qty != null) {
              $lcheck = 1;
              $details1 = explode(',', $prod->license_qty);
              foreach ($details1 as $ttl => $dtl) {
                  if ($dtl < 1) {
                      $lcheck = 0;
                  } else {
                      $lcheck = 1;
                      break;
                  }
              }
              if ($lcheck == 0) {
                  return 0;
              }
          }
          if ($prod->user_id != 0) {
              $gs = Generalsetting::findOrFail(1);
              $price = $prod->cprice + $gs->fixed_commission + ($prod->cprice / 100) * $gs->percentage_commission;
              $prod->cprice = round($price, 2);
          }

          $cart[$prod->id] = 
          [
            "qty" => $qty,
            "size" => $size,
            "color" => $color,
            "stock" => ($prod->stock >0)?$prod->stock-$qty:"null",
            "price" => $prod->cprice*$qty,
            "item" => $prod,
            "license" => "",
            "shipping_charges" => 0
          ];
          
        }


        $order = new Order;
        // return $shipping_service->id;
        $item_name = $gs->title . " Order";
        $item_number = str::random(4) . time();
        $order['user_id'] = auth('sanctum')->user()->id;

        $order['cart'] = utf8_encode(gzcompress(serialize($cart), 9));
        $order['totalQty'] = $totalQty;
        //$order['pay_amount'] = round(($request->total) / $curr->value, 2);
        $order['method'] = "pending";
        $order['shipping'] = $request->shipping;
        $order['shipping_service'] = $shipping_service['id']??'';
        $order['pickup_location'] = $request->pickup_location;
        $order['customer_email'] = $request->email;
        $order['customer_name'] = $request->name;
        $order['shipping_cost'] = round($gs->ship * $curr->value,2);
        $order['shipping_cost'] = $shippment_charges;
        $order['tax'] = $gs->tax;
        $order['customer_phone'] = $request->phone;
        $order['order_number'] = str::random(4) . time();
        $order['customer_address'] = $request->address;
        $order['customer_country'] = $request->country;
        $order['customer_city'] = $request->city;
        $order['province'] = $request->province;
        $order['customer_zip'] = $request->zip;
        $order['shipping_email'] = $request->shipping_email??$request->email;
        $order['shipping_name'] = $request->shipping_name??$request->name;
        $order['shipping_phone'] = $request->shipping_phone??$request->phone;
        $order['shipping_address'] = $request->shipping_address??$request->address;
        $order['shipping_country'] = $request->shipping_country??$request->country;
        $order['shipping_city'] = $request->shipping_city??$request->city;
        $order['shipping_zip'] = $request->shipping_zip??$request->zip;
        $order['order_note'] = $request->order_notes;
        $order['coupon_code'] = $request->coupon_code;
        $order['coupon_discount'] = $request->coupon_discount;
        $order['dp'] = $request->dp??0;
        $order['payment_status'] = "Pending";
        $order['currency_sign'] = $curr->sign;
        $order['currency_value'] = $curr->value;

        // if (Session::has('affilate')) {
        //     $val = $request->total / 100;
        //     $sub = $val * $gs->affilate_charge;
        //     $user = User::findOrFail(Session::get('affilate'));
        //     $user->affilate_income = $sub;
        //     $user->update();
        //     $order['affilate_user'] = $user->name;
        //     $order['affilate_charge'] = $sub;
        // }
        $order->save();

        $notification = new Notification;
        $notification->order_id = $order->id;
        $notification->save();
        $frenchisenotification = new FrenchiseNotification;
        $frenchisenotification->order_id = $order->id;
        $frenchisenotification->save();
        $usernotification = new UserNotification;
        $usernotification->order_id = $order->id;
        $usernotification->save();
        if ($request->coupon_id != "") {
            $coupon = Coupon::findOrFail($request->coupon_id);
            $coupon->used++;
            if ($coupon->times != null) {
                $i = (int)$coupon->times;
                $i--;
                $coupon->times = (string)$i;
            }
            $coupon->update();
        }
        foreach ($cart as $prod) {
            $x = (string)$prod['stock'];
            if ($x != null) {

                $product = Product::findOrFail($prod['item']['id']);
                $product->stock =  $prod['stock'];
                $product->update();
                if ($product->stock <= 5) {
                    $notification = new Notification;
                    $notification->product_id = $product->id;
                    $notification->save();
                    $usernotification = new UserNotification;
                    $usernotification->product_id = $product->id;
                    $usernotification->save();
                }
            }
        }
        foreach ($cart as $prod) {
            if ($prod['item']['user_id'] != 0) {
                $vorder =  new Vendororder;
                $vorder->order_id = $order->id;
                $vorder->item_id = $prod['item']['id'];
                $vorder->shipping_charges = $prod['shipping_charges'];
                $vorder->user_id = $prod['item']['user_id'];
                $vorder->qty = $prod['qty'];
                $vorder->price = $prod['price'];
                $vorder->order_number = $order->order_number;
                $vorder->save();
            }
        }

        return response()->json([
            'status_code' => 200,
            'status' => 1,
            'message' => 'Successfully added to Order',
        ]);  
    }
}