<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Network;
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
        // dd($network);
        return view('home',compact('network'));
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
            'account_number' => ['required','max:9'],
        ]);
        // check if account number exists
        $checkAccountNumber = User::where('account_number',$request->account_number)->first();

        if (!isset($checkAccountNumber)) {
            return back()->with('error_account', 'Account Number Does not Exist');
        }
        // check if user has sufficient funds to send including charges
        $amount = $request->amount + 20;
        if (auth()->user()->balance < ($amount) ) {
            return back()->with('error_balance', 'Insufficient Fund');
        }
        // Send money to beneficiary account
        $sendMoney = User::where('account_number',$request->account_number)->increment('balance',$request->amount);
        HomeController::sendTransactionMail($amount);
        // Deduct money from sender account
        $deductMoney = User::where('id', auth()->user()->id)->decrement('balance',$amount);
        // send mail to reciever of funds
        HomeController::sendTransactionMail($amount,$type='reciever',$checkAccountNumber->name,$checkAccountNumber->email);
        return back()->with('success', '₦'.$amount.' Sent  Successfully');
    }

    public function sendTransactionMail($amount,$type='sender',$name='',$email='',)
    {
        
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
}
