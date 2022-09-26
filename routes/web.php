<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FrenchiseController;
use App\Http\Controllers\SubHeadOfficeController;
use App\Http\Controllers\FrenchiseOrderController;
use App\Http\Controllers\Auth\FrenchiseLoginController;
use App\Http\Controllers;
use App\Models\UserSubscription;

Route::get('user/notf', function () {
  return View::make('user_notf');
});
Route::get('order/notf', function () {
  return View::make('order_notf');
});
Route::get('order/notf1', function () {
  return View::make('order_notf1');
});
Route::get('product/notf', function () {
  return View::make('product_notf');
});
Route::get('product/notf1', function () {
  return View::make('product_notf1');
});
Route::get('conv/notf', function () {
  return View::make('conv_notf');
});
Route::get('conv/notf1', function () {
  return View::make('conv_notf1');
});
Route::get('user/frennotf', function () {
  return View::make('fren_notf');
});
Route::get('order/frennotf', function () {
  return View::make('order_frennotf');
});
Route::get('product/frennotf', function () {
  return View::make('product_frennotf');
});
Route::get('/zeeorder', 'App\Http\Controllers\FrenchiseController@accountDetail')->name('zeeorder');
Route::get('/jazzcash', 'App\Http\Controllers\FrontendController@jazcashget')->name('jazzcash');
Route::post('/jazzcash', 'App\Http\Controllers\FrontendController@jazcash')->name('jazzcash');
Route::get('/bankalfa', 'App\Http\Controllers\PaymentController@showBankAlfa')->name('bankalfa');

Route::get('/bankAlfa', 'App\Http\Controllers\PaymentController@bankAlfa')->name('payment.bankAlfa');

Route::get('/bankalfaget', 'App\Http\Controllers\PaymentController@showBankAlfa')->name('bankalfaget');
Route::post('/bankalfpost', 'App\Http\Controllers\PaymentController@bankalfpost')->name('bankalfpost');
route::get('paymentpage/{id}', 'App\Http\Controllers\FrontendController@paymentpage')->name('paymentpage');

route::get('alfapayment/{id}', 'App\Http\Controllers\PaymentController@payment')->name('alfapayment');
route::get('setcashondelivery/{id}', 'App\Http\Controllers\PaymentController@setCashOnDevlivery')->name('setcashondelivery');

//Franchise rote
Route::prefix('franchise')->group(function () {
  Route::get('/frenchise-login', 'App\Http\Controllers\Auth\FrenchiseLoginController@showLoginForm')->name('frenchise-frenchise-login');
  Route::post('/frenchise-login', 'App\Http\Controllers\Auth\FrenchiseLoginController@login')->name('frenchise-login-submit');
  Route::get('/logout', 'App\Http\Controllers\Auth\FrenchiseLoginController@logout')->name('frenchise-logout');
  Route::group(['middleware' => 'isFrenchise'], function () {

    Route::get('/frenchise-dashboard', [FrenchiseController::class, 'index'])->name('frenchise-dashboard');
    Route::get('/frenchise-vendor-dashboard/{id}', [FrenchiseController::class, 'vendorDashbord'])->name('frenchise-vendor-dashboard');

    Route::get('/add-vendor', [FrenchiseController::class, 'regestershop'])->name('register-shop');
    Route::post('/frenchise/registration', [FrenchiseController::class, 'shop_register'])->name('shop.registration');

    Route::get('/frenchise-profile', 'App\Http\Controllers\FrenchiseController@profile')->name('frenchise-profile');
    Route::post('/profile', 'App\Http\Controllers\FrenchiseController@profileupdate')->name('frenchise-profile-update');
    Route::get('/reset-password', 'App\Http\Controllers\FrenchiseController@passwordreset')->name('frenchise-password-reset');
    Route::post('/reset-password', 'App\Http\Controllers\FrenchiseController@changepass')->name('frenchise-password-change');
    Route::get('/logout', 'App\Http\Controllers\Auth\FrenchiseLoginController@logout')->name('frenchise-logout');

    Route::get('/vender-list', [FrenchiseController::class, 'vendorlist'])->name('vendor-list');
    Route::get('/vendors_active/status/{id1}/{id2}', [FrenchiseController::class, 'status'])->name('frenchise-vendor_active-status');
    Route::get('/vendors/{id}', [FrenchiseController::class, 'show'])->name('frenchise-vendor-show');
    Route::get('/vendor/edit/{id}', [FrenchiseController::class, 'edit'])->name('frenchise-vendor-edit');
    Route::post('/vendor/edit/{id}', [FrenchiseController::class, 'update'])->name('frenchise-vendor-update');
    Route::get('/vendors/delete/{id}',  [FrenchiseController::class, 'destroy'])->name('frenchise-vendor-delete');
    Route::get('/vendors/withdraws', [FrenchiseController::class, 'withdraws'])->name('vendor-wt');
    Route::get('/vendors/withdraw/details/{id}', [FrenchiseController::class, 'withdrawdetails'])->name('frenchise-vendor-wtd');
    Route::get('/vendors/withdraw/reject/{id}',  [FrenchiseController::class, 'accept'])->name('frenchise-wt-accept');
    Route::get('/vendors/withdraw/reject/{id}',  [FrenchiseController::class, 'reject'])->name('frenchise-wt-reject');
    Route::get('/vendors', [FrenchiseController::class, 'subs'])->name('vendor-subs');
    Route::get('/vendors/sub/{id}', [FrenchiseController::class, 'sub'])->name('vendor-sub');

    Route::get('/orders', 'App\Http\Controllers\FrenchiseOrderController@index')->name('frenchise-order-index');
    Route::get('/order/{id}/invoice', 'App\Http\Controllers\FrenchiseOrderController@invoice')->name('frenchise-order-invoice');
    // Route::get('/product/{id}/{slug}','App\Http\Controllers\FrenchiseOrderController@product')->name('frenchise.front.product');
    Route::get('/order/{id}/print', 'App\Http\Controllers\FrenchiseOrderController@printpage')->name('frenchise-order-print');
    Route::get('/order/{id}/show', 'App\Http\Controllers\FrenchiseOrderController@show')->name('frenchise-order-show');
    Route::get('/vendors/{id}/show', 'App\Http\Controllers\FrenchiseOrderController@shows')->name('vendor-show');
    Route::post('/order/{id}/license', 'App\Http\Controllers\FrenchiseOrderController@license')->name('frenchise-order-license');
    Route::get('/vendors/status/{id1}/{id2}', 'App\Http\Controllers\FrenchiseOrderController@status')->name('frenchise-vendor-st');
    Route::get('/order/{id1}/status/{status}', 'App\Http\Controllers\FrenchiseOrderController@status')->name('frenchise-order-status');
    Route::get('/cancel/shipping/{order_id}/{track_id}', 'App\Http\Controllers\FrenchiseOrderController@cancel_shipping')->name('cancel_shipping');
    Route::get('/orders/status/{status}', 'App\Http\Controllers\FrenchiseOrderController@ordersstatus')->name('frenchise-vendor-status');

    Route::get('/frenchise-customer-list', [FrenchiseController::class, 'vendorcustomer'])->name('frenchise-customer-list');
    Route::get('/customer/{id}/show', [FrenchiseController::class, 'customershow'])->name('frenchise-customer-show');

    Route::get('/vendor-orders/{id}', 'App\Http\Controllers\FrenchiseVendorOrderController@index')->name('frenchise-vendor-order-index');
    Route::get('/vendor-order/{id}/invoice', 'App\Http\Controllers\FrenchiseOrderController@invoice')->name('frenchise-vendor-order-invoice');
    // Route::get('/product/{id}/{slug}','App\Http\Controllers\FrenchiseOrderController@product')->name('frenchise.front.product');
    Route::get('/vendor-order/{id}/print', 'App\Http\Controllers\FrenchiseOrderController@printpage')->name('frenchise-vendor-order-print');
    Route::get('/vendor-order/{id}/show', 'App\Http\Controllers\FrenchiseOrderController@show')->name('frenchise-vendor-order-show');
    // Route::get('/vendor-vendors/{id}/show', 'App\Http\Controllers\FrenchiseOrderController@shows')->name('vendor-show');
    // Route::get('/vendors/status/{id1}/{id2}', 'App\Http\Controllers\FrenchiseOrderController@status')->name('frenchise-vendor-st');
    // Route::get('/vendor-order/{id1}/status/{status}', 'App\Http\Controllers\FrenchiseOrderController@status')->name('frenchise-vendor-order-status');
    Route::get('/vendor-orders/status/{id}/{status}', 'App\Http\Controllers\FrenchiseVendorOrderController@ordersstatus')->name('frenchise-vendor-order-status');

    Route::get('/product/create', 'App\Http\Controllers\FrenchiseProductController@create')->name('frenchise-prod-create');
    Route::post('/product/create', 'App\Http\Controllers\FrenchiseProductController@store')->name('frenchise-prod-store');
    Route::post('/product/create1', 'App\Http\Controllers\FrenchiseProductController@store1')->name('frenchise-prod-store1');
    Route::post('/product/create2', 'App\Http\Controllers\FrenchiseProductController@store2')->name('frenchise-prod-store2');
    Route::get('/product', 'App\Http\Controllers\FrenchiseProductController@index')->name('frenchise-prod-index');
    Route::get('/product/status/{id1}/{id2}', 'App\Http\Controllers\FrenchiseProductController@status')->name('frenchise-prod-st');
    Route::get('/product/edit/{id}', 'App\Http\Controllers\FrenchiseProductController@edit')->name('frenchise-prod-edit');
    Route::post('/product/update/{id}', 'App\Http\Controllers\FrenchiseProductController@update')->name('frenchise-prod-update');
    Route::post('/product/update1/{id}', 'App\Http\Controllers\FrenchiseProductController@update1')->name('frenchise-prod-update1');
    Route::post('/product/update2/{id}', 'App\Http\Controllers\FrenchiseProductController@update2')->name('frenchise-prod-update2');
    Route::get('/product/delete/{id}', 'App\Http\Controllers\FrenchiseProductController@destroy')->name('frenchise-prod-delete');
    Route::get('/product/deactive', 'App\Http\Controllers\FrenchiseProductController@deactive')->name('frenchise-prod-deactive');
    Route::get('/product/status/{id1}/{id2}', 'App\Http\Controllers\FrenchiseProductController@status')->name('frenchise-prod-st');

    Route::get('/vendor-product/{id}', 'App\Http\Controllers\FrenchiseProductController@vendorProductIndex')->name('frenchise-vendor-prod-index');
    Route::get('/vendor-product/deactive/{id}', 'App\Http\Controllers\FrenchiseProductController@vendorProductDeactive')->name('frenchise-vendor-prod-deactive');

    Route::get('/users', 'App\Http\Controllers\AdminUserController@index')->name('frenchise-user-index');
    Route::get('/frenchise-user/{id}/show', 'App\Http\Controllers\AdminUserController@show')->name('frenchise-user-show');

    Route::get('/social', [FrenchiseController::class, 'social'])->name('frenchise-social-index');
    Route::post('/social/update', [FrenchiseController::class, 'socialupdate'])->name('frenchise-social-update');

    Route::get('/frenchise-accounts', [FrenchiseController::class, 'account'])->name('frenchise-accounts');
  });
});
// Franchise route end

Route::post('/json/comment', 'App\Http\Controllers\Json\JsonBlogController@comment');
Route::post('/json/comment/edit', 'App\Http\Controllers\Json\JsonBlogController@commentedit');
Route::get('/json/comment/delete', 'App\Http\Controllers\Json\JsonBlogController@commentdelete');

Route::post('/json/reply', 'App\Http\Controllers\Json\JsonBlogController@reply');
Route::post('/json/reply/edit', 'App\Http\Controllers\Json\JsonBlogController@replyedit');
Route::get('/json/reply/delete', 'App\Http\Controllers\Json\JsonBlogController@replydelete');

Route::post('/json/subreply', 'App\Http\Controllers\Json\JsonBlogController@subreply');
Route::post('/json/subreply/edit', 'App\Http\Controllers\Json\JsonBlogController@subreplyedit');
Route::get('/json/subreply/delete', 'App\Http\Controllers\Json\JsonBlogController@subreplydelete');

