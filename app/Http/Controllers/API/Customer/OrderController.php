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
use App\Models\State;
use App\Models\Country;

class OrderController extends Controller
{
    public function order(Request $request)
    {
        if(!auth('sanctum')->check())
        {
            return response()->json([
                'status_code' => 401,
                'status' => 1,
                'message' => 'Invalid Auth Token',
              ]);
        }

        $totalQty = 0;
        $paid_amount = 0;
        $shipping_service = $request->shipping_service;
        $curr = Currency::where('is_default', '=', 1)->first();
        $shippment_charges = 0;
        
        $temp_cart = array();
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

          $temp_cart[$prod->id] = 
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

          $paid_amount += $prod->cprice*$qty;

          
        }

        $cart = (object) array();
        $cart->items = $temp_cart; 
        
        $order = new Order;
        // return $shipping_service->id;
        $item_name = $gs->title . " Order";
        $item_number = str::random(4) . time();
        $order['user_id'] = auth('sanctum')->user()->id;

        $order['cart'] = utf8_encode(gzcompress(serialize($cart), 9));
        $order['totalQty'] = $totalQty;
        $order['pay_amount'] = round($paid_amount / $curr->value, 2);
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
        foreach ($cart->items as $prod) {
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
        foreach ($cart->items as $prod) {
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
            'order_id' => $order->id
        ]);  
    }

    public function payment(Request $request)
    {
        $id = $request->order_id;
        $payment_method = $request->payment_method;


        $order = Order::find($id);
        $states = State::all();
        $country = new Country;
        $countries = $country->get_countries();

        if($payment_method == "bank")
        {
            $data = array(
                "HS_MerchantId" => 3162,
                "HS_StoreId" => '011925',
                "HS_MerchantHash" => 'OUU362MB1uqOvabSg7KsREd15e+opQs5xXBRMsmPw/EuZIlEb1IyqYaLW6J1b44w',
                "HS_MerchantUsername" => 'abumub',
                "HS_MerchantPassword" => 'JVthGzAvw6ZvFzk4yqF7CA==',
                "HS_IsRedirectionRequest" => 1,
                "HS_ReturnURL" => route('alfapayment', $order->id),
                "HS_RequestHash" => "",
                "HS_ChannelId" => 1001,
                "HS_TransactionReferenceNumber" => $order->id,
            );

            $data = json_encode($data);

            $ch = curl_init('https://sandbox.bankalfalah.com/HS/HS/HS');
            # Setup request to send json via POST.
            
            curl_setopt( $ch, CURLOPT_POSTFIELDS, $data );
            curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
            # Return response instead of printing.
            curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
            # Send request.
            $result = json_decode(curl_exec($ch));
            curl_close($ch);
            # Print response.
            

            if(isset($result) && $result->success != 'false')
            {

                return response()->json([
                    'status_code' => 200,
                    'status' => 1,
                    'message' => 'Payment successfully processed',
                    'data' => $result,
                    'order' => $order = Order::find($id)
                ]);
            }
            else
            {

                return response()->json([
                    'status_code' => 402,
                    'status' => 0,
                    'message' => 'Payment can not process! API issue',
                    'data' => $result
                ]); 
            }
        }
        else if($payment_method == "cash on delivery")
        {
            Order::where('id',$id)->update(['method'=>"cash on delivery"]);
            return response()->json([
                'status_code' => 200,
                'status' => 1,
                'message' => 'Order cash on delivery successfully updated',
                'order' => $order = Order::find($id)
            ]);
        }
        else
        {
            return response()->json([
                'status_code' => 405,
                'status' => 0,
                'message' => 'Invalid Payment Method! use bank or cash on delivery',
            ]);
        }

        return response()->json([
            'status_code' => 400,
            'status' => 0,
            'message' => 'Bad Request',
        ]);
    }
}