<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\User;
use App\Models\Country;
use App\Models\Product;
use App\Models\Page;
use App\Models\State;
use App\Models\Slider;
use App\Models\Subcategory;
use App\Models\Review;


class FrontendController extends Controller
{
     public function category_products($id)
  {

   	  $cat = Category::where('id','=',$id)->first();
      $subcats=Subcategory::where('category_id','=',$id)->get();
      // $cat->image_url=asset('assets/images/').'/'.$cat->photo;

        $cats = $cat->products()->where('status','=',1)->orderBy('id','desc')->get();
        if(count($cats)==0)  
        {
         
          return response()->json([
            'status_code' => 500,
            'status' => 0,
            'message' => 'No product Found',
            'products' => null,
            'category' => $cat,
            'subcats' => $subcats,
            
          ]);
        }
        
        // foreach ($cats as $key) {

        //   $key->image_url=asset('assets/images/').'/'.$key->photo;
        // }
  
    return response()->json([
      'status_code' => 200,
      'status' => 1,
      'products'=>$cats,
      'category'=>$cat,
      'subcats'=>$subcats,

    ]);
       
  }

   public function categories(Request $request)
  {

     $category= Category::with(['subs'])->get();

      return response()->json([
        'status_code' => 200,
        'status' => 1,
        'categories' => $category,
      ]);
  }

   public function shops(Request $request)
  {

   	$shops= User::all();
    if(!$shops)
        {
            return response()->json([
                'status_code' => 500,
                'status' => 0,
                'message' => 'No category Found',
            ]);
        }

    return response()->json([
        'status_code' => 200,
        'status' => 1,
        'shops' => $shops,
      ]);
  }

   public function countries()
  {

  	$country = new Country;
   	$countries = $country->get_countries();

    return response()->json([
        'status_code' => 200,
        'status' => 1,
        'countries' => $countries,
      ]);
  }

   public function search($id)
  {
    
    $search = Product::where('status', '=', 1)->where('name', 'like', '%' . $id . '%')->get();

    return response()->json([
        'status_code' => 200,
        'status' => 1,
        'products' => $search,
      ]);
  
  }

  public function road_search($id)
  {

    $vendors = User::where('shop_address', 'like', "%{$id}%")->orWhere('shop_name', 'like', "%{$id}%")->orderBy('id', 'desc')->get();

    return response()->json([
        'status_code' => 200,
        'status' => 1,
        'countries' => $vendors,
      ]);
  }

  public function brands()
    {
        $brands = User::where('brand','1')->get();
        if(!$brands)
        {
            return response()->json([
                'status_code' => 500,
                'status' => 0,
                'message' => 'No category Found',
            ]);
        }

        return response()->json([
        'status_code' => 200,
        'status' => 1,
        'brands' => $brands,
      ]);
    }

    public function festivels()
    {
        $product = Product::where('status',1)->where('festival',1)->get();
        if(!$product)
        {
            return response()->json([
                'status_code' => 500,
                'status' => 0,
                'message' => 'No category Found',
            ]);
        }

        return response()->json([
            'status_code' => 200,
            'status' => 1,
            'festivels'=>$product,

        ]);
    }

    public function about()
    {
        $page = Page::where('slug','about')->first();
        
        return response()->json([
            'status_code' => 200,
            'status' => 1,
            'about'=>$page,

        ]);
    }

     public function groceries()
  {
    
    $groceries = Product::where('status', '=', 1)->where('category_id',13)->get();

    return response()->json([
        'status_code' => 200,
        'status' => 1,
        'groceries' => $groceries,
      ]);
  
  }

  public function advance_search()
    {
        $states = State::all();
        $country = new Country;
        $countries = $country->get_countries();
        $category= Category::all();


        return response()->json([
        'status_code' => 200,
        'status' => 1,
        'data' => ["categories"=>$category,"countries"=>$countries],
      ]);

    }