Route::get('/json/order/notf', 'App\Http\Controllers\Json\JsonRequestController@order_notf');
Route::get('/json/order/notf/clear', 'App\Http\Controllers\Json\JsonRequestController@order_notf_clear');
Route::get('/json/product/notf', 'App\Http\Controllers\Json\JsonRequestController@product_notf');
Route::get('/json/product/notf/clear', 'App\Http\Controllers\Json\JsonRequestController@product_notf_clear');
Route::get('/json/user/notf', 'App\Http\Controllers\Json\JsonRequestController@user_notf');
Route::get('/json/user/notf/clear', 'App\Http\Controllers\Json\JsonRequestController@user_notf_clear');
Route::get('/json/conv/notf', 'App\Http\Controllers\Json\JsonRequestController@conv_notf');
Route::get('/json/conv/notf/clear', 'App\Http\Controllers\Json\JsonRequestController@conv_notf_clear');

Route::get('/json/order/notf1', 'App\Http\Controllers\Json\JsonRequestController@order_notf1');
Route::get('/json/order/notf/clear1', 'App\Http\Controllers\Json\JsonRequestController@order_notf_clear1');
Route::get('/json/product/notf1', 'App\Http\Controllers\Json\JsonRequestController@product_notf1');
Route::get('/json/product/notf/clear1', 'App\Http\Controllers\Json\JsonRequestController@product_notf_clear1');
Route::get('/json/conv/notf1', 'App\Http\Controllers\Json\JsonRequestController@conv_notf1');
Route::get('/json/conv/notf/clear1', 'App\Http\Controllers\Json\JsonRequestController@conv_notf_clear1');

Route::get('/json/compare', 'App\Http\Controllers\Json\JsonRequestController@compare');
Route::get('/json/removecompare', 'App\Http\Controllers\Json\JsonRequestController@removecompare');
Route::get('/json/clearcompare', 'App\Http\Controllers\Json\JsonRequestController@clearcompare');
Route::get('/json/pos', 'App\Http\Controllers\Json\JsonRequestController@pos');
Route::get('/json/quick', 'App\Http\Controllers\Json\JsonRequestController@quick');
Route::get('/json/feature', 'App\Http\Controllers\Json\JsonRequestController@feature');
Route::get('/json/vendor_feature', 'App\Http\Controllers\Json\JsonRequestController@vendor_feature');
Route::get('/json/gallery', 'App\Http\Controllers\Json\JsonRequestController@gallery');
Route::post('/json/addgallery', 'App\Http\Controllers\Json\JsonRequestController@addgallery');
Route::get('/json/removegallery', 'App\Http\Controllers\Json\JsonRequestController@delgallery');
Route::get('/json/addbyone', 'App\Http\Controllers\Json\JsonRequestController@addbyone');
Route::get('/json/reducebyone', 'App\Http\Controllers\Json\JsonRequestController@reducebyone');
Route::get('/json/subcats', 'App\Http\Controllers\Json\JsonRequestController@subcats');
Route::get('/json/city', 'App\Http\Controllers\Json\JsonRequestController@city');
Route::get('/json/childcats', 'App\Http\Controllers\Json\JsonRequestController@childcats');
Route::get('/json/addcart', 'App\Http\Controllers\Json\JsonRequestController@addcart');
Route::get('/json/addnumcart', 'App\Http\Controllers\Json\JsonRequestController@addnumcart');
Route::get('/json/updatecart', 'App\Http\Controllers\Json\JsonRequestController@upcart');
Route::get('/json/upcolor', 'App\Http\Controllers\Json\JsonRequestController@upcolor');
Route::get('/json/removecart', 'App\Http\Controllers\Json\JsonRequestController@removecart');
Route::get('/json/coupon', 'App\Http\Controllers\Json\JsonRequestController@coupon');
Route::get('/json/wish', 'App\Http\Controllers\Json\JsonRequestController@wish');
Route::get('/json/removewish', 'App\Http\Controllers\Json\JsonRequestController@removewish');
Route::get('/json/favorite', 'App\Http\Controllers\Json\JsonRequestController@favorite');
Route::get('/json/removefavorite', 'App\Http\Controllers\Json\JsonRequestController@removefavorite');
Route::get('/json/suggest', 'App\Http\Controllers\Json\JsonRequestController@suggest');
Route::get('/json/trans', 'App\Http\Controllers\Json\JsonRequestController@trans');
Route::get('/json/transhow', 'App\Http\Controllers\Json\JsonRequestController@transhow');

Route::get('/json/cart/update/item/key', 'App\Http\Controllers\Json\JsonRequestController@update_cart_item');

Route::get('/json/productsdata', 'App\Http\Controllers\Json\JsonRequestController@sectionProducts');

Route::get('/json/user/frennotf', 'App\Http\Controllers\Json\JsonRequestController@fren_notf');
Route::get('/json/user/frontnotf/clear', 'App\Http\Controllers\Json\JsonRequestController@fren_notf_clear');

Route::get('/json/conv/frennotf', 'App\Http\Controllers\Json\JsonRequestController@fren_conv_notf');
Route::get('/json/conv/frennotf/clear', 'App\Http\Controllers\Json\JsonRequestController@fren_conv_notf_clear');
Route::get('/json/order/frontnotf/clear', 'App\Http\Controllers\Json\JsonRequestController@fren_order_notf_clear');
Route::get('/json/product/frontnotf/clear', 'App\Http\Controllers\Json\JsonRequestController@fren_product_notf_clear');
Route::get('json/product/frennotf', 'App\Http\Controllers\Json\JsonRequestController@fren_product_notf');
Route::get('/json/order/frennotf', 'App\Http\Controllers\Json\JsonRequestController@fre_order_notf');

Route::get('/shop-type/{slug}', 'App\Http\Controllers\FrontendController@shoptype')->name('shops-type');
Route::get('/top-shops/{slug}', 'App\Http\Controllers\FrontendController@topShop')->name('top-shops');
Route::get('/brand/{slug}', 'App\Http\Controllers\FrontendController@brand')->name('front.brand');
Route::get('/shops/{slug}', 'App\Http\Controllers\FrontendController@allshops')->name('all-shops');

