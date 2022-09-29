<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\UpdateValidationRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use App\Models\Head;
use App\Models\Order;
use App\Models\Vendororder;
use App\Models\Frenchise;
use App\Models\Product;
use App\Models\Subscriber;
use App\Models\UserSubscription;
use App\Models\Counter;
use App\Models\User;
use App\Models\Withdraw;
use App\Classes\GeniusMailer;
use App\Models\Generalsetting;
use App\Models\Notification;
use App\Models\FrenchiseNotification;
use App\Models\Currency;
use App\Models\Customer;
use App\Models\VendorSubscription;
use Carbon\Carbon;
use Auth;

class AdminSubHeadOfficeController extends Controller
{
    public function create_sub_head_office(Request $request)
    {
        $request->validate([
            'email' => 'required|unique:heads',
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
        );
        $data['password'] = Hash::make($data['password']);
        Head::create($data);
        return redirect()->back()->with('success', 'Sub Head Office Created Successfully!');
    }

    public function index(Head $user)
    {
        $headoffice = Head::where('id',$user->id)->first();
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


        

        $frenchises = Frenchise::select('id','percentage','monthly_percentage','yearly_percentage','sale_tax','registration_tax','other_expenses')->where('sub_head_office_id','=',$user->id)->get();
        //$vendors = User::whereIn('frenchise_id', $frenchises)->get()->pluck('id');
        //$order   = Order::whereIn('user_id', $vendors)->get();


        $userSubscription_daily = $this->chart('daily',$frenchises);
        $userSubscription_monthly = $this->chart('monthly',$frenchises);
        $userSubscription_yearly = $this->chart('yearly',$frenchises);


        $frenchises1 = Frenchise::all()->where('sub_head_office_id','=',$user->id)->pluck('id');
        $vendors = User::whereIn('frenchise_id', $frenchises1)->get()->pluck('id');

        $duration = '+'. $headoffice->duration . ' years';
        $select_date = UserSubscription::whereIn('user_id', $vendors)->first();
        $start_date = $select_date->created_at->format('Y-m-d');
        $end_date = date('Y-m-d',strtotime($duration.$start_date));

        $userSubscription_contract = $this->chart('contract',$frenchises,$end_date);
        
        
        //$userSubscription_daily = UserSubscription::whereIn('user_id', $vendors)->whereDate('created_at', '=', date('Y-m-d'))->sum('price');
        //$userSubscription_monthly = UserSubscription::whereIn('user_id', $vendors)->whereMonth('created_at', Carbon::now()->month)->whereYear('created_at', Carbon::now()->year)->sum('price');
        //$userSubscription_yearly = UserSubscription::whereIn('user_id', $vendors)->whereYear('created_at', Carbon::now()->year)->sum('price');

        //$frenchises = Frenchise::all()->where('sub_head_office_id','=',$user->id)->pluck('id');
        //$vendors = User::whereIn('frenchise_id', $frenchises)->get()->pluck('id');
        
        
        // $select_date = UserSubscription::whereIn('user_id', $vendors)->first();
        // $start_date = $select_date->created_at->format('Y-m-d');
        // $end_date = date('Y-m-d',strtotime($duration.$start_date));
        
        // $userSubscription_contract = UserSubscription::whereIn('user_id', $vendors)->where('created_at','<=', $end_date)->sum('price');
        

        //Yearly chart month wise
        // $yearlychart = array();
        // $month = date('Y-1-1');
        // $chart_company = array();
        // $chart_monthly = array();
        // $chart_sale_tax = array();
        // $chart_registration_tax = array();
        // $chart_other_expenses = array();

        // for($i=1; $i<=12; $i++)
        // {
        //     $array = array();
        //     $sales = UserSubscription::whereIn('user_id', $vendors)->whereYear('created_at', '=', date('Y',strtotime($month)))->whereMonth('created_at', '=', date('m',strtotime($month)))->sum('price');
        //     //$sales = Order::where('status', '=', 'completed')->whereMonth('created_at', '=', $month)->count();

        //     $chart_monthly_data = $sales/100*$headoffice->monthly_percentage;
        //     $chart_sale_tax_data = $sales/100*$headoffice->sale_tax;
        //     $chart_registration_tax_data = $sales/100*$headoffice->registration_tax;
        //     $chart_other_expenses_data = $sales/100*$headoffice->other_expenses;
        //     $chart_company_data = $sales - ($chart_monthly_data+$chart_sale_tax_data+$chart_registration_tax_data+$chart_other_expenses_data);
            

        //     $chart_company[] = [
        //          "group_name" => "Company",
        //          "name" => date('M',strtotime($month)),
        //          "value" => $chart_company_data
        //     ];
            
        //     $chart_monthly[] = [
        //         "group_name" => "Monthly",
        //         "name" => date('M',strtotime($month)),
        //         "value" => $chart_monthly_data
        //    ];

        //    $chart_sale_tax[] = [
        //         "group_name" => "Sale Tax",
        //         "name" => date('M',strtotime($month)),
        //         "value" => $chart_sale_tax_data
        //    ];

        //    $chart_registration_tax[] = [
        //         "group_name" => "Registration Tax",
        //         "name" => date('M',strtotime($month)),
        //         "value" => $chart_registration_tax_data
        //    ];

        //    $chart_other_expenses[] = [
        //         "group_name" => "Other Expenses",
        //         "name" => date('M',strtotime($month)),
        //         "value" => $chart_other_expenses_data
        //    ];

        //     $month = date('Y-m-d',strtotime("+1 month ". $month));
        // }

        // $yearlychart = array_merge(
        //     $chart_company,
        //     $chart_monthly,
        //     $chart_sale_tax,
        //     $chart_registration_tax,
        //     $chart_other_expenses
        // );
        // $yearlychart = json_encode($yearlychart);
        //End Yearly chart month wise



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
                'user',
                'userSubscription_daily',
                'userSubscription_monthly',
                'userSubscription_yearly',
                'userSubscription_contract',
                'headoffice',
            )
        );
    }

    public function chart($type,$data,$end_date=false)
    {
        $response = 0;

        foreach($data as $frenchise)
        {
            $total = 0;
            $vendors = User::where('frenchise_id', $frenchise->id)->get()->pluck('id');
            
            if($type == 'daily')
                {
                    $total += UserSubscription::whereIn('user_id', $vendors)->whereDate('created_at', '=', date('Y-m-d'))->sum('price');
                }
                else if($type == 'monthly')
                {
                    $total += UserSubscription::whereIn('user_id', $vendors)->whereMonth('created_at', Carbon::now()->month)->whereYear('created_at', Carbon::now()->year)->sum('price');
                }
                else if($type == 'yearly')
                {
                    $total += UserSubscription::whereIn('user_id', $vendors)->whereYear('created_at', Carbon::now()->year)->sum('price');
                }
                else if($type == 'contract')
                {
                    $total += UserSubscription::whereIn('user_id', $vendors)->where('created_at','<=', $end_date)->sum('price');
                }
                else if($type == 'yearlychart')
                {
                    
                }
            
            

            if($type == 'daily' or $type == 'monthly')
            {
                $response += $total - (($total/100* (float)$frenchise->monthly_percentage)+($total/100* (float)$frenchise->sale_tax)+($total/100* (float)$frenchise->registration_tax)+($total/100* (float)$frenchise->other_expenses));
            }
            else if($type == 'yearly' or $type =='contract')
            {
                $response += $total - (($total/100* (float)$frenchise->yearly_percentage)+($total/100* (float)$frenchise->sale_tax)+($total/100* (float)$frenchise->registration_tax)+($total/100* (float)$frenchise->other_expenses));
            }
           
        }
        return $response;
    }

    public function add_sub_head_office()
    {
        return view('admin.subheadoffice.add_sub_head_office');
    }

    public function sub_head_office_list()
    {
        $data['heads'] = Head::all();
        return view('admin.subheadoffice.sub_head_office_list', $data);
    }

    public function delete_sub_head_office($id)
    {
        Head::where('id', $id)->delete();
        return redirect()->route('admin.sub_head_office_list')->with('success', 'Sub Head Office Delete Successfully.');;
    }

    public function edit_sub_head_office($id)
    {
        $frenchise = Head::findOrFail($id);
        return view('admin.subheadoffice.edit', compact('frenchise'));
    }

    public function update_sub_head_office(Request $request, $id)
    {
        $frenchise = Head::findOrFail($id);
        $data = $request->all();
        $frenchise->update($data);
        return redirect()->route('admin.sub_head_office_list')->with('success', 'Sub Head Office Information Updated Successfully.');
    }

    public function update_sub_head_office_status($id1, $id2)
    {
        $frenchise = Head::findOrFail($id1);
        $frenchise->status = $id2;
        $frenchise->update();
        Session::flash('success', 'Sub Head Office Status Upated Successfully.');
        return redirect()->back();
    }

    public function show_sub_head_office($id)
    {
        $heads = Head::findOrFail($id);
        return view('admin.subheadoffice.details', compact('heads'));
    }

    // Orders Section
    public function orders_by_status(Head $user, $status)
    {
        $franchises_list = Frenchise::all()->where('sub_head_office_id','=',$user->id);
        $users = [];

        $orders = [];
        foreach ($franchises_list??[] as $franchise) {
            $fren_users = User::where('frenchise_id','=',$franchise->id)->where('is_vendor','=',2)->orWhere('is_vendor','=',1)->orderBy('id','desc')->get();
            foreach ($fren_users??[] as $key => $u) {
                $users[] = $u;
            }
        }

        foreach ($users??[] as $key => $u) {
            $temp_franchises_orders =  Vendororder::all()->where('user_id','=',$u->id);
            foreach ($temp_franchises_orders as $order) {
                $oList = [];
                if($status == 'all') $oList = Order::all()->where('id','=',$order->order_id);
                else $oList = Order::all()->where('id','=',$order->order_id)->where('status','=',$status);
                foreach ($oList as $key => $o) {
                    $orders[] = $o;
                }
            }
        }
        
        return view('admin.subheadoffice.order.index',compact('orders'));
    }

    public function view_order($id)
    {
        $order = Order::findOrFail($id);
        $cart = unserialize(gzuncompress(utf8_decode($order->cart)));
        return view('admin.subheadoffice.order.details',compact('order','cart'));
    }

    public function view_order_invoice($id)
    {
        $order = Order::findOrFail($id);
        $cart = unserialize(gzuncompress(utf8_decode($order->cart)));
        return view('admin.subheadoffice.order.invoice',compact('order','cart'));
    }

    public function print_page($id)
    {
        $order = Order::findOrFail($id);
        $cart = unserialize(gzuncompress(utf8_decode($order->cart)));
        return view('admin.order.print',compact('order','cart'));
    }

    public function emailsub(Request $request)
    {
        $gs = Generalsetting::findOrFail(1);
        if($gs->is_smtp == 1)
        {
            $data = [
                'to' => $request->to,
                'subject' => $request->subject,
                'body' => $request->message,
            ];
            $mailer = new GeniusMailer();
            $mailer->sendCustomMail($data);                
        }
        else
        {
            $data = 0;
            $headers = "From: ".$gs->from_name."<".$gs->from_email.">";
            $mail = mail($request->to,$request->subject,$request->message,$headers);
            if($mail) {   
                $data = 1;
            }
        }
        return response()->json($data);
    }

    public function license(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $cart = unserialize(gzuncompress(utf8_decode($order->cart)));
        $cart->items[$request->license_key]['license'] = $request->license;
        $order->cart = utf8_encode(gzcompress(serialize($cart), 9));
        $order->update();
        return redirect()->route('sub_head_office_order_details',$order->id)->with('success','Successfully Changed The License Key.');
    }

    public function update_order_status_by_id($id,$status)
    {
        $mainorder = Order::findOrFail($id);
        if ($mainorder->status == "completed"){
            return redirect()->back()->with('success','This Order is Already Completed');
        }else{
        if ($status == "completed"){
            foreach($mainorder->vendororders as $vorder)
            {
                $uprice = User::findOrFail($vorder->user_id);
                $uprice->current_balance = $uprice->current_balance + $vorder->price;
                $uprice->update();
            }
            $gs = Generalsetting::findOrFail(1);
            if($gs->is_smtp == 1)
            {
                $data = [
                    'to' => $mainorder->customer_email,
                    'subject' => 'Your order '.$mainorder->order_number.' is Confirmed!',
                    'body' => "Hello ".$mainorder->customer_name.","."\n Thank you for shopping with us. We are looking forward to your next visit.",
                ];

                $mailer = new GeniusMailer();
                $mailer->sendCustomMail($data);                
            }
            else
            {
               $to = $mainorder->customer_email;
               $subject = 'Your order '.$mainorder->order_number.' is Confirmed!';
               $msg = "Hello ".$mainorder->customer_name.","."\n Thank you for shopping with us. We are looking forward to your next visit.";
            $headers = "From: ".$gs->from_name."<".$gs->from_email.">";
               mail($to,$subject,$msg,$headers);                
            }
        }
        if ($status == "declined"){
            $gs = Generalsetting::findOrFail(1);
            if($gs->is_smtp == 1)
            {
                $data = [
                    'to' => $mainorder->customer_email,
                    'subject' => 'Your order '.$mainorder->order_number.' is Declined!',
                    'body' => "Hello ".$mainorder->customer_name.","."\n We are sorry for the inconvenience caused. We are looking forward to your next visit.",
                ];
            $mailer = new GeniusMailer();
            $mailer->sendCustomMail($data);
            }
            else
            {
               $to = $mainorder->customer_email;
               $subject = 'Your order '.$mainorder->order_number.' is Declined!';
               $msg = "Hello ".$mainorder->customer_name.","."\n We are sorry for the inconvenience caused. We are looking forward to your next visit.";
            $headers = "From: ".$gs->from_name."<".$gs->from_email.">";
               mail($to,$subject,$msg,$headers);
            }

        }
        $stat['status'] = $status;
        $stat['payment_status'] = ucfirst($status);
        $order = Vendororder::where('order_id','=',$id)->update(['status' => $status]);
        $mainorder->update($stat);
        return redirect()->back()->with('success','Order Status Updated Successfully');
        }
    }

    // Franchises
    public function add_franchise()
    {
        return view('admin.subheadoffice.franchise.add');
    }

    public function create_franchise(Head $user, Request $request)
    {
        $request->validate([
            'email' => 'required|unique:frenchises',
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
            'sub_head_office_id' => $user->id,
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
        return redirect()->Route('admin.sub_head_office_frenchises')->with('success','Franchise Created Successfully.');
    }

    public function list_franchise(Head $user)
    {
        $frenchises = Frenchise::where('sub_head_office_id','=',$user->id)->latest()->get();
        return view('admin.subheadoffice.franchise.index',compact('frenchises'));
    }

    public function list_franchise_by_status(Head $user, $status)
    {
        $frenchises = Frenchise::where('sub_head_office_id','=',$user->id)->where('status','=',$status == 'pending' ? '0' : '1')->latest()->get();
        return view('admin.subheadoffice.franchise.index',compact('frenchises'));
    }

    public function details_franchise($id)
    {
        $frenchise = Frenchise::findOrFail($id);
        return view('admin.subheadoffice.franchise.details',compact('frenchise'));
    }

    public function dashboard_franchise($fid)
    {
        $frenchise = Frenchise::where('id',$fid)->first();
        $vendors = User::where('frenchise_id',$frenchise->id)->get()->pluck('id');
        $orders   = Order::whereIn('user_id',$vendors)->get();
        $count_vendor =  count($vendors);
        $products = Product::whereIn('user_id',$vendors)->get();
        $pending = Order::whereIn('user_id',$vendors)->where('status','=','pending')->get();
        $processing = Order::whereIn('user_id',$vendors)->where('status','=','processing')->get();
        $completed = Order::whereIn('user_id',$vendors)->where('status','=','completed')->get();
        $referrals = Counter::where('type','referral')->orderBy('total_count','desc')->take(5)->get();
        $browsers = Counter::where('type','browser')->orderBy('total_count','desc')->take(5)->get();
        $currency_sign = Currency::where('is_default','=',1)->first();
        $c_order = Vendororder::whereIn('user_id',$vendors)->groupBy('order_id')->get()->pluck('order_id');
        $cust = Order::whereIn('id',$c_order)->groupBy('user_id')->pluck('user_id');
        $customer = Customer::whereIn('id',$cust)->orderBy('id','desc')->get();

        $days = "";
        $sales = "";
        for($i = 0; $i < 30; $i++) {
            $days .= "'".date("d M", strtotime('-'. $i .' days'))."',";
            $sales .=  "'".Order::where('status','=','completed')->whereDate('created_at', '=', date("Y-m-d", strtotime('-'. $i .' days')))->count()."',";
        }
        return view('admin.subheadoffice.franchise.frenchise_dashboard',compact('customer','orders','products','currency_sign','frenchise','count_vendor','pending','processing','completed','referrals','browsers','fid','days','sales'));
    }

    public function edit($id)
    {
        $frenchise = Frenchise::findOrFail($id);
        return view('admin.subheadoffice.franchise.edit',compact('frenchise'));
    }

    public function update_franchise(Request $request, $id)
    {
        $frenchise = Frenchise::findOrFail($id);
        $data = $request->all();
        if ($file = $request->file('photo'))
        {
            $name = time().$file->getClientOriginalName();
            $file->move('assets/images',$name);
            if($frenchise->photo != null)
            {
                if (file_exists(public_path().'/assets/images/'.$frenchise->photo)) {
                    unlink(public_path().'/assets/images/'.$frenchise->photo);
                }
            }
            $data['photo'] = $name;
        }
        $frenchise->update($data);
        return redirect()->route('admin.sub_head_office_frenchises')->with('success','Franchise Information Updated Successfully.');
    }

    public function franchise_orders_by_status(Head $user, Frenchise $franchise, $status)
    {
        $users = [];
        $vendors = User::where('frenchise_id',$franchise->id)->get()->pluck('id');
        $orders = [];
        if($status == 'all') $orders = Order::whereIn('user_id',$vendors)->get();
        else $orders = Order::whereIn('user_id',$vendors)->where('status','=',$status)->get();
        return view('admin.subheadoffice.order.index',compact('orders'));
    }

    // Customers
    public function franchise_vendors_customers(Frenchise $franchise)
    {
        $vendors = User::where('frenchise_id',$franchise->id)->get()->pluck('id');
        $c_order = Vendororder::whereIn('user_id',$vendors)->groupBy('order_id')->get()->pluck('order_id');
        $cust = Order::whereIn('id',$c_order)->groupBy('user_id')->pluck('user_id');
        $customer = Customer::whereIn('id',$cust)->orderBy('id','desc')->get();
        return view('admin.subheadoffice.franchise.customerlist',compact('customer'));
    }

    public function customer_show(Customer $user)
    {
        return view('admin.subheadoffice.franchise.customerdetail',compact('user'));
    }

    // Vendors
    public function franchise_vendors_list(Frenchise $franchise)
    {
        $users = User::where('frenchise_id',$franchise->id)->get();
        return view('admin.subheadoffice.vendor.index',compact('users'));
    }

    public function vendors_list(Head $user)
    {
        $franchise = Frenchise::where('sub_head_office_id',$user->id)->get()->pluck('id');
        $users = User::whereIn('frenchise_id',$franchise)->orderBy('id','desc')->get();
        return view('admin.subheadoffice.vendor.index',compact('users')); 
    }

    public function subheadoffice_chartdetail($head_id)
    {
        $headoffice = Head::where('id',$head_id)->first();
        
        $frenchises = Frenchise::all()->where('sub_head_office_id','=',$headoffice->id)->pluck('id');
        $vendors = User::whereIn('frenchise_id', $frenchises)->get()->pluck('id');

        $query  = Head::leftjoin('frenchises','heads.id','=','frenchises.sub_head_office_id')
        ->leftjoin('users','frenchises.id','=','users.frenchise_id')
        ->leftjoin('user_subscriptions','users.id','=','user_subscriptions.user_id')
        ->where('sub_head_office_id',$head_id)
        ->whereMonth('user_subscriptions.created_at',Carbon::now()->month)
        ->orderBy('user_subscriptions.id','desc')
        ->get(['users.shop_name','user_subscriptions.created_at','user_subscriptions.price','frenchises.frenchise_name','frenchises.registration_tax','frenchises.sale_tax','frenchises.other_expenses','frenchises.monthly_percentage','frenchises.yearly_percentage','frenchises.percentage']);

        return view('admin.subheadoffice.chart_details',compact('query'));

    }

}