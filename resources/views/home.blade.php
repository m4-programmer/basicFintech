@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header" style="font-weight: bold">{{auth()->user()->name}} {{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                     @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif 
                    @if (session('error'))
                        <div class="alert alert-danger" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif  

                  

                    <h4>Account Number: {{auth()->user()->account_number}}</h4>
                    <h4>Acccount Balance: &#8358;{{auth()->user()->balance}}</h4>
                    <p>Referral Link: <a href="{{url('/register?ref='.auth()->user()->referral_id)}}">{{url('/register?ref='.auth()->user()->referral_id)}}</a></p>

                    <hr>

                    <form action="{{url('topUp')}}" method="post" class="m-auto w-50">
                        @csrf
                        <h5>Top Up Account</h5>
                        <div class="form-input">
                            <label>Amount</label>
                            <input type="number" name="topUp" class="form-control @error('topUp') is-invalid @enderror">
                        </div>
                         @error('topUp')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                        <div class="text-center mt-3">
                            <button class="btn btn-primary">Top Up</button>
                        </div>

                    </form>
                    <hr>

                    
                    <form action="{{route('sendMoney')}}" method="post" class="m-auto w-50">
                        @csrf
                        <h5>Send Money</h5>

                          @if (session('error_balance'))
                        <div class="alert alert-danger" role="alert">
                            {{ session('error_balance') }}
                        </div>
                         @endif
                        <div class="form-input py-1">
                            <label>Account Number</label>
                            <input type="text" name="account_number" class="form-control @error('account_number') is-invalid @enderror" value="{{ old('account_number') }}">
                        </div>
                         @if (session('error_account'))
                        <div class="alert alert-danger py-1" role="alert">
                            {{ session('error_account') }}
                        </div>
                         @endif
                         @error('account_number')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                        <div class="form-input">
                            <label>Amount</label>
                            <input type="number" name="amount" id="amount" class="form-control @error('amount') is-invalid @enderror">
                        </div>
                        <div>
                            <p class="text-right" style="text-align: right!important;"><small>Charge: &#8358; 20.00</small></p>
                        </div>
                         @error('amount')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror

                        <div class="text-center mt-3">
                            <button class="btn btn-primary">Send</button>
                        </div>

                    </form>

                    <hr>
                    <p>My referrals</p>
                    <ol>
                      
                    @forelse($network as $data)
                    
                        <li>{{$data->reffered->name}}</li>
                    
                    @empty
                        <p>No Refferal yet</p>
                    @endforelse
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    
</script>
@endsection