Route::prefix('admin')->group(function () {
  Route::get('/', 'App\Http\Controllers\Auth\AdminLoginController@showLoginForm')->name('admin-login');
  Route::post('/login', 'App\Http\Controllers\Auth\AdminLoginController@login')->name('admin-login-submit');

  Route::middleware('isAdmin')->group(function () {
    Route::get('/dashboard', 'App\Http\Controllers\AdminController@index')->name('admin-dashboard');
    Route::get('/profile', 'App\Http\Controllers\AdminController@profile')->name('admin-profile');
    Route::post('/profile', 'App\Http\Controllers\AdminController@profileupdate')->name('admin-profile-update');
    Route::get('/reset-password', 'App\Http\Controllers\AdminController@passwordreset')->name('admin-password-reset');
    Route::post('/reset-password', 'App\Http\Controllers\AdminController@changepass')->name('admin-password-change');
    Route::get('/logout', 'App\Http\Controllers\Auth\AdminLoginController@logout')->name('admin-logout');
    Route::get('/video', 'App\Http\Controllers\GeneralSettingController@video')->name('admin-video');
    Route::post('/video', 'App\Http\Controllers\GeneralSettingController@videoup')->name('admin-video-submit');
    Route::get('/large-banner', 'App\Http\Controllers\PageSettingController@banner')->name('admin-lbanner');
    Route::post('/large-banner', 'App\Http\Controllers\PageSettingController@bannerup')->name('admin-lbanner-submit');

    Route::prefix('orders')->group(function () {
      Route::get('', 'App\Http\Controllers\AdminOrderController@index')->name('admin-order-index');
      Route::get('/pending', 'App\Http\Controllers\AdminOrderController@pending')->name('admin-order-pending');
      Route::get('/processing', 'App\Http\Controllers\AdminOrderController@processing')->name('admin-order-processing');
      Route::get('/completed', 'App\Http\Controllers\AdminOrderController@completed')->name('admin-order-completed');
      Route::get('/declined', 'App\Http\Controllers\AdminOrderController@declined')->name('admin-order-declined');
      Route::get('/order/{id}/show', 'App\Http\Controllers\AdminOrderController@show')->name('admin-order-show');
    });
    Route::prefix('order')->group(function () {
      Route::get('/{id}/invoice', 'App\Http\Controllers\AdminOrderController@invoice')->name('admin-order-invoice');
      Route::get('/{id}/print', 'App\Http\Controllers\AdminOrderController@printpage')->name('admin-order-print');
      Route::get('/{id1}/status/{status}', 'App\Http\Controllers\AdminOrderController@status')->name('admin-order-status');
      Route::post('/email', 'App\Http\Controllers\AdminOrderController@emailsub')->name('admin-order-emailsub');
      Route::post('/{id}/license', 'App\Http\Controllers\AdminOrderController@license')->name('admin-order-license');
    });

    Route::get('/user/{id}/show', 'App\Http\Controllers\AdminUserController@show')->name('admin-user-show');
    Route::prefix('users')->group(function () {
      Route::get('/', 'App\Http\Controllers\AdminUserController@index')->name('admin-user-index');
      Route::get('/edit/{id}', 'App\Http\Controllers\AdminUserController@edit')->name('admin-user-edit');
      Route::post('/edit/{id}', 'App\Http\Controllers\AdminUserController@update')->name('admin-user-update');
      Route::get('/delete/{id}', 'App\Http\Controllers\AdminUserController@destroy')->name('admin-user-delete');
    });

    Route::prefix('product')->group(function () {
      Route::get('', 'App\Http\Controllers\ProductController@index')->name('admin-prod-index');
      Route::get('/deactive', 'App\Http\Controllers\ProductController@deactive')->name('admin-prod-deactive');
      Route::get('/create', 'App\Http\Controllers\ProductController@create')->name('admin-prod-create');
      Route::post('/create', 'App\Http\Controllers\ProductController@store')->name('admin-prod-store');
      Route::post('/create1', 'App\Http\Controllers\ProductController@store1')->name('admin-prod-store1');
      Route::post('/create2', 'App\Http\Controllers\ProductController@store2')->name('admin-prod-store2');
      Route::get('/edit/{id}', 'App\Http\Controllers\ProductController@edit')->name('admin-prod-edit');
      Route::post('/update/{id}', 'App\Http\Controllers\ProductController@update')->name('admin-prod-update');
      Route::post('/update1/{id}', 'App\Http\Controllers\ProductController@update1')->name('admin-prod-update1');
      Route::post('/update2/{id}', 'App\Http\Controllers\ProductController@update2')->name('admin-prod-update2');
      Route::post('/feature/{id}', 'App\Http\Controllers\ProductController@feature')->name('admin-prod-feature');
      Route::get('/delete/{id}', 'App\Http\Controllers\ProductController@destroy')->name('admin-prod-delete');
      Route::get('/status/{id1}/{id2}', 'App\Http\Controllers\ProductController@status')->name('admin-prod-st');


       Route::get('/add_sale_tax', 'App\Http\Controllers\ProductController@add_sale_tax')->name('admin-sale-tax');
       Route::patch('/insert_sale_tax', 'App\Http\Controllers\ProductController@insert_sale_tax')->name('insert-sale-tax');
    });

    Route::prefix('category')->group(function () {
      Route::get('/', 'App\Http\Controllers\CategoryController@index')->name('admin-cat-index');
      Route::get('/create', 'App\Http\Controllers\CategoryController@create')->name('admin-cat-create');
      Route::post('/create', 'App\Http\Controllers\CategoryController@store')->name('admin-cat-store');
      Route::get('/edit/{id}', 'App\Http\Controllers\CategoryController@edit')->name('admin-cat-edit');
      Route::get('/edit/{id}', 'App\Http\Controllers\CategoryController@edit')->name('admin-cat-edit'); ///////////zee
      Route::get('/type_edit', 'App\Http\Controllers\CategoryController@type_edit')->name('admin-cat-type_edit'); ///////////zee
      Route::post('/update/{id}', 'App\Http\Controllers\CategoryController@update')->name('admin-cat-update');
      Route::post('/type_update/{id}', 'App\Http\Controllers\CategoryController@type_update')->name('admin-cat-type_update'); ////zeee
      Route::get('/delete/{id}', 'App\Http\Controllers\CategoryController@destroy')->name('admin-cat-delete');
      Route::get('/status/{id1}/{id2}', 'App\Http\Controllers\CategoryController@status')->name('admin-cat-st');
      Route::get('/type/{id1}/{id2}', 'App\Http\Controllers\CategoryController@set_type')->name('admin-cat-tp'); /////zee
    });

    Route::prefix('subcategory')->group(function () {
      Route::get('/', 'App\Http\Controllers\SubcategoryController@index')->name('admin-subcat-index');
      Route::get('/create', 'App\Http\Controllers\SubcategoryController@create')->name('admin-subcat-create');
      Route::post('/create', 'App\Http\Controllers\SubcategoryController@store')->name('admin-subcat-store');
      Route::get('/edit/{id}', 'App\Http\Controllers\SubcategoryController@edit')->name('admin-subcat-edit');
      Route::post('/update/{id}', 'App\Http\Controllers\SubcategoryController@update')->name('admin-subcat-update');
      Route::get('/delete/{id}', 'App\Http\Controllers\SubcategoryController@destroy')->name('admin-subcat-delete');
      Route::get('/status/{id1}/{id2}', 'App\Http\Controllers\SubcategoryController@status')->name('admin-subcat-st');
    });

    Route::prefix('childcategory')->group(function () {
      Route::get('/', 'App\Http\Controllers\ChildcategoryController@index')->name('admin-childcat-index');
      Route::get('/create', 'App\Http\Controllers\ChildcategoryController@create')->name('admin-childcat-create');
      Route::post('/create', 'App\Http\Controllers\ChildcategoryController@store')->name('admin-childcat-store');
      Route::get('/edit/{id}', 'App\Http\Controllers\ChildcategoryController@edit')->name('admin-childcat-edit');
      Route::post('/update/{id}', 'App\Http\Controllers\ChildcategoryController@update')->name('admin-childcat-update');
      Route::get('/delete/{id}', 'App\Http\Controllers\ChildcategoryController@destroy')->name('admin-childcat-delete');
      Route::get('/status/{id1}/{id2}', 'App\Http\Controllers\ChildcategoryController@status')->name('admin-childcat-st');
    });

    Route::prefix('coupon')->group(function () {
      Route::get('/', 'App\Http\Controllers\AdminCouponController@index')->name('admin-cp-index');
      Route::get('/create', 'App\Http\Controllers\AdminCouponController@create')->name('admin-cp-create');
      Route::post('/create', 'App\Http\Controllers\AdminCouponController@store')->name('admin-cp-store');
      Route::get('/edit/{id}', 'App\Http\Controllers\AdminCouponController@edit')->name('admin-cp-edit');
      Route::post('/edit/{id}', 'App\Http\Controllers\AdminCouponController@update')->name('admin-cp-update');
      Route::get('/delete/{id}', 'App\Http\Controllers\AdminCouponController@destroy')->name('admin-cp-delete');
      Route::get('/status/{id1}/{id2}', 'App\Http\Controllers\AdminCouponController@status')->name('admin-cp-st');
    });

    Route::prefix('blog')->group(function () {
      Route::get('/', 'App\Http\Controllers\AdminBlogController@index')->name('admin-blog-index');
      Route::get('/create', 'App\Http\Controllers\AdminBlogController@create')->name('admin-blog-create');
      Route::post('/create', 'App\Http\Controllers\AdminBlogController@store')->name('admin-blog-store');
      Route::get('/edit/{id}', 'App\Http\Controllers\AdminBlogController@edit')->name('admin-blog-edit');
      Route::post('/edit/{id}', 'App\Http\Controllers\AdminBlogController@update')->name('admin-blog-update');
      Route::get('/delete/{id}', 'App\Http\Controllers\AdminBlogController@destroy')->name('admin-blog-delete');
    });

    Route::prefix('subscription')->group(function () {
      Route::get('/', 'App\Http\Controllers\VendorSubscriptionController@index')->name('admin-subscription-index');
      Route::get('/create', 'App\Http\Controllers\VendorSubscriptionController@create')->name('admin-subscription-create');
      Route::post('/create', 'App\Http\Controllers\VendorSubscriptionController@store')->name('admin-subscription-store');
      Route::get('/edit/{id}', 'App\Http\Controllers\VendorSubscriptionController@edit')->name('admin-subscription-edit');
      Route::post('/edit/{id}', 'App\Http\Controllers\VendorSubscriptionController@update')->name('admin-subscription-update');
      Route::get('/delete/{id}', 'App\Http\Controllers\VendorSubscriptionController@destroy')->name('admin-subscription-delete');
    });

    Route::prefix('bottom-banner')->group(function () {
      Route::get('/list', 'App\Http\Controllers\ImageController@index')->name('admin-img1-index');
      Route::get('/create', 'App\Http\Controllers\ImageController@create')->name('admin-img1-create');
      Route::post('/create', 'App\Http\Controllers\ImageController@store')->name('admin-img1-store');
      Route::get('/edit/{id}', 'App\Http\Controllers\ImageController@edit')->name('admin-img1-edit');
      Route::post('/edit/{id}', 'App\Http\Controllers\ImageController@update')->name('admin-img1-update');
      Route::get('/delete/{id}', 'App\Http\Controllers\ImageController@destroy')->name('admin-img1-delete');
    });

    Route::prefix('banner')->group(function () {
      Route::get('/top', 'App\Http\Controllers\BannerController@top')->name('admin-banner-top');
      Route::get('/shop/{slug}', 'App\Http\Controllers\BannerController@shop')->name('admin-banner-shop');
      Route::post('/top', 'App\Http\Controllers\BannerController@topup')->name('admin-banner-topup');
      Route::post('/top1', 'App\Http\Controllers\BannerController@topup1')->name('admin-banner-topup1');
      Route::get('/edit/{id}/{slug}', 'App\Http\Controllers\BannerController@edit')->name('admin-banner-edit');
      Route::post('/update/{id}', 'App\Http\Controllers\BannerController@update')->name('admin-banner-update');
      Route::get('/delete/{id}', 'App\Http\Controllers\BannerController@destroy')->name('admin-banner-delete');
      Route::get('/bottom', 'App\Http\Controllers\BannerController@bottom')->name('admin-banner-bottom');
      Route::post('/bottom', 'App\Http\Controllers\BannerController@bottomup')->name('admin-banner-bottomup');
    });

    Route::prefix('general-settings')->group(function () {
      Route::get('/countdown', 'App\Http\Controllers\GeneralSettingController@countdown')->name('admin-gs-countdown');
      Route::post('/countdown', 'App\Http\Controllers\GeneralSettingController@countdownup')->name('admin-gs-countdownup');
      Route::get('/fes-info', 'App\Http\Controllers\GeneralSettingController@fesinfo')->name('admin-gs-festival');
      Route::post('/fes-info', 'App\Http\Controllers\GeneralSettingController@fesinfoup')->name('admin-gs-fesinfoup');
    });

    //Admin sub head office route
    Route::prefix('sub-head-office')->group(function () {
      Route::get('/add', 'App\Http\Controllers\AdminSubHeadOfficeController@add_sub_head_office')->name('admin.sub_head_office_add');
      Route::post('/create', 'App\Http\Controllers\AdminSubHeadOfficeController@create_sub_head_office')->name('admin.sub_head_office_create');
      Route::get('/{id}/edit', 'App\Http\Controllers\AdminSubHeadOfficeController@edit_sub_head_office')->name('admin.sub_head_office_edit');
      Route::post('/{id}/edit', 'App\Http\Controllers\AdminSubHeadOfficeController@update_sub_head_office')->name('admin.sub_head_office_update');
      Route::get('/{id}/delete', 'App\Http\Controllers\AdminSubHeadOfficeController@delete_sub_head_office')->name('admin.sub_head_office_delete');
      Route::get('/list', 'App\Http\Controllers\AdminSubHeadOfficeController@sub_head_office_list')->name('admin.sub_head_office_list');
      Route::get('/{user}/dashboard', 'App\Http\Controllers\AdminSubHeadOfficeController@index')->name('admin.sub_head_office_dashboard');
      Route::get('/{id1}/status/{id2}', 'App\Http\Controllers\AdminSubHeadOfficeController@update_sub_head_office_status')->name('admin.update_sub_head_office_status');
      Route::get('/{id}/show', 'App\Http\Controllers\AdminSubHeadOfficeController@show_sub_head_office')->name('admin.sub_head_office_show');

      // Admin Orders
      Route::get('/{user}/orders/{status}', 'App\Http\Controllers\AdminSubHeadOfficeController@orders_by_status')->name('admin.sub_head_office_orders_by_status');
      Route::prefix('order')->group(function () {
        Route::get('/{id}/details', 'App\Http\Controllers\AdminSubHeadOfficeController@view_order')->name('admin.sub_head_office_order_details');
        Route::get('/{id}/invoice', 'App\Http\Controllers\AdminSubHeadOfficeController@view_order_invoice')->name('admin.sub_head_office_order_invoice');
        Route::get('/{id}/print', 'App\Http\Controllers\AdminSubHeadOfficeController@print_page')->name('admin.sub_head_office_order_print');
        Route::get('/{id}/status/{status}', 'App\Http\Controllers\AdminSubHeadOfficeController@update_order_status_by_id')->name('admin.sub_head_office_update_order_status_by_id');
        Route::post('/email', 'App\Http\Controllers\AdminSubHeadOfficeController@emailsub')->name('admin.sub_head_office_order_emailsub');
        Route::post('/{id}/license', 'App\Http\Controllers\AdminSubHeadOfficeController@license')->name('admin.sub_head_office_order_license');
        Route::get('/{id}/license', 'App\Http\Controllers\AdminSubHeadOfficeController@license')->name('admin.sub_head_office_order_license');
      });

      // Admin Franchises
      Route::prefix('franchise')->group(function () {
        Route::get('{user}/all/{status}', 'App\Http\Controllers\AdminSubHeadOfficeController@list_franchise_by_status')->name('admin.sub_head_office_frenchises_by_status');
        Route::get('{user}/list', 'App\Http\Controllers\AdminSubHeadOfficeController@list_franchise')->name('admin.sub_head_office_frenchises');
        Route::get('/add', 'App\Http\Controllers\AdminSubHeadOfficeController@add_franchise')->name('admin.sub_head_office_frenchise_add');
        Route::get('/{id}/show', 'App\Http\Controllers\AdminSubHeadOfficeController@details_franchise')->name('admin.sub_head_office_frenchise_details');
        Route::get('/{id}/dashboard', 'App\Http\Controllers\AdminSubHeadOfficeController@dashboard_franchise')->name('admin.sub_head_office_frenchise_dashboard');
        Route::get('/{id}/edit', 'App\Http\Controllers\AdminSubHeadOfficeController@edit')->name('admin.sub_head_office_frenchise_edit');
        Route::post('/{id}/update', 'App\Http\Controllers\AdminSubHeadOfficeController@update_franchise')->name('admin.sub_head_office_frenchise_update');
        Route::post('/{user}/create', 'App\Http\Controllers\AdminSubHeadOfficeController@create_franchise')->name('admin.sub_head_office_frenchise_create');
        Route::get('/{franchise}/orders/{status}', 'App\Http\Controllers\AdminSubHeadOfficeController@franchise_orders_by_status')->name('admin.franchise_orders_by_status');
        Route::get('/{franchise}/customers/list', 'App\Http\Controllers\AdminSubHeadOfficeController@franchise_vendors_customers')->name('admin.sub_head_office_frenchise_customers');
        Route::get('/customer/{user}/show', 'App\Http\Controllers\AdminSubHeadOfficeController@customer_show')->name('admin.sub_head_office_frenchise_customer_show');

        Route::get('/{franchise}/vender/list', 'App\Http\Controllers\AdminSubHeadOfficeController@franchise_vendors_list')->name('admin.sub_head_office_franchise_vendors_list');
      });

      // Admin Vendors
      Route::prefix('vendor')->group(function () {
        Route::get('{user}/all', 'App\Http\Controllers\AdminSubHeadOfficeController@vendors_list')->name('admin.sub_head_office_vendors');
        // Route::get('/withdraws', 'App\Http\Controllers\AdminVendorController@withdraws')->name('sub_head_office_vendors_width');
      });
    });
    // Admin end sub head office

    Route::prefix('franchise')->group(function () {
      Route::get('/list', 'App\Http\Controllers\AdminController@listFrenchise')->name('admin-frenchise-index');
      Route::get('/add', 'App\Http\Controllers\AdminController@addFrenchise')->name('admin-frenchise-add');
      Route::get('/{id}/dashboard', 'App\Http\Controllers\AdminController@dashboardFrenchise')->name('admin-frenchise-dashboard');
      Route::post('/save', 'App\Http\Controllers\AdminVendorController@storeFrenchise')->name('admin-frenchise-submit');
      Route::get('/{id}/show', 'App\Http\Controllers\AdminController@show')->name('admin-frenchise-show');
      Route::get('/status/{id1}/{id2}', 'App\Http\Controllers\AdminController@status')->name('admin-frenchise-st');
      Route::get('/edit/{id}', 'App\Http\Controllers\AdminController@edit')->name('admin-frenchise-edit');
      Route::post('/edit/{id}', 'App\Http\Controllers\AdminController@update')->name('admin-frenchise-update');
      Route::get('/delete/{id}', 'App\Http\Controllers\AdminController@destroy')->name('admin-frenchise-delete');
      Route::get('/asign/{id}/{id2}', 'App\Http\Controllers\AdminController@frenchiseAsign')->name('admin-frenchise-asign');

      Route::get('/product/{id}', 'App\Http\Controllers\AdminFrenchiseProductController@frenchiseProductIndex')->name('admin-frenchise-prod-index');
      Route::get('/order/status/{status}/{id}', 'App\Http\Controllers\AdminFrenchiseProductController@frenchiseordersstatus')->name('admin-frenchise-order-status');
      Route::get('/vender-list/{id}', 'App\Http\Controllers\AdminFrenchiseProductController@vendorlist')->name('admin-frenchise-vendor-list');
      Route::get('/order/{id}/invoice', 'App\Http\Controllers\AdminOrderController@invoice')->name('admin-frenchise-order-invoice');
      Route::get('/order/{id}/show', 'App\Http\Controllers\AdminOrderController@show')->name('admin-frenchise-order-show');
      Route::get('/{id}/customers/list', 'App\Http\Controllers\AdminVendorController@customerlist')->name('admin-frenchise-vendor-customer');
      Route::get('/{id}/vendor/dashboard', 'App\Http\Controllers\AdminVendorController@vendorDashbord')->name('admin-frenchise-vendor-dashboard');

    Route::get('/{id}/vendor_chartdetail', 'App\Http\Controllers\AdminVendorController@vendor_chartdetail')->name('admin-vendor-chartdetail');      

    Route::get('/{id}/chart_detail', 'App\Http\Controllers\AdminController@chart_detail')->name('admin-chart-detail');

    Route::get('/{id}/subheadoffice_chartdetail', 'App\Http\Controllers\AdminSubHeadOfficeController@subheadoffice_chartdetail')->name('admin-subheadoffice-chart_detail');



    });

    // add country and city
    Route::get('/add-country-city', 'App\Http\Controllers\AdminController@addCountryCity')->name('add-country-city');
    Route::post('/addcity', 'App\Http\Controllers\AdminController@addcity')->name('addcity');
    Route::get('/list-country-city', 'App\Http\Controllers\AdminController@listcountry')->name('listcountry');
    Route::get('/list-country-city/delete/{id}', 'App\Http\Controllers\AdminController@delete')->name('list-country-city');

    Route::get('/vendors', 'App\Http\Controllers\AdminVendorController@index')->name('admin-vendor-index');
    Route::prefix('vendor')->group(function () {
      Route::post('/feature/{id}', 'App\Http\Controllers\ProductController@vendor_feature')->name('admin-vendor-feature');
      Route::get('/edit/{id}', 'App\Http\Controllers\AdminVendorController@edit')->name('admin-vendor-edit');
      Route::post('/edit/{id}', 'App\Http\Controllers\AdminVendorController@update')->name('admin-vendor-update');
      Route::get('/active/status/{id1}/{id2}', 'App\Http\Controllers\AdminController@v_status')->name('admin-frenchise-vendor_active-status');
      Route::get('/product/{id}', 'App\Http\Controllers\AdminFrenchiseProductController@vendorProductIndex')->name('admin-frenchise-vendor-prod-index');
      Route::get('/product/deactive/{id}', 'App\Http\Controllers\AdminFrenchiseProductController@vendorProductDeactive')->name('admin-frenchise-vendor-prod-deactive');
      Route::get('/orders/{id}', 'App\Http\Controllers\AdminVendorController@vendororder')->name('admin-frenchise-vendor-order-index');
      Route::get('/orders/status/{id}/{status}', 'App\Http\Controllers\AdminVendorController@ordersstatus')->name('admin-frenchise-vendor-order-status');
      Route::get('/subs', 'App\Http\Controllers\AdminVendorController@subs')->name('admin-vendor-subs');
      Route::get('/sub/{id}', 'App\Http\Controllers\AdminVendorController@sub')->name('admin-vendor-sub');
      Route::get('/{id}/show', 'App\Http\Controllers\AdminVendorController@show')->name('admin-vendor-show');
      Route::get('/pending', 'App\Http\Controllers\AdminVendorController@pending')->name('admin-vendor-pending');
      Route::get('/status/{id1}/{id2}', 'App\Http\Controllers\AdminVendorController@status')->name('admin-vendor-st');
      Route::get('/delete/{id}', 'App\Http\Controllers\AdminVendorController@destroy')->name('admin-vendor-delete');
      Route::get('/email/{id}', 'App\Http\Controllers\AdminVendorController@email')->name('admin-vendor-email');
      Route::get('/withdraws', 'App\Http\Controllers\AdminVendorController@withdraws')->name('admin-vendor-wt');
      Route::get('/withdraws/pending', 'App\Http\Controllers\AdminVendorController@pendings')->name('admin-wt-pending');
      Route::get('/withdraw/details/{id}', 'App\Http\Controllers\AdminVendorController@withdrawdetails')->name('admin-vendor-wtd');
      Route::get('/withdraw/accept/{id}', 'App\Http\Controllers\AdminVendorController@accept')->name('admin-wt-accept');
      Route::get('/withdraw/reject/{id}', 'App\Http\Controllers\AdminVendorController@reject')->name('admin-wt-reject');
    });

    Route::prefix('users')->group(function () {
      Route::get('/withdraws', 'App\Http\Controllers\AdminVendorController@userwithdraws')->name('admin-vendor-wtt');
      Route::get('/withdraws/pending', 'App\Http\Controllers\AdminVendorController@userpendings')->name('admin-wtt-pending');
      Route::get('/withdraw/details/{id}', 'App\Http\Controllers\AdminVendorController@userwithdrawdetails')->name('admin-vendor-wttd');
      Route::get('/withdraw/accept/{id}', 'App\Http\Controllers\AdminVendorController@useraccept')->name('admin-wtt-accept');
      Route::get('/withdraw/reject/{id}', 'App\Http\Controllers\AdminVendorController@userreject')->name('admin-wtt-reject');
    });

    Route::get('/customer/{id}/show', 'App\Http\Controllers\AdminVendorController@customershow')->name('admin-frenchise-customer-show');

    Route::prefix('faq')->group(function () {
      Route::get('/', 'App\Http\Controllers\FaqController@index')->name('admin-fq-index');
      Route::get('/create', 'App\Http\Controllers\FaqController@create')->name('admin-fq-create');
      Route::post('/create', 'App\Http\Controllers\FaqController@store')->name('admin-fq-store');
      Route::get('/edit/{id}', 'App\Http\Controllers\FaqController@edit')->name('admin-fq-edit');
      Route::post('/update/{id}', 'App\Http\Controllers\FaqController@update')->name('admin-fq-update');
      Route::get('/delete/{id}', 'App\Http\Controllers\FaqController@destroy')->name('admin-fq-delete');
    });

    Route::prefix('currency')->group(function () {
      Route::get('/', 'App\Http\Controllers\AdminCurrencyController@index')->name('admin-currency-index');
      Route::get('/create', 'App\Http\Controllers\AdminCurrencyController@create')->name('admin-currency-create');
      Route::post('/create', 'App\Http\Controllers\AdminCurrencyController@store')->name('admin-currency-store');
      Route::get('/edit/{id}', 'App\Http\Controllers\AdminCurrencyController@edit')->name('admin-currency-edit');
      Route::post('/update/{id}', 'App\Http\Controllers\AdminCurrencyController@update')->name('admin-currency-update');
      Route::get('/delete/{id}', 'App\Http\Controllers\AdminCurrencyController@destroy')->name('admin-currency-delete');
      Route::get('/status/{id1}/{id2}', 'App\Http\Controllers\AdminCurrencyController@status')->name('admin-currency-st');
    });

    Route::prefix('page')->group(function () {
      Route::get('/', 'App\Http\Controllers\PageController@index')->name('admin-page-index');
      Route::get('/create', 'App\Http\Controllers\PageController@create')->name('admin-page-create');
      Route::post('/create', 'App\Http\Controllers\PageController@store')->name('admin-page-store');
      Route::get('/edit/{id}', 'App\Http\Controllers\PageController@edit')->name('admin-page-edit');
      Route::post('/update/{id}', 'App\Http\Controllers\PageController@update')->name('admin-page-update');
      Route::get('/delete/{id}', 'App\Http\Controllers\PageController@destroy')->name('admin-page-delete');
    });

    Route::prefix('payment/gateway')->group(function () {
      Route::get('/', 'App\Http\Controllers\PaymentGatewayController@index')->name('admin-payment-index');
      Route::get('/create', 'App\Http\Controllers\PaymentGatewayController@create')->name('admin-payment-create');
      Route::post('/create', 'App\Http\Controllers\PaymentGatewayController@store')->name('admin-payment-store');
      Route::get('/edit/{id}', 'App\Http\Controllers\PaymentGatewayController@edit')->name('admin-payment-edit');
      Route::post('/update/{id}', 'App\Http\Controllers\PaymentGatewayController@update')->name('admin-payment-update');
      Route::get('/delete/{id}', 'App\Http\Controllers\PaymentGatewayController@destroy')->name('admin-payment-delete');
      Route::get('/st/{id1}/{id2}', 'App\Http\Controllers\PaymentGatewayController@status')->name('admin-payment-st');
    });

    Route::prefix('testimonial')->group(function () {
      Route::get('/', 'App\Http\Controllers\PortfolioController@index')->name('admin-ad-index');
      Route::get('/create', 'App\Http\Controllers\PortfolioController@create')->name('admin-ad-create');
      Route::post('/create', 'App\Http\Controllers\PortfolioController@store')->name('admin-ad-store');
      Route::get('/edit/{id}', 'App\Http\Controllers\PortfolioController@edit')->name('admin-ad-edit');
      Route::post('/edit/{id}', 'App\Http\Controllers\PortfolioController@update')->name('admin-ad-update');
      Route::get('/delete/{id}', 'App\Http\Controllers\PortfolioController@destroy')->name('admin-ad-delete');
    });

    Route::prefix('service')->group(function () {
      Route::get('/list', 'App\Http\Controllers\AdminServiceController@index')->name('admin-service-index');
      Route::get('/create', 'App\Http\Controllers\AdminServiceController@create')->name('admin-service-create');
      Route::post('/create', 'App\Http\Controllers\AdminServiceController@store')->name('admin-service-store');
      Route::get('/edit/{id}', 'App\Http\Controllers\AdminServiceController@edit')->name('admin-service-edit');
      Route::post('/edit/{id}', 'App\Http\Controllers\AdminServiceController@update')->name('admin-service-update');
      Route::get('/delete/{id}', 'App\Http\Controllers\AdminServiceController@destroy')->name('admin-service-delete');
    });

    Route::prefix('slider')->group(function () {
      Route::get('/list', 'App\Http\Controllers\SliderController@index')->name('admin-sl-index');
      Route::get('/create', 'App\Http\Controllers\SliderController@create')->name('admin-sl-create');
      Route::post('/create', 'App\Http\Controllers\SliderController@store')->name('admin-sl-store');
      Route::get('/edit/{id}', 'App\Http\Controllers\SliderController@edit')->name('admin-sl-edit');
      Route::post('/edit/{id}', 'App\Http\Controllers\SliderController@update')->name('admin-sl-update');
      Route::get('/delete/{id}', 'App\Http\Controllers\SliderController@destroy')->name('admin-sl-delete');
    });

    Route::get('/brand', 'App\Http\Controllers\BrandController@index')->name('admin-img-index');
    Route::get('/brand/create', 'App\Http\Controllers\BrandController@create')->name('admin-img-create');
    Route::post('/brand/create', 'App\Http\Controllers\BrandController@store')->name('admin-img-store');
    Route::get('/brand/edit/{id}', 'App\Http\Controllers\BrandController@edit')->name('admin-img-edit');
    Route::post('/brand/edit/{id}', 'App\Http\Controllers\BrandController@update')->name('admin-img-update');
    Route::get('/brand/delete/{id}', 'App\Http\Controllers\BrandController@destroy')->name('admin-img-delete');

    Route::get('/pickup', 'App\Http\Controllers\PickupController@index')->name('admin-pick-index');
    Route::get('/pickup/create', 'App\Http\Controllers\PickupController@create')->name('admin-pick-create');
    Route::post('/pickup/create', 'App\Http\Controllers\PickupController@store')->name('admin-pick-store');
    Route::get('/pickup/edit/{id}', 'App\Http\Controllers\PickupController@edit')->name('admin-pick-edit');
    Route::post('/pickup/edit/{id}', 'App\Http\Controllers\PickupController@update')->name('admin-pick-update');
    Route::get('/pickup/delete/{id}', 'App\Http\Controllers\PickupController@destroy')->name('admin-pick-delete');

    Route::get('/page-settings/contact', 'App\Http\Controllers\PageSettingController@contact')->name('admin-ps-contact');
    Route::post('/page-settings/contact', 'App\Http\Controllers\PageSettingController@contactupdate')->name('admin-ps-contact-submit');

    Route::get('/staff', 'App\Http\Controllers\AdminStaffController@index')->name('admin-staff-index');
    Route::get('/staff/create', 'App\Http\Controllers\AdminStaffController@create')->name('admin-staff-create');
    Route::post('/staff/create', 'App\Http\Controllers\AdminStaffController@store')->name('admin-staff-store');
    Route::get('/staff/edit/{id}', 'App\Http\Controllers\AdminStaffController@show')->name('admin-staff-show');
    Route::get('/staff/delete/{id}', 'App\Http\Controllers\AdminStaffController@destroy')->name('admin-staff-delete');

    Route::get('/social', 'App\Http\Controllers\SocialSettingController@index')->name('admin-social-index');
    Route::post('/social/update', 'App\Http\Controllers\SocialSettingController@update')->name('admin-social-update');
    Route::get('/social/facebook', 'App\Http\Controllers\SocialSettingController@facebook')->name('admin-social-facebook');
    Route::post('/social/facebook', 'App\Http\Controllers\SocialSettingController@facebookupdate')->name('admin-social-ufacebook');
    Route::get('/social/google', 'App\Http\Controllers\SocialSettingController@google')->name('admin-social-google');
    Route::post('/social/google', 'App\Http\Controllers\SocialSettingController@googleupdate')->name('admin-social-ugoogle');
    Route::get('/seotools/analytics', 'App\Http\Controllers\SeoToolController@analytics')->name('admin-seotool-analytics');
    Route::post('/seotools/analytics/update', 'App\Http\Controllers\SeoToolController@analyticsupdate')->name('admin-seotool-analytics-update');
    Route::get('/seotools/keywords', 'App\Http\Controllers\SeoToolController@keywords')->name('admin-seotool-keywords');
    Route::post('/seotools/keywords/update', 'App\Http\Controllers\SeoToolController@keywordsupdate')->name('admin-seotool-keywords-update');

    Route::get('/general-settings/logo', 'App\Http\Controllers\GeneralSettingController@logo')->name('admin-gs-logo');
    Route::post('/general-settings/logo', 'App\Http\Controllers\GeneralSettingController@logoup')->name('admin-gs-logoup');


    Route::get('/general-settings/coming-soon-gif', 'App\Http\Controllers\GeneralSettingController@comingsoongif')->name('admin-gs-comingsoongif');
    Route::post('/general-settings/coming-soon-gif', 'App\Http\Controllers\GeneralSettingController@comingsoongifup')->name('admin-gs-comingsoongifup');

    Route::get('/general-settings/affilate', 'App\Http\Controllers\GeneralSettingController@affilate')->name('admin-gs-affilate');
    Route::post('/general-settings/affilate', 'App\Http\Controllers\GeneralSettingController@affilateup')->name('admin-gs-affilateup');

    Route::get('/general-settings/popup', 'App\Http\Controllers\GeneralSettingController@popup')->name('admin-gs-popup');
    Route::post('/general-settings/popup', 'App\Http\Controllers\GeneralSettingController@popupup')->name('admin-gs-popupup');

    Route::get('/general-settings/favicon', 'App\Http\Controllers\GeneralSettingController@fav')->name('admin-gs-fav');
    Route::post('/general-settings/favicon', 'App\Http\Controllers\GeneralSettingController@favup')->name('admin-gs-favup');

    Route::get('/general-settings/payments', 'App\Http\Controllers\GeneralSettingController@payments')->name('admin-gs-payments');
    Route::post('/general-settings/payments', 'App\Http\Controllers\GeneralSettingController@paymentsup')->name('admin-gs-paymentsup');
    Route::get('/general-settings/guest/{status}', 'App\Http\Controllers\GeneralSettingController@guest')->name('admin-gs-guest');
    Route::get('/general-settings/paypal/{status}', 'App\Http\Controllers\GeneralSettingController@paypal')->name('admin-gs-paypal');
    Route::get('/general-settings/stripe/{status}', 'App\Http\Controllers\GeneralSettingController@stripe')->name('admin-gs-stripe');
    Route::get('/general-settings/cod/{status}', 'App\Http\Controllers\GeneralSettingController@cod')->name('admin-gs-cod');
    Route::get('/issubscribe/{status}', 'App\Http\Controllers\GeneralSettingController@issubscribe')->name('admin-gs-issubscribe');

    Route::get('/general-settings/contents', 'App\Http\Controllers\GeneralSettingController@contents')->name('admin-gs-contents');
    Route::post('/general-settings/contents', 'App\Http\Controllers\GeneralSettingController@contentsup')->name('admin-gs-contentsup');

    Route::get('/general-settings/bgimg', 'App\Http\Controllers\GeneralSettingController@bgimg')->name('admin-gs-bgimg');
    Route::post('/general-settings/bgimgup', 'App\Http\Controllers\GeneralSettingController@bgimgup')->name('admin-gs-bgimgup');

    Route::get('/general-settings/cimg', 'App\Http\Controllers\GeneralSettingController@cimg')->name('admin-gs-cimg');
    Route::post('/general-settings/cimgup', 'App\Http\Controllers\GeneralSettingController@cimgup')->name('admin-gs-cimgup');

    Route::get('/general-settings/copyright', 'App\Http\Controllers\GeneralSettingController@about')->name('admin-gs-about');
    Route::post('/general-settings/copyright', 'App\Http\Controllers\GeneralSettingController@aboutup')->name('admin-gs-aboutup');

    Route::get('/general-settings/home-info', 'App\Http\Controllers\GeneralSettingController@bginfo')->name('admin-gs-bginfo');
    Route::post('/general-settings/home-info', 'App\Http\Controllers\GeneralSettingController@bginfoup')->name('admin-gs-bginfoup');

    Route::get('/general-settings/feature', 'App\Http\Controllers\GeneralSettingController@feature')->name('admin-gs-feature');
    Route::post('/general-settings/feature', 'App\Http\Controllers\GeneralSettingController@featureup')->name('admin-gs-featureup');

    Route::get('/general-settings/success', 'App\Http\Controllers\GeneralSettingController@successm')->name('admin-gs-successm');
    Route::post('/general-settings/success', 'App\Http\Controllers\GeneralSettingController@successmup')->name('admin-gs-successmup');

    Route::get('/subscribers', 'App\Http\Controllers\SubscriberController@index')->name('admin-subs-index');
    Route::get('/subscribers/download', 'App\Http\Controllers\SubscriberController@download')->name('admin-subs-download');

    Route::get('/languages', 'App\Http\Controllers\LanguageController@index')->name('admin-lang-index');
    Route::get('/languages/create', 'App\Http\Controllers\LanguageController@create')->name('admin-lang-create');
    Route::get('/languages/edit/{id}', 'App\Http\Controllers\LanguageController@edit')->name('admin-lang-edit');
    Route::post('/languages/create', 'App\Http\Controllers\LanguageController@store')->name('admin-lang-store');
    Route::post('/languages/edit/{id}', 'App\Http\Controllers\LanguageController@update')->name('admin-lang-update');
    Route::get('/languages/delete/{id}', 'App\Http\Controllers\LanguageController@destroy')->name('admin-lang-delete');
    Route::get('/languages/status/{id1}/{id2}', 'App\Http\Controllers\LanguageController@status')->name('admin-lang-st');
    Route::get('/regvendor/{status}', 'App\Http\Controllers\GeneralSettingController@regvendor')->name('admin-gs-regvendor');
    Route::get('/rtl/{status}', 'App\Http\Controllers\GeneralSettingController@rtl')->name('admin-gs-rtl');
    Route::get('/vendor/registration', 'App\Http\Controllers\GeneralSettingController@reg')->name('admin-gs-reg');

    Route::get('/general-settings/loader', 'App\Http\Controllers\GeneralSettingController@load')->name('admin-gs-load');
    Route::post('/general-settings/loader', 'App\Http\Controllers\GeneralSettingController@loadup')->name('admin-gs-loadup');
    //new
    Route::get('/products/popular/{id}', 'App\Http\Controllers\SeoToolController@popular')->name('admin-prod-popular');

    Route::get('/reviews', 'App\Http\Controllers\AdminController@reviews')->name('admin-review-index');
    Route::get('/review/delete/{id}', 'App\Http\Controllers\AdminController@reviewdelete')->name('admin-review-delete');
    Route::get('/review/show/{id}', 'App\Http\Controllers\AdminController@reviewshow')->name('admin-review-show');

    Route::get('/comments', 'App\Http\Controllers\AdminController@comments')->name('admin-comment-index');
    Route::get('/comments/delete/{id}', 'App\Http\Controllers\AdminController@commentdelete')->name('admin-comment-delete');
    Route::get('/comments/show/{id}', 'App\Http\Controllers\AdminController@commentshow')->name('admin-comment-show');

    Route::get('/messages', 'App\Http\Controllers\AdminController@messages')->name('admin-message-index');
    Route::get('/message/{id}', 'App\Http\Controllers\AdminController@message')->name('admin-message-show');
    Route::post('/message/post', 'App\Http\Controllers\AdminController@postmessage')->name('admin-message-store');
    Route::get('/message/{id}/delete', 'App\Http\Controllers\AdminController@messagedelete')->name('admin-message-delete');
    Route::post('/user/send/message', 'App\Http\Controllers\AdminController@usercontact')->name('admin-send-message');

    Route::get('/newupdates', 'App\Http\Controllers\AdminController@newupdates')->name('admin-newupdate');
    Route::post('/newupdates/slider', 'App\Http\Controllers\AdminController@slider')->name('admin-newupdate-slider');

    Route::get('/email-templates', 'App\Http\Controllers\EmailController@index')->name('admin-mail-index');
    Route::get('/email-templates/{id}', 'App\Http\Controllers\EmailController@edit')->name('admin-mail-edit');
    Route::post('/email-templates/{id}', 'App\Http\Controllers\EmailController@update')->name('admin-mail-update');
    Route::get('/email-config', 'App\Http\Controllers\EmailController@config')->name('admin-mail-config');
    Route::Post('/email-config', 'App\Http\Controllers\EmailController@configupdate')->name('admin-mail-configupdate');
    Route::get('/groupemail', 'App\Http\Controllers\EmailController@groupemail')->name('admin-group-show');
    Route::post('/groupemailpost', 'App\Http\Controllers\EmailController@groupemailpost')->name('admin-group-submit');
    Route::get('/comment/{status}', 'App\Http\Controllers\GeneralSettingController@comment')->name('admin-gs-comment');
    Route::get('/affilate/{status}', 'App\Http\Controllers\GeneralSettingController@isaffilate')->name('admin-gs-isaffilate');
    Route::get('/faqup/{status}', 'App\Http\Controllers\PageSettingController@faqupdate')->name('admin-faq-update');
    Route::get('/contact/{status}', 'App\Http\Controllers\PageSettingController@contactup')->name('admin-ps-contactup');
    Route::get('/issmtp/{status}', 'App\Http\Controllers\GeneralSettingController@issmtp')->name('admin-gs-issmtp');
    Route::get('/talkto/{status}', 'App\Http\Controllers\GeneralSettingController@talkto')->name('admin-gs-talkto');
    Route::get('/loader/{status}', 'App\Http\Controllers\GeneralSettingController@isloader')->name('admin-gs-isloader');
    Route::get('/currencyup/{status}', 'App\Http\Controllers\PageSettingController@currencyup')->name('admin-cur-update');
    Route::get('/langup/{status}', 'App\Http\Controllers\GeneralSettingController@lungup')->name('admin-lung-update');
    Route::get('/facebook/{status}', 'App\Http\Controllers\SocialSettingController@facebookup')->name('admin-social-facebookup');
    Route::get('/google/{status}', 'App\Http\Controllers\SocialSettingController@googleup')->name('admin-social-googleup');
    Route::get('/loader/{status}', 'App\Http\Controllers\GeneralSettingController@isloader')->name('admin-gs-isloader');
  });
});

