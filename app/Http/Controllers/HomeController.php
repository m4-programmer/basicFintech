<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Network;
use App\Models\Beneficiary;
use Mail;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $network = Network::where('user_id', auth()->user()->id)->get();
        $beneficiary = Beneficiary::where('user_id',auth()->user()->id)->get();
        // dd($network);
        return view('home',compact('network','beneficiary'));
    }
    public function topUp(Request $request)
    {
        $request->validate([
            'topUp' => 'numeric|required',
        ]);

        $result = User::where('id', auth()->user()->id)->increment('balance',$request->topUp);

        return back()->with('success', 'Account Topped up successfully');
    }
    public function sendMoney(Request $request)
    {
        $request->validate([
            'amount' => ['required','numeric'],
            'account_number' => ['nullable','max:9'],
        ]);
        // beneficiary account number
        $BeneficiaryAccount = $request->beneficiary;
        // dd($BeneficiaryAccount);
        if ($BeneficiaryAccount == 0) {
            $checkAccountNumber = User::where('account_number',$request->account_number)->first();
        }else{
            $checkAccountNumber = User::where('account_number',$BeneficiaryAccount)->first();;
        }

        // check if account number exists
        

        if (!isset($checkAccountNumber)) {
            return back()->with('error_account', 'Account Number Does not Exist');
        }
        // check if user has sufficient funds to send including charges
        $amount = $request->amount ;
        if (auth()->user()->balance < ($amount) ) {
            return back()->with('error_balance', 'Insufficient Fund');
        }
        // Send money to beneficiary account
        $sendMoney = User::where('account_number',$request->account_number)->increment('balance',$request->amount);
        HomeController::sendTransactionMail($amount,'sender','','',auth()->user()->is_subscribed);
        // Deduct money from sender account
        $deductMoney = User::where('id', auth()->user()->id)->decrement('balance',$amount);
        // send mail to reciever of funds
        HomeController::sendTransactionMail($amount,$type='reciever',$checkAccountNumber->name,$checkAccountNumber->email,$checkAccountNumber->is_subscribed);
        $BeneficiaryId = User::where('account_number', $request->account_number)->get('id')->first();

        if (isset($request->save)) {
            // check if beneficiary already exist
            $checkUser = Beneficiary::where('beneficiary_id',$BeneficiaryId->id)->get()->count();
            if ($checkUser == 0) {
                 Beneficiary::create([
                'user_id' => auth()->user()->id,
                'beneficiary_id' => $BeneficiaryId->id,
                ]);
            }
           
        }
        return back()->with('success', '₦'.$amount.' Sent  Successfully');
    }
    public function subscribe(Request $request)
    {
        // check the request and charge user a one time fee of 5 naira for mail service
        // if user balance is not sufficient, do not allow user to subscribe
        $subscribe = $request->join;
        $mailCharges = 5;
        
        if (isset($subscribe) and  auth()->user()->is_subscribed == 0) {
            // if user balance is sufficient, deduct user balance, and subscribe the user
            if (auth()->user()->balance >= $mailCharges) {
                $deductMoney = User::where('id', auth()->user()->id)->decrement('balance',$mailCharges);
                auth()->user()->is_subscribed = 1;
                auth()->user()->save();
                
                return back()->with('success', "congratulations, u have successfully subscribed to recieve mail notification, a one time fee of ₦$mailCharges has been deducted from your account. ");
            }
            return back()->with('error', "Insufficient funds to subscribe to mail service. ");

        }else{
            // if user is subscribed, then unscribe user
            if (auth()->user()->is_subscribed == 1) {
                auth()->user()->is_subscribed = '0';
                auth()->user()->save();   
            
                return back()->with('success', "congratulations, u have successfully unsubscribed from recieving mail notification");
            }
               return back()->with('error', "Please click the checkbox");
        }

        

        // if user is subscribe already, unscribe him, when he chooses to unscribe
    }

    public function sendTransactionMail($amount,$type='sender',$name='',$email='',$is_subscribed = 0)
    {
        
        
        if ($is_subscribed == 1) {
            if ($type=='sender') {
            // Send mail to sender informing him/her of the transaction that was initiated on the account
            $data['name'] = auth()->user()->name;
            $data['email'] = auth()->user()->email;
            $data['amount'] = $amount;
            $data['title'] = 'Debit Alert';
            $data['message'] = "Goodday {$data['name']}, ₦{$data['amount']} has been sent from your account successfully";

             try{
                    Mail::send('email.notification', ['data' => $data], function($message) use($data){
                    $message->to($data['email'])->subject($data['title']);
                 });
                }
                catch(Exception $e){

                     }
            

            return;
        }
        // else send mail to user telling him/her of the amount recieved.
         $data['name'] = $name;
         $data['email'] = $email;
         $data['amount'] = $amount;
         $data['title'] = 'Credit Alert';
         $data['message'] = "Goodday {$data['name']}, ₦{$data['amount']} has been credited to your account successfully";
         
         try{
                Mail::send('email.notification', ['data' => $data], function($message) use($data){
                $message->to($data['email'])->subject($data['title']);
             });
            }
            catch(Exception $e){

                 }
                
        return;
        }
        return false;
    }
    
}
