<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Classes\GeniusMailer;
use App\Models\Generalsetting;
use App\Http\Requests\StoreValidationRequest;
use App\Models\User;
use App\Models\Vendororder;
use App\Models\Product;
use App\Models\Counter;
use App\Models\Currency;
use App\Models\Order;
use App\Models\UserSubscription;
use App\Models\Withdraw;
use App\Models\Customer;
use App\Models\Frenchise;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;
use Hash;

class AdminVendorController extends Controller
{
    public function __construct()
    {
        //$this->middleware('auth:admin');
    }

    public function index()
    {
        $users = User::where('is_vendor','=',2)->orWhere('is_vendor','=',1)->orderBy('id','desc')->get();
        $pendings = User::where('is_vendor','=',1)->get()->count();
        return view('admin.vendor.index',compact('users','pendings')); 
    }

    public function vendororder($uid)
    { 
        $orders = Vendororder::where('user_id',$uid)->orderBy('id','desc')->get();
        return view('admin.frenchise.vendororder.index',compact('orders','uid'));
    }

    public function ordersstatus($uid,$status)
    {
        $orders = Vendororder::where('user_id','=',$uid)->where('status','=',$status)->orderBy('id','desc')->get();
        return view('admin.frenchise.vendororder.index',compact('uid','orders'));
    }

    public function vendorDashbord($uid)
    {
        $vendor   = User::where('id',$uid)->first(); 
        $order    = Vendororder::where('user_id',$uid)->get();
        $products = Product::where('user_id',$uid)->get(); 
        $pending   = Vendororder::where('user_id',$uid)->where('status','=','pending')->get();
        $processing = Vendororder::where('user_id',$uid)->where('status','=','processing')->get();
        $completed  = Vendororder::where('user_id',$uid)->where('status','=','completed')->get();
        $referrals  = Counter::where('type','referral')->orderBy('total_count','desc')->take(5)->get();
        $browsers   = Counter::where('type','browser')->orderBy('total_count','desc')->take(5)->get();
        $currency_sign = Currency::where('is_default','=',1)->first();
        $days = ""; 
        $sales = "";
        for($i = 0; $i < 30; $i++) {
            $days .= "'".date("d M", strtotime('-'. $i .' days'))."',";

            $sales .=  "'".Order::where('status','=','completed')->whereDate('created_at', '=', date("Y-m-d", strtotime('-'. $i .' days')))->count()."',";
        } 

         $sale_daily = Order::where('user_id', $uid)->whereDate('created_at', '=', date('Y-m-d'))->where('status','completed')->sum('pay_amount');
        $sale_monthly = Order::where('user_id', $uid)->whereMonth('created_at', Carbon::now()->month)->where('status','completed')->sum('pay_amount');

        return view('admin.frenchise.vendor_dashboard',compact('products','currency_sign','pending','processing','completed','sale_daily','sale_monthly','referrals','browsers','days','sales','vendor','uid','order'));
    }

    public function customerlist($fid)
    {
        $frenchise = Frenchise::where('id',$fid)->first(); 
        $vendors = User::where('frenchise_id',$frenchise->id)->get()->pluck('id');
        $c_order = Vendororder::whereIn('user_id',$vendors)->groupBy('order_id')->get()->pluck('order_id');
        $cust = Order::whereIn('id',$c_order)->groupBy('user_id')->pluck('user_id');
        $customer = Customer::whereIn('id',$cust)->orderBy('id','desc')->get();
        return view('admin.frenchise.customerlist',compact('customer'));
    }

    public function customershow($id)
    {
        $user = Customer::findOrFail($id);
        return view('admin.frenchise.customerdetail',compact('user'));
    }

    public function subs()
    {

        $subs = UserSubscription::where('status','=',1)->orderBy('id','desc')->get();
        return view('admin.vendor.subscriptions',compact('subs'));
    }

    public function sub($id)
    {
        $subs = UserSubscription::findOrFail($id);
        return view('admin.vendor.subdetails',compact('subs'));
    } 

    public function pending()
    {
        $users = User::where('is_vendor','=',1)->orderBy('id','desc')->get();
        return view('admin.vendor.pendings',compact('users'));
    }

    public function status($id1,$id2)
    {
        $user = User::findOrFail($id1);
        $user->is_vendor = $id2;
        $user->update();
        Session::flash('success', 'Vendor Status Upated Successfully.');
        return redirect()->back();
    }