Route::prefix('customer')->group(function () {
  Route::post('/register', 'App\Http\Controllers\Auth\CustomerRegisterController@register')->name('customer-register-submit');
  Route::post('/login', 'App\Http\Controllers\Auth\CustomerLoginController@login')->name('customer-login-submit');
  Route::get('/forgot', 'App\Http\Controllers\Auth\CustomerForgotController@showforgotform')->name('customer-forgot');
  Route::post('/forgot', 'App\Http\Controllers\Auth\CustomerForgotController@forgot')->name('customer-forgot-submit');
  Route::get('/login', 'App\Http\Controllers\Auth\CustomerLoginController@showLoginForm')->name('customer-login');
  Route::get('/register', 'App\Http\Controllers\Auth\CustomerRegisterController@showRegisterForm')->name('customer-register');
  Route::get('/logout', 'App\Http\Controllers\Auth\CustomerLoginController@logout')->name('customer-logout');
  Route::group(['middleware' => 'isCustomer'], function () {
    Route::get('/dashboard', 'App\Http\Controllers\CustomerController@index')->name('customer-dashboard');
    Route::get('/package', 'App\Http\Controllers\CustomerController@package')->name('customer-package');
    Route::get('/wishlist', 'App\Http\Controllers\CustomerController@wishlists')->name('customer-wishlist');
    Route::get('/wishlists', 'App\Http\Controllers\CustomerController@wishlist')->name('customer-wishlists');

    Route::get('/favorites', 'App\Http\Controllers\CustomerController@favorites')->name('customer-favorites');
    Route::get('/wishlists/{sort}', 'App\Http\Controllers\CustomerController@wishlistsort')->name('customer-wishlistsort');
    Route::get('/wishlist/product/{id}/delete', 'App\Http\Controllers\CustomerController@delete')->name('customer-wish-delete');
    Route::get('/favorite/vendor/{id}/delete', 'App\Http\Controllers\CustomerController@favdelete')->name('customer-favorite-delete');
    Route::get('/reset', 'App\Http\Controllers\CustomerController@resetform')->name('customer-reset');
    Route::post('/reset', 'App\Http\Controllers\CustomerController@reset')->name('customer-reset-submit');
    Route::get('/profile', 'App\Http\Controllers\CustomerController@profile')->name('customer-profile');
    Route::post('/profile', 'App\Http\Controllers\CustomerController@profileupdate')->name('customer-profile-update');


    Route::get('/messages', 'App\Http\Controllers\CustomerController@messages')->name('customer-messages');
    Route::get('/message/{id}', 'App\Http\Controllers\CustomerController@message')->name('customer-message');
    Route::get('/message/{id}/delete', 'App\Http\Controllers\CustomerController@messagedelete')->name('customer-message-delete');
    Route::post('/message/post', 'App\Http\Controllers\CustomerController@postmessage')->name('customer-message-post');

    Route::get('admin/messages', 'App\Http\Controllers\CustomerController@adminmessages')->name('customer-message-index');
    Route::get('admin/message/{id}', 'App\Http\Controllers\CustomerController@adminmessage')->name('customer-message-show');
    Route::post('admin/message/post', 'App\Http\Controllers\CustomerController@adminpostmessage')->name('customer-message-store');
    Route::get('admin/message/{id}/delete', 'App\Http\Controllers\CustomerController@adminmessagedelete')->name('customer-message-delete1');


    Route::get('/orders', 'App\Http\Controllers\CustomerController@orders')->name('customer-orders');

    Route::get('/orders', 'App\Http\Controllers\CustomerOrderController@index')->name('customer-order-index');
    Route::get('/order/{id}/show', 'App\Http\Controllers\CustomerOrderController@show')->name('customer-order-show');
    Route::get('/vendors/{id}/show', 'App\Http\Controllers\CustomerOrderController@show')->name('customer-vendor-show');

    Route::get('/order/{id}/invoice', 'App\Http\Controllers\CustomerOrderController@invoice')->name('customer-order-invoice');
    Route::get('/order/{id}/print', 'App\Http\Controllers\CustomerOrderController@printpage')->name('customer-order-print');
    Route::get('/orders/status/{status}', 'App\Http\Controllers\CustomerOrderController@ordersstatus')->name('customer-vendor-status');
    Route::post('/order/{id}/license', 'App\Http\Controllers\CustomerOrderController@license')->name('customer-order-license');
    Route::get('/order/{slug}/status/{status}', 'App\Http\Controllers\CustomerOrderController@orderStatusCompleted')->name('customer-order-status');

  });
});

