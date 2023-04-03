<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\Network;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;
     public function showRegistrationForm(Request $request)
    {
        if ($request->has('ref')) {
            session(['referrer' => $request->query('ref')]);
           
        }

        return view('auth.register');
    }

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        // we check if user is accessing webpage with a referral link
        // dd($data['referral_id']);

        $user_referral_id = RegisterController::generateTransactionId();
        $account_number = RegisterController::generateAccountNumber();

          if (isset($data['referral_id'])) {
            // Get the owner of the referral code
            $userData = User::where('referral_id', $data['referral_id'])->get();
           
            
            if (count($userData) > 0) {
                $user_id = User::create([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'password' => Hash::make($data['password']),
                    'referral_id' => $user_referral_id,
                    'account_number' => $account_number
                ]);
                
                
                // We update the Network Model
                 Network::create([
                    'referral_id' => $data['referral_id'],
                    'user_id' => $userData[0]->id,
                    // Owner of the Referral Link is the user_id
                    'reffered_id' => $user_id->id,
                    'amount_earned' => 0,

                ]);
                 
                 $result = User::where('id', $userData[0]->id)->increment('balance', 0);

                return $user_id;
            }
        }
        else{
            
         
            $createUser =  User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'referral_id' => $user_referral_id,
                'account_number' => $account_number
            ]);
            return $createUser;
        }
    }

     public static function generateTransactionId(int $length = 10)
    {
        $trans_id = Str::random(10); //Generates random id
        $exist = User::where('referral_id', '=', $trans_id)->get(['referral_id']);
        if (isset($exist[0]->referral_id)) {
            return self::generateTransactionId();
        }
        return $trans_id;

    }
    public static function generateAccountNumber(int $length = 10)
    {
        $account_number = rand(100000000, 999999999); //Generates account number
        $exist = User::where('account_number', '=', $account_number)->get(['account_number']);
        if (isset($exist[0]->referral_id)) {
            return self::generateAccountNumber();
        }
        return $account_number;
    }
}