    public function storeFrenchise(Request $request)
    {
       $request->validate([
            'email' => 'required|unique:frenchises',
            'sub_head_office_id' => 'required'
        ]);
        $data=array(
			'reg_number'=>$request->reg_number,
            'owner_name'=>$request->owner_name,
            'father_name'=>$request->father_name,
            'cnic'=>$request->cnic,
			'frenchise_address'=>$request->frenchise_address,
			'religion'=>$request->religion,
			'province'=>$request->province,
			'city'=>$request->city,
			'address'=>$request->address,
			'home_address'=>$request->home_address,
			'bank_account_1'=>$request->bank_account_1,
			'bank_account_2'=>$request->bank_account_2,
            'mobile_number'=>$request->mobile_number,
            'mobile_number_1'=>$request->mobile_number_1,
            'submit_amount'=>$request->submit_amount,
            'remaining_amount'=>$request->remaining_amount,
            'duration'=>$request->duration,
            'partner'=>$request->partner,
            'percentage'=>$request->percentage,
            'monthly_percentage'=>$request->monthly_percentage,
            'yearly_percentage'=>$request->yearly_percentage,
            'completion_percentage'=>$request->completion_percentage,
            'sale_tax'=>$request->sale_tax,
            'registration_tax'=>$request->registration_tax,
            'other_expenses'=>$request->other_expenses,
            'vitnes'=>$request->vitnes,
            'father_vitnes'=>$request->father_vitnes,
            'cnic_vitnes'=>$request->cnic_vitnes,
            'vitnes_address'=>$request->vitnes_address,
            'vitnes_mobile'=>$request->vitnes_mobile,
            'vitnes_mobile_1'=>$request->vitnes_mobile_1,
            'frenchise_name'=>$request->frenchise_name,
			'frenchise_mobile_number'=>$request->frenchise_mobile_number,
			'vendor_limit'=>$request->vendor_limit,
            'email'=>$request->email,
            'password'=>$request->password,
			'area'=>$request->area,
			'frenchise_message'=>$request->frenchise_message,
			'frenchise_detail'=>$request->frenchise_detail,
            'sub_head_office_id' => $request->sub_head_office_id,
        );
        $data['password'] = Hash::make($data['password']);
        if ($file = $request->file('photo')) 
        {              
            $name = time().$file->getClientOriginalName();
            $file->move('assets/images',$name);           
            $input['photo'] = $name;
            $data['photo'] = $name; 
        }
        Frenchise::create($data);
        return redirect()->Route('admin-frenchise-index')->with('success','Franchise Created Successfully.');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('admin.vendor.edit',compact('user'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $data = $request->all();
        if ($file = $request->file('photo'))
        {
            $name = time().$file->getClientOriginalName();
            $file->move('assets/images',$name);
            if($user->photo != null)
            {
                if (file_exists(public_path().'/assets/images/'.$user->photo)) {
                    unlink(public_path().'/assets/images/'.$user->photo);
                }
            }
            $data['photo'] = $name;
        }
        $user->update($data);
        return redirect()->route('admin-vendor-index')->with('success','Vendor Information Updated Successfully.');
    }

    public function show($id)
    {
        $user = User::findOrFail($id);
        $frenchise = Frenchise::get();
        return view('admin.vendor.details',compact('user','frenchise')); 
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        Session::flash('success', 'Vendor Removed Successfully');
        return redirect()->route('admin-vendor-index');
    }
    
    public function withdraws()
    {
        $withdraws = Withdraw::where('type','=','vendor')->orderBy('id','desc')->get();
        $pending = Withdraw::where('status','=','pending')->get()->count();
        return view('admin.vendor.withdraws',compact('withdraws','pending'));
    }

    public function pendings()
    {
        $withdraws = Withdraw::where('status','=','pending')->where('type','=','vendor')->orderBy('id','desc')->get();
        return view('admin.vendor.pending-withdraws',compact('withdraws'));
    }

    public function withdrawdetails($id)
    {
        $withdraw = Withdraw::findOrFail($id);
        return view('admin.vendor.withdraw-details',compact('withdraw'));
    }

    public function accept($id)
    {
        $withdraw = Withdraw::findOrFail($id);
        $data['status'] = "completed";
        $withdraw->update($data);
        return redirect()->route('admin-vendor-wt')->with('success','Withdraw Accepted Successfully');
    }

    public function reject($id)
    {
        $withdraw = Withdraw::findOrFail($id);
        $account = User::findOrFail($withdraw->user->id);
        $account->current_balance = $account->current_balance + $withdraw->amount + $withdraw->fee;
        $account->update();
        $data['status'] = "rejected";
        $withdraw->update($data);
        return redirect()->route('admin-vendor-wt')->with('success','Withdraw Rejected Successfully');
    }
    
    public function userwithdraws()
    {
        $withdraws = Withdraw::where('type','=','affilate')->orderBy('id','desc')->get();
        $pending = Withdraw::where('status','=','pending')->get()->count();
        return view('admin.user.withdraws',compact('withdraws','pending'));
    }

    public function userpendings()
    {
        $withdraws = Withdraw::where('status','=','pending')->where('type','=','affilate')->orderBy('id','desc')->get();
        return view('admin.user.pending-withdraws',compact('withdraws'));
    }

    public function userwithdrawdetails($id)
    {
        $withdraw = Withdraw::findOrFail($id);
        return view('admin.user.withdraw-details',compact('withdraw'));
    }

    public function useraccept($id)
    {
        $withdraw = Withdraw::findOrFail($id);
        $data['status'] = "completed";
        $withdraw->update($data);
        return redirect()->route('admin-vendor-wtt')->with('success','Withdraw Accepted Successfully');
    }

    public function userreject($id)
    {
        $withdraw = Withdraw::findOrFail($id);
        $account = User::findOrFail($withdraw->user->id);
        $account->affilate_income = $account->affilate_income + $withdraw->amount + $withdraw->fee;
        $account->update();
        $data['status'] = "rejected";
        $withdraw->update($data);
        return redirect()->route('admin-vendor-wtt')->with('success','Withdraw Rejected Successfully');
    }

    public function vendor_chartdetail(Request $request,$uid)
    {
       
       // $frenchise = Frenchise::where('id', $fid)->first();
       // $vendors = User::all()->where('frenchise_id', $frenchise->id);

         $orders  = User::leftjoin('orders','users.id','=','orders.user_id')
        ->where('users.id',$uid)
        ->where('orders.status','=','completed')
        ->whereMonth('orders.created_at',Carbon::now()->month)
        ->get(['users.sale_tax','orders.*']);

        // $orders = Order::where('user_id',$uid)->where('status','=','completed')->whereMonth('created_at', Carbon::now()->month)->get();


         return view('admin.frenchise.vendor_monthly_chartdetail',compact('orders'));
    }
}