//////////////user routes
Route::prefix('user')->group(function () {

  Route::get('/forgot', 'App\Http\Controllers\Auth\UserForgotController@showforgotform')->name('user-forgot');
  Route::post('/forgot', 'App\Http\Controllers\Auth\UserForgotController@forgot')->name('user-forgot-submit');
  Route::get('/login', 'App\Http\Controllers\Auth\UserLoginController@showLoginForm')->name('user-login');
  Route::post('/login', 'App\Http\Controllers\Auth\UserLoginController@login')->name('user-login-submit');
  Route::get('/register', 'App\Http\Controllers\Auth\UserRegisterController@showRegisterForm')->name('user-register');
  Route::post('/register', 'App\Http\Controllers\Auth\UserRegisterController@register')->name('user-register-submit');
  Route::get('/logout', 'App\Http\Controllers\Auth\UserLoginController@logout')->name('user-logout');

  Route::get('/package', 'App\Http\Controllers\UserController@package')->name('user-package');

  Route::prefix('vendor')->group(function () {
    Route::get('/subscription/{id}', 'App\Http\Controllers\UserController@vendorrequest')->name('user-vendor-request');
    Route::get('/unsubscribe/{id}', 'App\Http\Controllers\UserController@unsub_package')->name('unsub-vendor-package');
    // Route::get('/unsubscribe/{id}', 'App\Http\Controllers\UserController@unsub_package')->name('unsub-vendor-package');
    Route::post('/vendor-request', 'App\Http\Controllers\UserController@vendorrequestsub')->name('user-vendor-request-submit');

    Route::post('/paypal/submit', 'App\Http\Controllers\SubscribePaypalController@store')->name('user.paypal.submit');
    Route::get('/paypal/cancle', 'App\Http\Controllers\SubscribePaypalController@paycancle')->name('user.payment.cancle');
    Route::get('/paypal/return', 'App\Http\Controllers\SubscribePaypalController@payreturn')->name('user.payment.return');
    Route::post('/paypal/notify', 'App\Http\Controllers\SubscribePaypalController@notify')->name('user.payment.notify');
    Route::post('/stripe/submit', 'App\Http\Controllers\SubscribeApp\Http\Controllers\StripeController@store')->name('user.stripe.submit');

    Route::post('/payment', 'App\Http\Controllers\PaymentController@store')->name('payment.submit');
    Route::get('/payment/cancle', 'App\Http\Controllers\PaymentController@paycancle')->name('payment.cancle');
    Route::get('/payment/return', 'App\Http\Controllers\PaymentController@payreturn')->name('payment.return');
  });

  Route::middleware('CheckVendorPackage')->group(function () {

    Route::get('/dashboard', 'App\Http\Controllers\UserController@index')->name('user-dashboard');
    Route::get('/wishlist', 'App\Http\Controllers\UserController@wishlists')->name('user-wishlist');
    Route::get('/wishlists', 'App\Http\Controllers\UserController@wishlist')->name('user-wishlists');
    Route::get('/favorites', 'App\Http\Controllers\UserController@favorites')->name('user-favorites');
    Route::get('/wishlists/{sort}', 'App\Http\Controllers\UserController@wishlistsort')->name('user-wishlistsort');
    Route::get('/wishlist/product/{id}/delete', 'App\Http\Controllers\UserController@delete')->name('user-wish-delete');
    Route::get('/favorite/vendor/{id}/delete', 'App\Http\Controllers\UserController@favdelete')->name('user-favorite-delete');
    Route::get('/reset', 'App\Http\Controllers\UserController@resetform')->name('user-reset');
    Route::post('/reset', 'App\Http\Controllers\UserController@reset')->name('user-reset-submit');
    Route::get('/profile', 'App\Http\Controllers\UserController@profile')->name('user-profile');
    Route::post('/profile', 'App\Http\Controllers\UserController@profileupdate')->name('user-profile-update');

    Route::get('/settings/logo', 'App\Http\Controllers\VendorSliderController@logo')->name('user-gs-logo');
    Route::post('/settings/logo', 'App\Http\Controllers\VendorSliderController@logoup')->name('user-gs-logoup');

    Route::get('/settings/gif', 'App\Http\Controllers\VendorSliderController@gif')->name('user-gs-gif');
    Route::post('/settings/gif', 'App\Http\Controllers\VendorSliderController@gifup')->name('user-gs-gifup');

    Route::post('/user/contact', 'App\Http\Controllers\UserController@usercontact');
    Route::get('/orders', 'App\Http\Controllers\UserController@orders')->name('user-orders');
    Route::get('/order/{id}', 'App\Http\Controllers\UserController@order')->name('user-order');
    Route::get('/order/{slug}/{id}', 'App\Http\Controllers\UserController@orderdownload')->name('user-order-download');
    Route::get('print/order/print/{id}', 'App\Http\Controllers\UserController@orderprint')->name('user-order-print');

    Route::get('/customer', 'App\Http\Controllers\UserController@vendorcustomer')->name('vendor-customer');
    Route::get('/users', 'App\Http\Controllers\UserController@index')->name('user-index');
    Route::get('/{id}/show', 'App\Http\Controllers\UserController@show')->name('user-show');

    Route::get('/messages', 'App\Http\Controllers\UserController@messages')->name('user-messages');
    Route::get('/message/{id}', 'App\Http\Controllers\UserController@message')->name('user-message');
    Route::post('/message/post', 'App\Http\Controllers\UserController@postmessage')->name('user-message-post');
    Route::get('/message/{id}/delete', 'App\Http\Controllers\UserController@messagedelete')->name('user-message-delete');
    //new
    Route::get('admin/messages', 'App\Http\Controllers\UserController@adminmessages')->name('user-message-index');
    Route::get('admin/message/{id}', 'App\Http\Controllers\UserController@adminmessage')->name('user-message-show');
    Route::post('admin/message/post', 'App\Http\Controllers\UserController@adminpostmessage')->name('user-message-store');
    Route::get('admin/message/{id}/delete', 'App\Http\Controllers\UserController@adminmessagedelete')->name('user-message-delete1');
    Route::post('admin/user/send/message', 'App\Http\Controllers\UserController@adminusercontact')->name('user-send-message');



    ///vendor group-----------------------------------------------------------
    Route::prefix('vendor')->group(function () {
      Route::get('/affilate/code', 'App\Http\Controllers\UserController@affilate_code')->name('user-affilate-code');
      Route::get('/affilate/withdraw', 'App\Http\Controllers\UserWithdrawController@index')->name('user-wwt-index');
      Route::get('/affilate/withdraw/create', 'App\Http\Controllers\UserWithdrawController@create')->name('user-wwt-create');
      Route::post('/affilate/withdraw/create', 'App\Http\Controllers\UserWithdrawController@store')->name('user-wwt-store');

      //================================================================================================
      //middleware for vendor
      Route::group(['middleware' => 'isVendor'], function () {

        Route::get('/product', 'App\Http\Controllers\UserProductController@index')->name('user-prod-index');
        Route::get('/product/create', 'App\Http\Controllers\UserProductController@create')->name('user-prod-create');
        Route::post('/product/create', 'App\Http\Controllers\UserProductController@store')->name('user-prod-store');
        Route::post('/product/create1', 'App\Http\Controllers\UserProductController@store1')->name('user-prod-store1');
        Route::post('/product/create2', 'App\Http\Controllers\UserProductController@store2')->name('user-prod-store2');
        Route::get('/product/edit/{id}', 'App\Http\Controllers\UserProductController@edit')->name('user-prod-edit');
        Route::post('/product/update/{id}', 'App\Http\Controllers\UserProductController@update')->name('user-prod-update');
        Route::post('/product/update1/{id}', 'App\Http\Controllers\UserProductController@update1')->name('user-prod-update1');
        Route::post('/product/update2/{id}', 'App\Http\Controllers\UserProductController@update2')->name('user-prod-update2');
        Route::get('/product/delete/{id}', 'App\Http\Controllers\UserProductController@destroy')->name('user-prod-delete');
        Route::get('/product/status/{id1}/{id2}', 'App\Http\Controllers\UserProductController@status')->name('user-prod-st');
        Route::get('/product/high/{id1}/{id2}', 'App\Http\Controllers\UserProductController@high')->name('user-prod-high');

        Route::get('/slider', 'App\Http\Controllers\VendorSliderController@index')->name('user-sl-index');
        Route::get('/slider/create', 'App\Http\Controllers\VendorSliderController@create')->name('user-sl-create');
        Route::post('/slider/create', 'App\Http\Controllers\VendorSliderController@store')->name('user-sl-store');
        Route::get('/slider/edit/{id}', 'App\Http\Controllers\VendorSliderController@edit')->name('user-sl-edit');
        Route::post('/slider/edit/{id}', 'App\Http\Controllers\VendorSliderController@update')->name('user-sl-update');
        Route::get('/slider/delete/{id}', 'App\Http\Controllers\VendorSliderController@destroy')->name('user-sl-delete');

        Route::get('/shop', 'App\Http\Controllers\UserController@shop')->name('user-shop-desc');
        Route::post('/shop', 'App\Http\Controllers\UserController@shopup')->name('user-shop-descup');

        Route::get('/social', 'App\Http\Controllers\UserController@social')->name('user-social-index');
        Route::post('/social/update', 'App\Http\Controllers\UserController@socialupdate')->name('user-social-update');
        Route::get('/orders', 'App\Http\Controllers\UserController@vendororders')->name('vendor-order-index');
        Route::get('/ordersstatus/{status}', 'App\Http\Controllers\UserController@vendorordersstatus')->name('vendor-status');
        Route::get('/order/{slug}/show', 'App\Http\Controllers\UserController@vendororder')->name('vendor-order-show');
        Route::get('/order/{slug}/show1', 'App\Http\Controllers\UserController@vendorordershow')->name('vendor-order-show1');
        Route::get('/order/{slug}/invoice', 'App\Http\Controllers\UserController@invoice')->name('vendor-order-invoice');
        Route::get('/order/{slug}/print', 'App\Http\Controllers\UserController@printpage')->name('vendor-order-print');
        Route::get('/order/{slug}/status/{status}', 'App\Http\Controllers\UserController@status')->name('vendor-order-status');
        Route::get('/order/email/{email}', 'App\Http\Controllers\UserController@email')->name('vendor-order-email');
        Route::post('/order/email/', 'App\Http\Controllers\UserController@emailsub')->name('vendor-order-emailsub');
        Route::post('/order/{slug}/license/', 'App\Http\Controllers\UserController@vendorlicense')->name('vendor-order-license');
        Route::get('/shipping-cost', 'App\Http\Controllers\UserController@ship')->name('user-shop-ship');
        Route::post('/shipping-cost', 'App\Http\Controllers\UserController@shipup')->name('user-shop-shipup');
        Route::get('/withdraw', 'App\Http\Controllers\VendorWithdrawController@index')->name('user-wt-index');
        Route::get('/withdraw/create', 'App\Http\Controllers\VendorWithdrawController@create')->name('user-wt-create');
        Route::post('/withdraw/create', 'App\Http\Controllers\VendorWithdrawController@store')->name('user-wt-store');
        Route::get('/frenchiseinfo', 'App\Http\Controllers\UserController@frenchiseinfo')->name('user-fren-info');
      });
      //------------------------------end vendor middle ware

    });
  });
});
/////////////////user end--------------------------------------------------------------------------------