    public function productsearch(Request $request)
    {
        $min      = $request->min;
        $max      = $request->max;
        $name     = $request->name;
        $category = $request->category;

         $products = Product::query();
        
        if($name)
        {
          $products->orWhere('name', 'like' ,'%{$name}%');
        }
        if ($category) 
        {
          $products->orWhere('category_id',$category);
        }
        if($min && $max)
        {
          $products->whereBetween('cprice', [$min, $max]);
        }
        
        $products->get();


        return response()->json([
        'status_code' => 200,
        'status' => 1,
        'data' => ["products"=>$products],
      ]);   

    }

    public function sliders()
  {
    $sliders_obj = Slider::all();
    $sliders = [];
    $base_url = url('/') . '/assets/images/';
    foreach ($sliders_obj as $slider)
      array_push($sliders, $base_url . $slider->photo);

    return response()->json([
      'status_code' => 200,
      'status' => 1,
      'data' => $sliders
    ]);
  }

   public function featured_shops(Request $request)
  {
    $shops_list = User::all()->where('top_rated','=',1);
    $shops = [];
    foreach ($shops_list as $shop){
      array_push($shops, $shop);
    }

    return response()->json([
      'status_code' => 200,
      'status' => 1,
      'data' => $shops,
    ]);
  }

  public function hot_sale()
    {
      $hot_sale = Product::where('hot','==',1)->get();

      return response()->json([
        'status_code' => 200,
        'status' => 1,
        'data' => strip_tags($hot_sale),
      ]);

  }

  public function deals_of_the_day()
    {
      $deals_of_the_day = Product::where('deal_of_the_day','==',1)->get();

    return response()->json([
      'status_code' => 200,
      'status' => 1,
      'data' => $deals_of_the_day,
    ]);

    }

      public function shops_details(Request $request,$slug)
    {
      $string = str_replace('-', ' ', $slug);
      $vendor = User::all()->where('shop_name','=',$string)->where('is_vendor',2)->first();
      
      if($vendor){
      $sliders= $vendor->sliders;
      }

      if($vendor){
      $vprods = $vendor->products()->where('status','=',1)->orderBy('id','desc')->get();
      }

      if($vendor){
      $offers_of_day = $vendor->products()->where('status','=',1)->where('shop_status','=',2)->orderBy('id','desc')->limit(9)->with('reviews')->get();
      }

    
      if($vendor){
      $discount_items = $vendor->products()->where('status','=',1)->where('shop_status','=',1)->orderBy('id','desc')->limit(9)->with('reviews')->get();
      }

      if($vendor){
      $sale_items = $vendor->products()->where('status','=',1)->where('shop_status','=',0)->orderBy('id','desc')->limit(9)->with('reviews')->get();
      }

      if($vendor){
      $nearest_shops = User::where('frenchise_id','=',$vendor->frenchise_id)->inRandomOrder()->limit(12)->get();
  }
  
   if($vendor){   
      
    return response()->json([
      'status_code' => 200,
      'status' => 1,
      'data' => ["sliders"=>$sliders, "offers of the day"=>$offers_of_day, "discount items"=>$discount_items, "sale items"=>$sale_items, "nearest shops"=>$nearest_shops],
    ]);
  }else{

    return response()->json([
      'status_code' => 200,
      'status' => 1,
      'data' => [],
    ]);
  }

    }

     public function cities_shops(Request $request, $slug)
    {
        $sort = "";
        $reservedSymbols = ['-', '_', '+', '<', '>', '@', '(', ')', '~'];
        $searchTerm = str_replace($reservedSymbols, ' ', $slug);
        $searchValues = preg_split('/\s+/', $searchTerm, -1, PREG_SPLIT_NO_EMPTY);

        $vendors = User::where(function ($q) use ($searchValues) {
            foreach ($searchValues as $value) {
                $q->orWhere('city', 'like', "%{$value}%")->orWhere('shop_address', 'like', "%{$value}%");
            }
        })->orderBy('id', 'desc')->get();

      return response()->json([
      'status_code' => 200,
      'status' => 1,
      'data' => $vendors,
    ]);

        
    }




}
