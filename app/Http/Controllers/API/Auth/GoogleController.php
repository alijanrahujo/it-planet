<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sociallink; 
use App\Models\User;
use App\Models\Customer;
use Auth;
use Config;
use Illuminate\Support\Facades\Session;
use Socialite;

class GoogleController extends Controller
{
     public function loginWithGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callbackFromGoogle()
    {
         try {
      
            $customer = Socialite::driver('google')->user();
       
            $findcustomer = Customer::where('google_id', $customer->id)->first();
       
            if($findcustomer){
            	$token = $findcustomer->createToken('my-app-token')->plainTextToken;
                return response(['error'=>0,'token'=>$token,'data'=>$findcustomer,'message'=>'success'], 200);
       
                
            }else{
                $newUser = Customer::create([
                    'name' => $customer->name,
                    'email' => $customer->email,
                    'google_id'=> $customer->id
                ]);
      
                Auth::guard('customer')->login($newUser);
      
                // return redirect()->intended('dashboard');
                return response(['error'=>0,'token'=>$token,'data'=>$findcustomer,'message'=>'success'], 200);
            }
      
        } catch (Exception $e) {
            dd($e->getMessage());
        }
    }
}