// sub head office route
Route::prefix('sub-head-office')->group(function () {
  Route::get('/login', 'App\Http\Controllers\Auth\SubHeadOfficeLoginController@showLoginForm')->name('sub-head-office-login');
  Route::post('/login', 'App\Http\Controllers\Auth\SubHeadOfficeLoginController@login')->name('sho-login-submit');

  Route::post('/register', 'App\Http\Controllers\SubHeadOfficeController@create_sub_head_office')->name('create_sub_head_office');
  Route::get('/logout', 'App\Http\Controllers\Auth\SubHeadOfficeLoginController@logout')->name('sho-logout');

  Route::middleware('isSubHeadOffice')->group(function () {
    Route::get('/dashboard', 'App\Http\Controllers\SubHeadOfficeController@index')->name('sub_head_office_dashboard');
    Route::get('/profile', 'App\Http\Controllers\SubHeadOfficeController@sho_profile')->name('sub_head_office_profile');
    Route::post('/profile/update', 'App\Http\Controllers\SubHeadOfficeController@profile_update')->name('sub_head_office_profile_update');

    Route::get('/reset/password', 'App\Http\Controllers\SubHeadOfficeController@view_password_reset')->name('sub_head_office_reset_psswd');
    Route::post('/update/password', 'App\Http\Controllers\SubHeadOfficeController@update_password')->name('sub_head_office_update_psswd');

    // Orders
    Route::prefix('order')->group(function () {
      Route::get('{status}', 'App\Http\Controllers\SubHeadOfficeController@orders_by_status')->name('sub_head_office_orders_by_status');
      Route::get('{status}/vendor/{vendor}', 'App\Http\Controllers\SubHeadOfficeController@orders_by_vendor_and_status')->name('sub_head_office_orders_by_vendor_and_status');
      Route::get('{id}/details', 'App\Http\Controllers\SubHeadOfficeController@view_order')->name('sub_head_office_order_details');
      Route::get('{id}/invoice', 'App\Http\Controllers\SubHeadOfficeController@view_order_invoice')->name('sub_head_office_order_invoice');
      Route::get('{id}/print', 'App\Http\Controllers\SubHeadOfficeController@print_page')->name('sub_head_office_order_print');
      Route::get('{id}/status/{status}', 'App\Http\Controllers\SubHeadOfficeController@update_order_status_by_id')->name('sub_head_office_update_order_status_by_id');
      Route::post('email/', 'App\Http\Controllers\SubHeadOfficeController@emailsub')->name('sub_head_office_order_emailsub');
      Route::post('{id}/license', 'App\Http\Controllers\SubHeadOfficeController@license')->name('sub_head_office_order_license');
      Route::get('{id}/license', 'App\Http\Controllers\SubHeadOfficeController@license')->name('sub_head_office_order_license');
    });

    // Franchises
    Route::prefix('franchise')->group(function () {
      Route::get('/all/{status}', 'App\Http\Controllers\SubHeadOfficeController@list_franchise_by_status')->name('sub_head_office_frenchises_by_status');
      Route::get('/list', 'App\Http\Controllers\SubHeadOfficeController@list_franchise')->name('sub_head_office_frenchises');
      Route::get('/add', 'App\Http\Controllers\SubHeadOfficeController@add_franchise')->name('sub_head_office_frenchise_add');
      Route::get('/{id}/show', 'App\Http\Controllers\SubHeadOfficeController@details_franchise')->name('sub_head_office_frenchise_details');
      Route::get('/{id}/dashboard', 'App\Http\Controllers\SubHeadOfficeController@dashboard_franchise')->name('sub_head_office_frenchise_dashboard');
      Route::get('/edit/{id}', 'App\Http\Controllers\SubHeadOfficeController@edit')->name('sub_head_office_frenchise_edit');
      Route::post('/edit/{id}', 'App\Http\Controllers\SubHeadOfficeController@update')->name('sub_head_office_frenchise_update');
      Route::post('/create', 'App\Http\Controllers\SubHeadOfficeController@create_franchise')->name('sub_head_office_frenchise_create');
      Route::get('/{franchise}/orders/{status}', 'App\Http\Controllers\SubHeadOfficeController@franchise_orders_by_status')->name('franchise_orders_by_status');
      Route::get('/{franchise}/customers/list', 'App\Http\Controllers\SubHeadOfficeController@franchise_vendors_customers')->name('sub_head_office_frenchise_customers');
      Route::get('/customer/{user}/show', 'App\Http\Controllers\SubHeadOfficeController@customer_show')->name('sub_head_office_frenchise_customer_show');

      Route::get('/{franchise}/vender/list', 'App\Http\Controllers\SubHeadOfficeController@franchise_vendors_list')->name('sub_head_office_franchise_vendors_list');
    });

    // Vendors
    Route::prefix('vendor')->group(function () {
      Route::get('/all', 'App\Http\Controllers\SubHeadOfficeController@vendors_list')->name('sub_head_office_vendors');
      Route::get('/{id}/details', 'App\Http\Controllers\SubHeadOfficeController@vendor_details')->name('sub_head_office_vendor_details');
      Route::get('/{id}/edit', 'App\Http\Controllers\SubHeadOfficeController@vendor_update')->name('sub_head_office_vendor_edit');

      Route::get('/withdraw/list', 'App\Http\Controllers\SubHeadOfficeController@withdraws_list')->name('sub_head_office_vendors_width');
      Route::get('/withdraw/{id}/details', 'App\Http\Controllers\SubHeadOfficeController@withdraw_details')->name('sub_head_office_vendor_wtd');
      Route::get('/withdraw/{id}/accept', 'App\Http\Controllers\SubHeadOfficeController@withdraw_accept')->name('sub_head_office_vendor_wt_accept');
      Route::get('/withdraw/{id}/reject', 'App\Http\Controllers\SubHeadOfficeController@withdraw_reject')->name('sub_head_office_vendor_wt_reject');
      Route::get('/withdraw/pending/list', 'App\Http\Controllers\SubHeadOfficeController@withdraw_pendings')->name('sub_head_office_vendor_pending');

      Route::get('/subscriptions/list', 'App\Http\Controllers\SubHeadOfficeController@subs_list')->name('sub_head_office_vendor_subs_list');
      Route::get('/subscription/{id}/details', 'App\Http\Controllers\SubHeadOfficeController@sub_details')->name('sub_head_office_vendor_sub_details');
    });
  });
});
// end sub head office


