<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\AdminUserConversation;
use App\Models\AdminUserMessage;
use App\Models\Advertise;
use App\Classes\GeniusMailer;
use App\Models\Head;
use App\Models\NewUpdate;
use App\Models\Counter;
use App\Http\Requests\StoreValidationRequest;
use App\Http\Requests\UpdateValidationRequest;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use App\Models\State;
use App\Models\Subscriber;
use App\Models\User;
use App\Models\Customer;
use App\Models\Vendororder;
use App\Models\UserNotification;
use App\Models\VendorSubscription;
use App\Models\UserSubscription;

use Carbon\Carbon;

use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use App\Models\Generalsetting;
use InvalidArgumentException;
use App\Models\Comment;
use App\Models\Country;
use App\Models\Frenchise;
use App\Models\Currency;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index()
    {

        $users = User::all();
        $products = Product::all();
        $subs = Subscriber::all();
        $pending = Order::where('status', '=', 'pending')->get();
        $processing = Order::where('status', '=', 'processing')->get();
        $completed = Order::where('status', '=', 'completed')->get();
        $referrals = Counter::where('type', 'referral')->orderBy('total_count', 'desc')->take(5)->get();
        $browsers = Counter::where('type', 'browser')->orderBy('total_count', 'desc')->take(5)->get();

        $days = "";
        $sales = "";
        for ($i = 0; $i < 30; $i++) {
            $days .= "'" . date("d M", strtotime('-' . $i . ' days')) . "',";

            $sales .=  "'" . Order::where('status', '=', 'completed')->whereDate('created_at', '=', date("Y-m-d", strtotime('-' . $i . ' days')))->count() . "',";
        }

        $activation_notify = "";
        return view('admin.index', compact('users', 'products', 'subs', 'pending', 'processing', 'completed', 'referrals', 'browsers', 'days', 'sales', 'activation_notify'));
    }

    public function profile()
    {
        return view('admin.profile');
    }

    public function reviews()
    {
        $reviews = Review::orderBy('id', 'desc')->get();
        return view('admin.reviews.index', compact('reviews'));
    }
    public function reviewdelete($id)
    {
        $pick = Review::findOrFail($id);
        $pick->delete();
        return redirect()->route('admin-review-index')->with('success', 'Review Deleted Successfully.');
    }
    public function reviewshow($id)
    {
        $review = Review::findOrFail($id);
        return view('admin.reviews.show', compact('review'));
    }

    public function comments()
    {
        $comments = Comment::orderBy('id', 'desc')->get();
        return view('admin.comment.index', compact('comments'));
    }
    public function commentdelete($id)
    {
        $comment = Comment::findOrFail($id);
        if ($comment->replies->count() > 0) {
            foreach ($comment->replies as $reply) {
                if ($reply->subreplies->count() > 0) {
                    foreach ($reply->subreplies as $subreply) {
                        $subreply->delete();
                    }
                }
                $reply->delete();
            }
        }
        $comment->delete();
        return redirect()->route('admin-comment-index')->with('success', 'Comment Deleted Successfully.');
    }
    public function commentshow($id)
    {
        $comment = Comment::findOrFail($id);
        return view('admin.comment.show', compact('comment'));
    }


    public function profileupdate(UpdateValidationRequest $request)
    {
        $input = $request->all();
        $admin = Auth::guard('admin')->user();
        if ($file = $request->file('photo')) {
            $name = time() . $file->getClientOriginalName();
            $file->move('assets/images', $name);
            if ($admin->photo != null) {
                if (file_exists(public_path() . '/assets/images/' . $admin->photo)) {
                    unlink(public_path() . '/assets/images/' . $admin->photo);
                }
            }
            $input['photo'] = $name;
        }

        $admin->update($input);
        Session::flash('success', 'Successfully updated your profile');
        return redirect()->back();
    }


    public function passwordreset()
    {
        return view('admin.reset-password');
    }

    public function changepass(Request $request)
    {
        $admin = Auth::guard('admin')->user();
        if ($request->cpass) {
            if (Hash::check($request->cpass, $admin->password)) {
                if ($request->newpass == $request->renewpass) {
                    $input['password'] = Hash::make($request->newpass);
                } else {
                    Session::flash('unsuccess', 'Confirm password does not match.');
                    return redirect()->back();
                }
            } else {
                Session::flash('unsuccess', 'Current password Does not match.');
                return redirect()->back();
            }
        }
        $admin->update($input);
        Session::flash('success', 'Successfully updated your password');
        return redirect()->back();
    }
    public function messages()
    {
        $convs = AdminUserConversation::all();
        return view('admin.message.index', compact('convs'));
    }

    public function message($id)
    {
        $conv = AdminUserConversation::findOrfail($id);
        return view('admin.message.create', compact('conv'));
    }
    public function messagedelete($id)
    {
        $conv = AdminUserConversation::findOrfail($id);
        if ($conv->messages->count() > 0) {
            foreach ($conv->messages as $key) {
                $key->delete();
            }
        }
        if ($conv->notifications->count() > 0) {
            foreach ($conv->notifications as $key) {
                $key->delete();
            }
        }
        $conv->delete();
        return redirect()->back()->with('success', 'Conversation Deleted Successfully');
    }
    public function postmessage(Request $request)
    {
        $msg = new AdminUserMessage();
        $input = $request->all();
        $msg->fill($input)->save();
        $notification = new UserNotification;
        $notification->user_id = $msg->conversation->user->id;
        $notification->conversation1_id = $msg->conversation->id;
        $notification->save();
        Session::flash('success', 'Message Sent!');
        return redirect()->back();
    }

    // frenchise

    public function addFrenchise()
    {
        return view('admin.frenchise.add');
    }

    public function listFrenchise()
    {
        $frenchises = Frenchise::latest()->get();
        return view('admin.frenchise.index', compact('frenchises'));
    }

    public function show($id)
    {
        $frenchise = Frenchise::findOrFail($id);
        return view('admin.frenchise.details', compact('frenchise'));
    }

    public function v_status($id1, $id2)
    {
        $user = User::findOrFail($id1);
        $package = $user->subscribes()->where('status', 1)->orderBy('id', 'desc')->first();

        $subs = VendorSubscription::findOrFail(1);
        $settings = Generalsetting::findOrFail(1);
        $today = Carbon::now()->format('Y-m-d');
        if ($id2 == 1) {
            $user->is_vendor = 1;
        } else {
            $user->is_vendor = 2;
        }
        $user->date = date('Y-m-d', strtotime($today . ' + ' . $subs->days . ' days'));
        $user->mail_sent = 1;
        $user->update();
        $sub = new UserSubscription;
        $sub->user_id = $user->id;
        $sub->subscription_id = $subs->id;
        $sub->title = $subs->title;
        $sub->currency = $subs->currency;
        $sub->currency_code = $subs->currency_code;
        $sub->price = $subs->price;
        $sub->days = $subs->days;
        $sub->allowed_products = $subs->allowed_products;
        $sub->details = $subs->details;
        $sub->method = 'Free';
        $sub->status = 0;
        $sub->save();
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
        Session::flash('success', 'Vendor Status Upated Successfully.');
        return redirect()->back();
    }

    public function status($id1, $id2)
    {
        //print_r($id2); exit;
        $frenchise = Frenchise::findOrFail($id1);
        $frenchise->status = $id2;
        $frenchise->update();
        Session::flash('success', 'Franchise Status Upated Successfully.');
        return redirect()->back();
    }

    public function edit($id)
    {
        $frenchise = Frenchise::findOrFail($id);
        return view('admin.frenchise.edit', compact('frenchise'));
    }

    public function update(Request $request, $id)
    {
        $frenchise = Frenchise::findOrFail($id);
        $data = $request->all();
        if ($file = $request->file('photo')) {
            $name = time() . $file->getClientOriginalName();
            $file->move('assets/images', $name);
            if ($frenchise->photo != null) {
                if (file_exists(public_path() . '/assets/images/' . $frenchise->photo)) {
                    unlink(public_path() . '/assets/images/' . $frenchise->photo);
                }
            }
            $data['photo'] = $name;
        }
        $frenchise->update($data);
        return redirect()->route('admin-frenchise-index')->with('success', 'Franchise Information Updated Successfully.');
    }

    public function destroy($id)
    {
        Frenchise::where('id', $id)->delete();
        return redirect()->route('admin-frenchise-index')->with('success', 'Franchise Delete Successfully.');;
    }

    public function storeFrenchise(Request $request)
    {
        $data = 1;
        $admin = Auth::guard('admin')->user();
        $user = User::where('email', '=', $request->to)->first();
        if (empty($user)) {
            $data = 0;
            return response()->json($data);
        }
        $gs = Generalsetting::findOrFail(1);
        $subject = $request->subject;
        $to = $request->to;
        $from = $admin->email;
        $msg = "Email: " . $from . "<br>Message: " . $request->message;
        if ($gs->is_smtp == 1) {


            $datas = [
                'to' => $to,
                'subject' => $subject,
                'body' => $msg,
            ];
            $mailer = new GeniusMailer();
            $mailer->sendCustomMail($datas);
        } else {
            $headers = "From: " . $gs->from_name . "<" . $gs->from_email . ">";
            mail($to, $subject, $msg, $headers);
        }

        $conv = AdminUserConversation::where('user_id', '=', $user->id)->where('subject', '=', $subject)->first();
        if (isset($conv)) {
            $msg = new AdminUserMessage();
            $msg->conversation_id = $conv->id;
            $msg->message = $request->message;
            $msg->save();
            return response()->json($data);
        } else {
            $message = new AdminUserConversation();
            $message->subject = $subject;
            $message->user_id = $user->id;
            $message->message = $request->message;
            $message->save();
            $notification = new UserNotification;
            $notification->user_id = $user->id;
            $notification->conversation1_id = $message->id;
            $notification->save();
            $msg = new AdminUserMessage();
            $msg->conversation_id = $message->id;
            $msg->message = $request->message;
            $msg->save();
            return response()->json($data);
        }
    }

    public function store(StoreValidationRequest $request)
    {
        $this->validate($request, [
            'photo' => 'required',
        ]);
        $prod = new Product;
        $sign = Currency::where('is_default', '=', 1)->first();
        $input = $request->all();
        if (in_array(null, $request->features) || in_array(null, $request->colors)) {
            $input['features'] = null;
            $input['colors'] = null;
        } else {
            $input['features'] = implode(',', $request->features);
            $input['colors'] = implode(',', $request->colors);
        }

        if (empty($request->scheck)) {
            $input['size'] = null;
        } else {
            $input['size'] = implode(',', $request->size);
        }


        if (empty($request->colcheck)) {
            $input['color'] = null;
        } else {
            $input['color'] = implode(',', $request->color);
        }

        if ($file = $request->file('photo')) {
            $name = time() . $file->getClientOriginalName();
            $file->move('assets/images', $name);
            $input['photo'] = $name;
        }

        if (!empty($request->tags)) {
            $input['tags'] = implode(',', $request->tags);
        }

        if ($request->pccheck == "") {
            $input['product_condition'] = 0;
        }
        if ($request->shcheck == "") {
            $input['ship'] = null;
        }
        if (!empty($request->meta_tag)) {
            $input['meta_tag'] = implode(',', $request->meta_tag);
        }
        if ($request->mescheck == "") {
            $input['measure'] = null;
        }
        if ($request->secheck == "") {
            $input['meta_tag'] = null;
            $input['meta_description'] = null;
        }
        $input['cprice'] = ($input['cprice'] / $sign->value);
        $input['pprice'] = ($input['pprice'] / $sign->value);
        $prod->fill($input)->save();
        $lastid = $prod->id;
        if ($files = $request->file('gallery')) {
            foreach ($files as  $key => $file) {
                if (in_array($key, $request->galval)) {
                    $gallery = new Gallery;
                    $name = time() . $file->getClientOriginalName();
                    $file->move('assets/images/gallery', $name);
                    $gallery['photo'] = $name;
                    $gallery['product_id'] = $lastid;
                    $gallery->save();
                }
            }
        }

        Session::flash('success', 'New Product added successfully.');
        return redirect()->route('admin-prod-index');
    }

    public function frenchiseAsign($uid, $fid)
    {
        $user = User::find($uid);
        if ($user) {
            User::where('id', $uid)->update(['frenchise_id' => $fid]);
        }

        Session::flash('success', 'Franchise Asign  successfully.');
        return redirect()->back();
    }

    public function dashboardFrenchise($fid)
    {
        $frenchise = Frenchise::where('id', $fid)->first();
        $vendors = User::where('frenchise_id', $frenchise->id)->get()->pluck('id');
        $order   = Order::whereIn('user_id', $vendors)->get();
        $count_vendor =  count($vendors);
        $products = Product::whereIn('user_id', $vendors)->get();
        $pending = Order::whereIn('user_id', $vendors)->where('status', '=', 'pending')->get();
        $processing = Order::whereIn('user_id', $vendors)->where('status', '=', 'processing')->get();
        $completed = Order::whereIn('user_id', $vendors)->where('status', '=', 'completed')->get();
        $referrals = Counter::where('type', 'referral')->orderBy('total_count', 'desc')->take(5)->get();
        $browsers = Counter::where('type', 'browser')->orderBy('total_count', 'desc')->take(5)->get();
        $currency_sign = Currency::where('is_default', '=', 1)->first();
        $c_order = Vendororder::whereIn('user_id', $vendors)->groupBy('order_id')->get()->pluck('order_id');
        $cust = Order::whereIn('id', $c_order)->groupBy('user_id')->pluck('user_id');
        $customer = Customer::whereIn('id', $cust)->orderBy('id', 'desc')->get();


        $userSubscription_daily = UserSubscription::whereIn('user_id', $vendors)->whereDate('created_at', '=', date('Y-m-d'))->sum('price');
        $userSubscription_monthly = UserSubscription::whereIn('user_id', $vendors)->whereMonth('created_at', Carbon::now()->month)->sum('price');
        $userSubscription_yearly = UserSubscription::whereIn('user_id', $vendors)->whereYear('created_at', Carbon::now()->year)->sum('price');

        if(!$userSubscription_daily){ $userSubscription_daily=1; }
        if(!$userSubscription_monthly){$userSubscription_monthly = 1;}
        if(!$userSubscription_yearly){$userSubscription_yearly=1;}


        //Yearly chart month wise
        $yearlychart = array();
        $month = date('Y-1-1');
        $chart_company = array();
        $chart_monthly = array();
        $chart_sale_tax = array();
        $chart_registration_tax = array();
        $chart_other_expenses = array();

        for($i=1; $i<=12; $i++)
        {
            $array = array();
            $sales = UserSubscription::whereIn('user_id', $vendors)->whereYear('created_at', '=', date('Y',strtotime($month)))->whereMonth('created_at', '=', date('m',strtotime($month)))->sum('price');
            //$sales = Order::where('status', '=', 'completed')->whereMonth('created_at', '=', $month)->count();

            $chart_monthly_data = $sales/100*$frenchise->monthly_percentage;
            $chart_sale_tax_data = $sales/100*$frenchise->sale_tax;
            $chart_registration_tax_data = $sales/100*$frenchise->registration_tax;
            $chart_other_expenses_data = $sales/100*$frenchise->other_expenses;
            $chart_company_data = $sales - ($chart_monthly_data+$chart_sale_tax_data+$chart_registration_tax_data+$chart_other_expenses_data);
            

            $chart_company[] = [
                 "group_name" => "Company",
                 "name" => date('M',strtotime($month)),
                 "value" => $chart_company_data
            ];
            
            $chart_monthly[] = [
                "group_name" => "Monthly",
                "name" => date('M',strtotime($month)),
                "value" => $chart_monthly_data
           ];

           $chart_sale_tax[] = [
                "group_name" => "Sale Tax",
                "name" => date('M',strtotime($month)),
                "value" => $chart_sale_tax_data
           ];

           $chart_registration_tax[] = [
                "group_name" => "Registration Tax",
                "name" => date('M',strtotime($month)),
                "value" => $chart_registration_tax_data
           ];

           $chart_other_expenses[] = [
                "group_name" => "Other Expenses",
                "name" => date('M',strtotime($month)),
                "value" => $chart_other_expenses_data
           ];

            $month = date('Y-m-d',strtotime("+1 month ". $month));
        }

        $yearlychart = array_merge(
            $chart_company,
            $chart_monthly,
            $chart_sale_tax,
            $chart_registration_tax,
            $chart_other_expenses
        );
        $yearlychart = json_encode($yearlychart);
        //End Yearly chart month wise

        $days = "";
        $sales = "";

        $start = $month = strtotime(date('Y-m-1')); //strtotime('2022-07-01');
        $end = strtotime(date('Y-m-t')); //strtotime('2022-07-31');
        while($month < $end)
        {
            $date = date('d M', $month);
            $days .= "'" . $date . "',";

            $sales .=  "'" . Order::where('status', '=', 'completed')->whereDate('created_at', '=', date('Y-m-d', $month))->count() . "',";
            $month = strtotime("+1 day", $month);
        }

        // for ($i = 0; $i < 30; $i++) {
        //     $days .= "'" . date("d M", strtotime('-' . $i . ' days')) . "',";
        //     $sales .=  "'" . Order::where('status', '=', 'completed')->whereDate('created_at', '=', date("Y-m-d", strtotime('-' . $i . ' days')))->count() . "',";
        // }
        return view('admin.frenchise.frenchise_dashboard', compact('customer', 'products', 'currency_sign', 'frenchise', 'count_vendor', 'pending', 'processing', 'completed', 'referrals', 'browsers', 'fid', 'days', 'sales','yearlychart','userSubscription_daily','userSubscription_monthly','userSubscription_yearly'));
    }

    public function newupdates()
    {
        $ad = NewUpdate::findOrFail(1);
        return view('admin.newupdates.index', compact('ad'));
    }

    public function slider(StoreValidationRequest $request)
    {
        $this->validate($request, [
            'mainslider' => 'mimes:jpeg,jpg,png',
            'mainslider1' => 'mimes:jpeg,jpg,png',
            'mainslider2' => 'mimes:jpeg,jpg,png',
            'sidebanner' => 'mimes:jpeg,jpg,png',
            'sidebanner1' => 'mimes:jpeg,jpg,png',
            'sidebanner2' => 'mimes:jpeg,jpg,png',
            'videobanner1' => 'mimes:jpeg,jpg,png',
            'videobanner2' => 'mimes:jpeg,jpg,png',
            'videobanner3' => 'mimes:jpeg,jpg,png',
            'tag' => 'mimes:jpeg,jpg,png',
        ], [
            'mainslider.mimes' => 'The Image type is invalid.',
            'mainslider1.mimes' => 'The Image type is invalid.',
            'mainslider2.mimes' => 'The Image type is invalid.',
            'sidebanner.mimes' => 'The Image type is invalid.',
            'sidebanner1.mimes' => 'The Image type is invalid.',
            'sidebanner2.mimes' => 'The Image type is invalid.',
            'videobanner1.mimes' => 'The Image type is invalid.',
            'videobanner2.mimes' => 'The Image type is invalid.',
            'videobanner3.mimes' => 'The Image type is invalid.',
            'tag.mimes' => 'The Image type is invalid.',
        ]);
        $ad = NewUpdate::findOrFail(1);
        $data = $request->all();

        if ($file = $request->file('mainslider')) {
            $name = time() . $file->getClientOriginalName();
            $file->move('assets/images', $name);
            if ($ad->mainslider != null) {
                if (file_exists(public_path() . '/assets/images/' . $ad->mainslider)) {
                    unlink(public_path() . '/assets/images/' . $ad->mainslider);
                }
            }
            $data['mainslider'] = $name;
        }
        if ($file = $request->file('mainslider1')) {
            $name = time() . $file->getClientOriginalName();
            $file->move('assets/images', $name);
            if ($ad->mainslider1 != null) {
                if (file_exists(public_path() . '/assets/images/' . $ad->mainslider1)) {
                    unlink(public_path() . '/assets/images/' . $ad->mainslider1);
                }
            }
            $data['mainslider1'] = $name;
        }
        if ($file = $request->file('mainslider2')) {
            $name = time() . $file->getClientOriginalName();
            $file->move('assets/images', $name);
            if ($ad->mainslider2 != null) {
                if (file_exists(public_path() . '/assets/images/' . $ad->mainslider2)) {
                    unlink(public_path() . '/assets/images/' . $ad->mainslider2);
                }
            }
            $data['mainslider2'] = $name;
        }
        if ($file = $request->file('sidebanner')) {
            $name = time() . $file->getClientOriginalName();
            $file->move('assets/images', $name);
            if ($ad->sidebanner != null) {
                if (file_exists(public_path() . '/assets/images/' . $ad->sidebanner)) {
                    unlink(public_path() . '/assets/images/' . $ad->sidebanner);
                }
            }
            $data['sidebanner'] = $name;
        }
        if ($file = $request->file('sidebanner1')) {
            $name = time() . $file->getClientOriginalName();
            $file->move('assets/images', $name);
            if ($ad->sidebanner1 != null) {
                if (file_exists(public_path() . '/assets/images/' . $ad->sidebanner1)) {
                    unlink(public_path() . '/assets/images/' . $ad->sidebanner1);
                }
            }
            $data['sidebanner1'] = $name;
        }
        if ($file = $request->file('sidebanner2')) {
            $name = time() . $file->getClientOriginalName();
            $file->move('assets/images', $name);
            if ($ad->sidebanner2 != null) {
                if (file_exists(public_path() . '/assets/images/' . $ad->sidebanner2)) {
                    unlink(public_path() . '/assets/images/' . $ad->sidebanner2);
                }
            }
            $data['sidebanner2'] = $name;
        }
        if ($file = $request->file('tag')) {
            $name = time() . $file->getClientOriginalName();
            $file->move('assets/images', $name);
            if ($ad->tag != null) {
                if (file_exists(public_path() . '/assets/images/' . $ad->tag)) {
                    unlink(public_path() . '/assets/images/' . $ad->tag);
                }
            }
            $data['tag'] = $name;
        }
        if ($file = $request->file('videobanner1')) {
            $name = time() . $file->getClientOriginalName();
            $file->move('assets/images', $name);
            if ($ad->videobanner1 != null) {
                if (file_exists(public_path() . '/assets/images/' . $ad->videobanner1)) {
                    unlink(public_path() . '/assets/images/' . $ad->videobanner1);
                }
            }
            $data['videobanner1'] = $name;
        }
        if ($file = $request->file('videobanner2')) {
            $name = time() . $file->getClientOriginalName();
            $file->move('assets/images', $name);
            if ($ad->videobanner2 != null) {
                if (file_exists(public_path() . '/assets/images/' . $ad->videobanner2)) {
                    unlink(public_path() . '/assets/images/' . $ad->videobanner2);
                }
            }
            $data['videobanner2'] = $name;
        }
        if ($file = $request->file('videobanner3')) {
            $name = time() . $file->getClientOriginalName();
            $file->move('assets/images', $name);
            if ($ad->videobanner3 != null) {
                if (file_exists(public_path() . '/assets/images/' . $ad->videobanner3)) {
                    unlink(public_path() . '/assets/images/' . $ad->videobanner3);
                }
            }
            $data['videobanner3'] = $name;
        }
        $ad->update($data);
        return redirect()->route('admin-newupdate')->with('success', 'Data Updated Successfully.');
    }

    public function subHeadOffice()
    {
        return view('admin.subheadoffice.add_sub_head_office');
    }

    public function subHeadOfficeList()
    {
        $data['heads'] = Head::all();
        return view('admin.subheadoffice.sub_head_office_list', $data);
    }

    public function subHeadOfficeDashboard(Head $user)
    {
        $franchises_list = Frenchise::all()->where('sub_head_office_id','=',$user->id);
        $users = [];
        $products = Product::all();
        $subs = Subscriber::all();
        $pending = Order::where('status','=','pending')->get();
        $processing = Order::where('status','=','processing')->get();
        $completed = Order::where('status','=','completed')->get();
        $referrals = Counter::where('type','referral')->orderBy('total_count','desc')->take(5)->get();
        $browsers = Counter::where('type','browser')->orderBy('total_count','desc')->take(5)->get();
        $days = "";
        $sales = "";

        $franchises_orders = [];
        $franchises_orders_pending = [];
        $franchises_orders_processing = [];
        $franchises_orders_completed = [];
        foreach ($franchises_list??[] as $franchise) {
            $fren_users = User::where('frenchise_id','=',$franchise->id)->where('is_vendor','=',2)->orWhere('is_vendor','=',1)->orderBy('id','desc')->get();
            foreach ($fren_users??[] as $key => $u) {
                $users[] = $u;
            }
        }

        foreach ($users??[] as $key => $u) {
            $temp_franchises_orders =  Vendororder::all()->where('user_id','=',$u->id);
            foreach ($temp_franchises_orders->where('status','=','pending')??[] as $order) {
                $franchises_orders[] = $order;
                $franchises_orders_pending[] = $order;
            }
            foreach ($temp_franchises_orders->where('status','=','processing')??[] as $order) {
                $franchises_orders[] = $order;
                $franchises_orders_processing[] = $order;
            }
            foreach ($temp_franchises_orders->where('status','=','completed')??[] as $order) {
                $franchises_orders[] = $order;
                $franchises_orders_completed[] = $order;
            }
        }

        for($i = 0; $i < 30; $i++) {
            $days .= "'".date("d M", strtotime('-'. $i .' days'))."',";
            $sales .=  "'".Order::where('status','=','completed')->whereDate('created_at', '=', date("Y-m-d", strtotime('-'. $i .' days')))->count()."',";
        }
        $activation_notify = "";

        return view('admin.subheadoffice.index',
            compact(
                'franchises_list',
                'franchises_orders',
                'franchises_orders_pending',
                'franchises_orders_processing',
                'franchises_orders_completed',
                'users',
                'products',
                'subs',
                'pending',
                'processing',
                'completed',
                'referrals',
                'browsers',
                'days',
                'sales',
                'activation_notify',
            )
        );
    }

    public function addCountryCity()
    {
        return view('admin.addcountry&city.index');
    }

    public function listcountry()
    {
        // $data['states']= State::all();
        $cList = Country::all();
        return view('admin.addcountry&city.list', compact('cList'));
    }

    public function delete($id)
    {
        // State::where('id',$id)->delete();
        Country::where('id', $id)->delete();
        return redirect()->route('listcountry')->with('success', 'State Delete Successfully.');;
    }



    public function addcity(Request $request)
    {
        $data = request()->validate([
            "country" => 'required',
            "iso2" => 'required',
            "province" => 'required',
            "cities" => 'required',
        ]);
        $cities = explode(',', $data['cities']);
        // $state= new State();
        foreach ($cities as $c) {
            $country = new Country();
            $country->country = $data['country'];
            $country->admin_name = $data['province'];
            $country->iso2 = $data['iso2'];
            $country->city = $c;
            $country->capital = '';
            $country->save();
        }
        return redirect()->route('add-country-city')->withSuccess(['State Saved Successful!']);
    }


    public function chart_detail(Request $request,$fid)
    {
       $frenchise = Frenchise::where('id', $fid)->first();
       $vendors = User::all()->where('frenchise_id', $frenchise->id);

         $query  = Frenchise::leftjoin('users','frenchises.id','=','users.frenchise_id')
        ->leftjoin('user_subscriptions','users.id','=','user_subscriptions.user_id')
        ->where('frenchise_id',$fid)
        ->whereMonth('created_at',Carbon::now()->month)
        ->orderBy('user_subscriptions.id','desc')
        ->get(['users.shop_name','user_subscriptions.created_at','user_subscriptions.price','frenchises.frenchise_name','frenchises.registration_tax','frenchises.sale_tax','other_expenses','monthly_percentage']);


         return view('admin.frenchise.chart_detail',compact('query'));
    }

}
