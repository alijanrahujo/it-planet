<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use  HasApiTokens, Notifiable;

    // use HasApiTokens, Notifiable;

    // protected $guard = 'user';

    protected $fillable = [
        'name', 'frenchise_id', 'photo', 'zip', 'residency', 'province', 'city', 'password_api', 'address',
        'phone', 'fax', 'email', 'password', 'shop_name', 'owner_name', 'shop_number', 'shop_address', 'vendor_city', 'reg_number',
        'shop_message', 'is_vendor', 'shop_details', 'f_url', 'g_url', 't_url', 'i_url', 'l_url', 'f_check', 'g_check', 't_check', 'i_check',
        'l_check', 'shipping_cost', 'affilate_code', 'brand', 'top', 'top_by_category', 'nav_shop', 'coming_shop', 'v_type', 'top_rated', 'logo', 'gif', 'gif1', 'gif2', 'sale_tax'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    //protected $remember_token = false;
    //    public static function where(string $string, string $string1, string $string2)
    //    {
    //    }

    public function orders()
    {
        return $this->hasMany('App\Models\Order');
    }
    public function vendororders()
    {
        return $this->hasMany('App\Models\Vendororder');
    }
    public function comments()
    {
        return $this->hasMany('App\Models\Comment');
    }
    public function replies()
    {
        return $this->hasMany('App\Models\Reply');
    }
    public function subreplies()
    {
        return $this->hasMany('App\Models\SubReply');
    }
    public function reviews()
    {
        return $this->hasMany('App\Models\Review');
    }
    public function wishlists()
    {
        return $this->hasMany('App\Models\Wishlist');
    }
    public function favorites()
    {
        return $this->hasMany('App\Models\FavoriteSeller');
    }
    public function socialProviders()
    {
        return $this->hasMany(SocialProvider::class);
    }
    public function products()
    {
        return $this->hasMany('App\Models\Product');
    }
    public function sliders()
    {
        return $this->hasMany('App\Models\VendorSlider');
    }
    public function IsVendor()
    {
        if ($this->is_vendor == 2) {
            return true;
        }
        return false;
    }

    public function withdraws()
    {
        return $this->hasMany('App\Models\Withdraw');
    }
    public function senders()
    {
        return $this->hasMany('App\Models\Conversation', 'sent_user');
    }
    public function recievers()
    {
        return $this->hasMany('App\Models\Conversation', 'recieved_user');
    }
    public function conversations()
    {
        return $this->hasMany('App\Models\AdminUserConversation');
    }
    public function notifications()
    {
        return $this->hasMany('App\Models\Notification');
    }
    public function notivications()
    {
        return $this->hasMany('App\Models\Notification', 'vendor_id');
    }
    public function subscribes()
    {
        return $this->hasMany('App\Models\UserSubscription');
    }

    public function frenchise()
    {
        return $this->belongsTo('App\Models\Frenchise', 'frenchise_id');
    }

    public  function shopratings($shopid)
    {
        $productid = Product::where('user_id', $shopid)->get()->pluck('id');
        $stars = Review::whereIn('product_id', $productid)->avg('rating');
        $ratings = number_format((float)$stars, 1, '.', '') * 20;
        return $ratings;
    }
}