// Route::get('admin/check/movescript', 'App\Http\Controllers\AdminController@movescript')->name('admin-move-script');
// Route::get('admin/generate/backup', 'App\Http\Controllers\AdminController@generate_bkup')->name('admin-generate-backup');
// Route::get('admin/activation', 'App\Http\Controllers\AdminController@activation')->name('admin-activation-form');
// Route::post('admin/activation', 'App\Http\Controllers\AdminController@activation_submit')->name('admin-activate-purchase');
// Route::get('admin/clear/backup', 'App\Http\Controllers\AdminController@clear_bkup')->name('admin-clear-backup');

// Route::post('the/genius/ocean/2441139', 'App\Http\Controllers\FrontendController@subscription');
Route::get('finalize', 'App\Http\Controllers\FrontendController@finalize');

Route::get('/', 'App\Http\Controllers\FrontendController@index')->name('front.index');
Route::get('/category_data/{id}', 'App\Http\Controllers\FrontendController@index1'); //->name('front.index1');///////
Route::get('/category_index/{id}', 'App\Http\Controllers\FrontendController@category_index'); //->name('front.category_index');/////

Route::get('/extra', 'App\Http\Controllers\FrontendController@extraIndex')->name('front.extraIndex');
Route::get('/lang/{id}', 'App\Http\Controllers\FrontendController@lang')->name('front.lang');
Route::get('/currency/{id}', 'App\Http\Controllers\FrontendController@currency')->name('front.curr');
Route::get('/faq', 'App\Http\Controllers\FrontendController@faq')->name('front.faq');
Route::get('/contact', 'App\Http\Controllers\FrontendController@contact')->name('front.contact');
Route::get('/category/{slug}', 'App\Http\Controllers\FrontendController@category')->name('front.category');
Route::get('/category/{slug}/{sort}', 'App\Http\Controllers\FrontendController@categorysort');
Route::get('/subcategory/{slug}', 'App\Http\Controllers\FrontendController@subcategory')->name('front.subcategory');
Route::get('/subcategory/{slug}/{sort}', 'App\Http\Controllers\FrontendController@subcategorysort');
Route::get('/childcategory/{slug}', 'App\Http\Controllers\FrontendController@childcategory')->name('front.childcategory');
Route::get('/childcategory/{slug}/{sort}', 'App\Http\Controllers\FrontendController@childcategorysort');

