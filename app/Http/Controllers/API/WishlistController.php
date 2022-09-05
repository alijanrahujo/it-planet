<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Advertise;
use App\Models\Banner;
use App\Models\Blog;
use App\Models\Brand;
use App\Models\Cart;
use App\Models\Category;
use App\Models\Childcategory;
use App\Classes\GeniusMailer;
use App\Models\Compare;
use App\Models\Conversation;
use App\Models\Counter;
use App\Models\Coupon;
use App\Models\Currency;
use App\Models\Faq;
use App\Models\Generalsetting;
use App\Models\Image;
use App\Models\Language;
use App\Models\Message;
use App\Models\VendorSubscription;
use App\Models\Notification;
use App\Models\FrenchiseNotification;
use App\Models\Order;
use App\Models\Page;
use App\Models\Pagesetting;
use App\Models\PaymentGateway;
use App\Models\Pickup;
use App\Models\Portfolio;
use App\Models\Product;
use App\Models\ProductClick;
use App\Models\Review;
use App\Models\Service;
use App\Models\Wishlist;
use App\Models\Customer;
use App\Models\Subscriber;
use App\Models\User;
use App\Models\UserSubscription;
use App\Models\UserNotification;
use App\Models\VendorOrder;
use Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;
use InvalidArgumentException;
use Markury\MarkuryPost;
use Illuminate\Support\Str;

use DB;

class WishlistController extends Controller {
  public function fetch(Request $request)
  {
    $fav_list = [];
    $user = Auth::user();
    $fav_list = Wishlist::all()->where('user_id','=',$user->id);
    $products = [];
    foreach ($fav_list as $fav){
      $prodcut = Product::all()->where('id','=',$fav->product_id)->first();
      array_push($products, $prodcut);
    }

    return response()->json([
      'status_code' => 200,
      'status' => 1,
      'data' => $products,
    ]);
  }

  public function create(Request $request)
  {
    $fav_list = [];
    $user = Auth::user();

    $payload = $request->validate([
      'product_id' => 'required',
    ]);
    $payload['user_id'] = $user->id;
  
    // check if already exist
    $fav_list = Wishlist::all()->where('user_id','=',$user->id)->where('product_id','=',$payload['product_id']);
    if(count($fav_list) > 0){
      return response()->json([
        'status_code' => 200,
        'status' => 0,
        'message' => 'This product already exist in favourite!'
      ]);
    }
    
    $a = Wishlist::create($payload);
    $fav_list = Wishlist::all()->where('user_id','=',$user->id);

    $products = [];
    foreach ($fav_list as $fav){
      $prodcut = Product::all()->where('id','=',$fav->product_id)->first();
      array_push($products, $prodcut);
    }

    return response()->json([
      'status_code' => 200,
      'status' => 1,
      'message' => 'Product is added in favourite!',
      'data' => $products,
    ]);
  }

  public function delete(Request $request)
  {
    $user = Auth::user();
    $payload = $request->validate([ 'product_id' => 'required' ]);
    $fav_product = Wishlist::all()->where('product_id','=',$payload['product_id'])->where('user_id','=',$user->id)->first();
    $wish = Wishlist::findOrFail($fav_product->id);
    $wish->delete();
    if(!$wish)
      return response()->json([
        'status_code' => 200,
        'status' => 0,
        'message' => 'This product not found in faourite list!',
      ]);

    $fav_list = Wishlist::all()->where('user_id','=',$user->id);
    $products = [];
    foreach ($fav_list as $fav){
      $prodcut = Product::all()->where('id','=',$fav->product_id)->first();
      array_push($products, $prodcut);
    }

    return response()->json([
      'status_code' => 200,
      'status' => 1,
      'message' => 'Product delete successfully from favourite!',
      'data' => $products,
    ]);
  }
}
