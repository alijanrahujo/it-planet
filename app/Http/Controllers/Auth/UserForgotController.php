<?php

namespace App\Http\Controllers\Auth;
use App\Classes\GeniusMailer;
use Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use App\Models\User;
use App\Models\Customer;
use Illuminate\Support\Facades\Hash;
use App\Models\Generalsetting;
use Illuminate\Support\Str;
use App\Mail\NotifyMail;
use Mail;

class UserForgotController extends Controller
{
    public function __construct()
    {
      $this->middleware('guest:user', ['except' => ['logout']]);
    }

    public function showforgotform()
    {
    	return view('user.forgot');
    }

    public function forgot(Request $request)
    {
       
        $input =  $request->all();
        if (Customer::where('email', '=', $request->email)->count() > 0) {
            // user found
            $user = Customer::where('email', '=', $request->email)->firstOrFail();
            $password = rand(10000000, 99999999);
            $input['password'] = bcrypt($password);

            $user->update($input);

            Mail::to($request->email)->send(new NotifyMail($password));

            if (Mail::failures()) {
                return 'Sorry! Please try again latter';
            }else{
                Session::flash('success', 'Your Password Reseted Successfully. Please Check your email for new Password.');
                return redirect()->route('user-forgot');
            }
        }
        else{
            // user not found
            Session::flash('unsuccess', 'No Account Found With This Email.');
            return redirect()->route('user-forgot');
        }

    }
    
}