Route::get('/allproduct/{ptype}', 'App\Http\Controllers\FrontendController@allproduct')->name('front.allproduct');
Route::get('/allproductsort/{ptype}/{sort}', 'App\Http\Controllers\FrontendController@allproductsort');
Route::get('/hotsale/{ptype}', 'App\Http\Controllers\FrontendController@hotsale')->name('front.hotsale');
Route::get('/dealsofday/{ptype}', 'App\Http\Controllers\FrontendController@dealsofday2')->name('front.dealsofday2');
Route::get('/latestspecial/{ptype}', 'App\Http\Controllers\FrontendController@latestspecial')->name('front.latestspecial');
Route::get('/festival/{ptype}', 'App\Http\Controllers\FrontendController@festival')->name('front.festival');


Route::get('/product/{id}/{slug}', 'App\Http\Controllers\FrontendController@product')->name('front.product');
Route::post('/product/review', 'App\Http\Controllers\FrontendController@reviewsubmit')->name('front.review.submit');
Route::get('/cart', 'App\Http\Controllers\FrontendController@cart')->name('front.cart');
Route::get('/compare', 'App\Http\Controllers\FrontendController@compare')->name('front.compare');
Route::get('/checkout', 'App\Http\Controllers\FrontendController@checkout')->name('front.checkout');

Route::get('/tags/{tag}', 'App\Http\Controllers\FrontendController@tags')->name('front.tags');
Route::get('/search', 'App\Http\Controllers\FrontendController@search')->name('front.search');
Route::get('/search/{search}', 'App\Http\Controllers\FrontendController@searchs')->name('front.searchs');
Route::get('/search/{search}/{sort}', 'App\Http\Controllers\FrontendController@searchss')->name('front.searchss');
Route::get('/blog', 'App\Http\Controllers\FrontendController@blog')->name('front.blog');
Route::get('/blog/{id}', 'App\Http\Controllers\FrontendController@blogshow')->name('front.blogshow');
Route::post('/contact', 'App\Http\Controllers\FrontendController@contactemail')->name('front.contact.submit');
Route::post('/subscribe', 'App\Http\Controllers\FrontendController@subscribe')->name('front.subscribe.submit');
Route::post('/vendor/contact', 'App\Http\Controllers\FrontendController@vendorcontact')->name('front.vendor.contact');

Route::post('/payment', 'App\Http\Controllers\PaymentController@store')->name('payment.submit');
Route::get('/payment/cancle', 'App\Http\Controllers\PaymentController@paycancle')->name('payment.cancle');
Route::get('/payment/return', 'App\Http\Controllers\PaymentController@payreturn')->name('payment.return');
Route::get('/package/payment/return', 'App\Http\Controllers\PaymentController@package_pay_return')->name('package.payment.return');
Route::post('/payment/notify', 'App\Http\Controllers\PaymentController@notify')->name('payment.notify');

Route::post('/stripe-submit', 'App\Http\Controllers\StripeController@store')->name('stripe.submit');
Route::post('/cashondelivery', 'App\Http\Controllers\FrontendController@cashondelivery')->name('cash.submit');
Route::post('/mobile_money', 'App\Http\Controllers\FrontendController@mobilemoney')->name('mobile.submit');
Route::post('/bank_wire', 'App\Http\Controllers\FrontendController@bankwire')->name('bank.submit');
Route::post('/gateway', 'App\Http\Controllers\FrontendController@gateway')->name('gateway.submit');
Route::get('/contact/refresh_code', 'App\Http\Controllers\FrontendController@refresh_code');
Route::get('/stopcaptcha/{str}', 'App\Http\Controllers\FrontendController@adsstop')->name('stopcaptcha');
Route::get('/reload-captcha', 'App\Http\Controllers\FrontendController@reloadCaptcha');

Route::post('/vendor/registration', 'App\Http\Controllers\FrontendController@vendor_register')->name('vendor.registration');
Route::get('/vendor/{slug}', 'App\Http\Controllers\VendorFrontController@vendor')->name('front.vendor');
Route::get('/vendor/{slug}/{sort}', 'App\Http\Controllers\VendorFrontController@vendorsort');

Route::get('/message/return', 'App\Http\Controllers\FrontendController@messagereturn')->name('message.return');

Route::get('/vendor/{slug1}/category/{slug2}', 'App\Http\Controllers\VendorFrontController@vendorcategory')->name('front.vendor.category');
Route::get('/vendor/{slug1}/category/{slug2}/{sort}', 'App\Http\Controllers\VendorFrontController@vendorcategorysort');


Route::get('/vendor/{slug1}/subcategory/{slug2}', 'App\Http\Controllers\VendorFrontController@vendorsubcategory')->name('front.vendor.subcategory');
Route::get('/vendor/{slug1}/subcategory/{slug2}/{sort}', 'App\Http\Controllers\VendorFrontController@vendorsubcategorysort');


Route::get('/vendor/{slug1}/childcategory/{slug2}', 'App\Http\Controllers\VendorFrontController@vendorchildcategory')->name('front.vendor.childcategory');
Route::get('/vendor/{slug1}/childcategory/{slug2}/{sort}', 'App\Http\Controllers\VendorFrontController@vendorchildcategorysort');

Route::get('auth', 'App\Http\Controllers\Auth\SocialRegisterController@redirectToProvider')->name('social-provider');
Route::get('auth/{provider}/callback', 'App\Http\Controllers\Auth\SocialRegisterController@handleProviderCallback');


Route::get('pages/{id}', 'App\Http\Controllers\FrontendController@page')->name('front.pages');
Route::get('/email/test', 'App\Http\Controllers\EmailController@sendMail')->name('mail.test');
Route::get('userss/testlogin', 'API\auth_api@login');

// frenchies rout////
// Route::get('/list','App\Http\Controllers\FrontendController@frenchisealllist')->name('frenchisealllist');
Route::get('/frenchisealllist/vendor/list/{id}', 'App\Http\Controllers\FrontendController@frenchisevendorlist')->name('frenchise-vendor-list');


Route::get('/latestupdates', 'App\Http\Controllers\FrontendController@comingsoon')->name('comingsoon');
Route::get('countrieslist/{slug}', 'App\Http\Controllers\FrontendController@countrieslist')->name('countrieslist');
Route::get('provinceslist/{slug}', 'App\Http\Controllers\FrontendController@provinceslist')->name('provinceslist');
Route::get('citieslist/{slug}', 'App\Http\Controllers\FrontendController@citieslist')->name('citieslist');
Route::get('city/{slug}/shops/list', 'App\Http\Controllers\FrontendController@cities_shops')->name('cities_shops');


Route::get('/frenchisealllist/{slug}', 'App\Http\Controllers\FrontendController@frenchisealllist')->name('front.frenchisealllist');
Route::get('advancesearch', 'App\Http\Controllers\FrontendController@advancesearch')->name('advancesearch');
Route::get('/settings/gif', 'App\Http\Controllers\VendorSliderController@gif')->name('user-gs-gif');
Route::post('/settings/gif', 'App\Http\Controllers\VendorSliderController@gifup')->name('user-gs-gifup');
route::get('/productsearch', 'App\Http\Controllers\FrontendController@productsearch')->name('productsearch');
route::get('/shopsearch', 'App\Http\Controllers\FrontendController@shopsearch')->name('shopsearch');
route::get('/roadsearch', 'App\Http\Controllers\FrontendController@roadsearch')->name('roadsearch');
route::get('/brandsearch', 'App\Http\Controllers\FrontendController@brandsearch')->name('brandsearch');
route::get('/frenchisesearch', 'App\Http\Controllers\FrontendController@frenchisesearch')->name('frenchisesearch');
route::get('privacypolicy', 'App\Http\Controllers\FrontendController@privacypolicy')->name('privacypolicy');
route::get('termsconditions', 'App\Http\Controllers\FrontendController@termsconditions')->name('termsconditions');
route::get('working', 'App\Http\Controllers\FrontendController@working')->name('working');
route::get('bankalfalah', 'App\Http\Controllers\FrontendController@bankalfalah')->name('bankalfalah');
route::get('apply', 'App\Http\Controllers\FrontendController@apply')->name('apply');
route::get('magnifier', 'App\Http\Controllers\FrontendController@magnifier')->name('magnifier');
Route::post('/save-frenchise', 'App\Http\Controllers\FrontendController@storeFrenchise')->name('frenchise-submit');

Route::get('/charges/leopard/{product}/{vendor}/{city}', 'App\Http\Controllers\Shipping\LeopardsController@get_charges')->name('get_delivery_charges');



// Common Endpoints
Route::post('/check/vendor/order/qty/qlty', 'App\Http\Controllers\CommonController@change_vendor_qty_qlty')->name('change_vendor_qty_qlty');


 route::get('callback', 'App\Http\Controllers\Auth\SocialRegisterController@callbackFromGoogle');



//Clear Cache facade value:
Route::get('/clear-cache', function () {
  $exitCode = Artisan::call('cache:clear');
  return '<h1>Cache facade value cleared</h1>';
});

//Reoptimized class loader:
Route::get('/optimize', function () {
  $exitCode = Artisan::call('optimize');
  return '<h1>Reoptimized class loader</h1>';
});

//Route cache:
Route::get('/route-cache', function () {
  $exitCode = Artisan::call('route:cache');
  return '<h1>Routes cached</h1>';
});

//Clear Route cache:
Route::get('/route-clear', function () {
  $exitCode = Artisan::call('route:clear');
  return '<h1>Route cache cleared</h1>';
});

//Clear View cache:
Route::get('/view-clear', function () {
  $exitCode = Artisan::call('view:clear');
  return '<h1>View cache cleared</h1>';
});

//Clear Config cache:
Route::get('/config-cache', function () {
  $exitCode = Artisan::call('config:cache');
  return '<h1>Clear Config cleared</h1>';
});
