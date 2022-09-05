<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Classes\GeniusMailer;
use Hash;
use Auth;
use App\Models\Notification;
use App\Models\Customer;
use App\Models\UsersAddresses;
use App\Mail\UserSendCode;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Session;
use App\Models\Generalsetting;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use App\Mail\NotifyMail;
use Mail;


class CustomerAuthController extends Controller
{
    public function customerlogin(Request $request)
  {
    try {
      $request->validate([
        'email' => 'email|required',
        'password' => 'required'
      ]);
      $credentials = request(['email', 'password']);
      if (!Auth::guard('customer')->attempt(['email' => $request->email, 'password' => $request->password])) {
         return response(['error'=>1,'data'=>'','message'=>'invalid email or password'], 400);
      }
      $customer = Customer::where('email', $request->email)->first();

      if ( ! Hash::check($request->password, $customer->password, [])) {
        throw new \Exception('Error in Login');
      }
      $tokenResult = $customer->createToken('authToken')->plainTextToken;
      return response()->json([
        'status_code' => 200,
        'status' => 1,
        'access_token' => $tokenResult,
        'token_type' => 'Bearer',
        'customer' => $customer,
      ]);
    } catch (Exception $error) {
      return response()->json([
          'status_code' => 500,
          'status' => 022,
          'message'=>'Invalid Username or Password',
        ]);
    }
  }


  public function register(Request $request)
  {
   
    if(!$request->name && !$request->email && !$request->password && !$request->c_password)
    {
      return response(['error'=>1,'data'=>'','message'=>'Missing parameters or invalid field'], 400);
    }

    $input = $request->all();
    $input['password'] = bcrypt($input['password']);
    $customer = Customer::create($input);
    if (!$customer) {
      return response()->json([
        'status_code' => 500,
        'status' => 0,
        'customer' => $customer,
      ]);
    }
    $tokenResult = $customer->createToken('authToken')->plainTextToken;
    return response()->json([
      'status_code' => 200,
      'status' => 1,
      'access_token' => $tokenResult,
      'token_type' => 'Bearer',
      'customer' => $customer,
    ]);
  }

 public function customer_forgot(Request $request)
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
      
              return response()->json([
                'status_code' => 500,
                'status' => 0,
                'failed' => 'Sorry! Please try again latter.',
              ]);

            }else{

              return response()->json([
                'status_code' => 500,
                'status' => 0,
                'success' => 'Your Password Reseted Successfully. Please Check your email for new Password.',
              ]);

            }
        }
        else{
            // user not found
            return response()->json([
                'status_code' => 500,
                'status' => 0,
                'unsuccess' => 'No Account Found With This Email.',
              ]);
        }

    }

  public function customer_profile(Request $request)
  {
    if($request->customer_id =="")
    {
       return response(['error'=>1,'data'=>'','message'=>'Missing parameters'], 400);
    }

    $customer_profile= Customer::where('id',$request->customer_id)->first();
    return response()->json([
      'status_code' => 200,
      'status' => 1,
      'customer_profile' => $customer_profile,
    ]);
    
  }

  public function customer_edit_profile(Request $request)
  {
    $customer = Auth::user();

    $customer_profile= Customer::where('id',$customer->id)->update([
      'name' => $request->name,
      'email' => $request->email,
      'dob' => $request->dob,
      'gender' => $request->gender,
      'city' => $request->city,
      'address' => $request->address,
      'phone' => $request->phone
    ]);

    return response()->json([
      'status_code' => 200,
      'status' => 1,
      'success' => 'your profile updated successfully.',
    ]);
    
  }


  public function customer_change_password(Request $request)
    {
        $validator = Validator::make($request->all(), [
        'new_password' => 'min:8',
        ]);

        if ($validator->fails())
        {
          return response()->json([
                'status_code' => 500,
                'status' => 0,
                'unsuccess' => 'New password must be 8 characters'
              ]);
        }
        else
        {
          $customer = Customer::where('id',$request->customer_id)->first();
          if (Hash::check($request->current_password, $customer->password)) {
                $input['password'] = Hash::make($request->new_password);
                $customer->update($input);

              return response()->json([
                'status_code' => 500,
                'status' => 0,
                'success' => 'Successfully updated your password',
              ]);
  
               
          } else {
                 return response()->json([
                'status_code' => 500,
                'status' => 0,
                'unsuccess' => 'Current password Does not match.',
              ]);
            }
        }  
    }

}
