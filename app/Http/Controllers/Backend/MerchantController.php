<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Services\SmsService;
use Illuminate\Http\Request;
use App\Http\Requests\Merchant\StoreRequest;
use App\Http\Requests\Merchant\SignUpRequest;
use App\Http\Requests\Merchant\UpdateRequest;
use App\Http\Requests\Merchant\OtpRequest;
use App\Mail\MerchantSignup;
use App\Repositories\Invoice\InvoiceInterface;
use App\Repositories\Merchant\MerchantInterface;
use Illuminate\Support\Facades\Mail;
use Brian2694\Toastr\Facades\Toastr;
class MerchantController extends Controller
{
    protected $repo,$invoiceRepo;
    public function __construct(MerchantInterface $repo,InvoiceInterface $invoiceRepo)
    {
        $this->repo        = $repo;
        $this->invoiceRepo = $invoiceRepo;
    }

    public function index()
    {
        $merchants = $this->repo->all();
        return view('backend.merchant.index',compact('merchants'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $hubs = $this->repo->all_hubs();

        return view('backend.merchant.create', compact('hubs'));
    }

    public function signUp(Request $request)
    {

        $hubs       = $this->repo->all_hubs();
        return view('backend.merchant.sign_up',compact('hubs','request'));
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request)
    {

        if($this->repo->store($request)){

            Toastr::success(__('merchant.added_msg'),__('message.success'));
            return redirect()->route('merchant.index');
        }else{
            Toastr::error(__('merchant.error_msg'),__('message.error'));
            return redirect()->back()->withInput($request->all());
        }

    }


    public function signUpStore(SignUpRequest $request)
    {
        if($this->repo->signUpStore($request)){
            return redirect()->route('merchant.otp-verification-form');
        }else{
            Toastr::error(__('merchant.error_msg'),__('message.error'));
            return redirect()->back()->withInput($request->all());
        }
    }


    public function otpVerification(OtpRequest $request)
    {
        $result     = $this->repo->otpVerification($request);
        if($result != null){
            if(auth()->attempt([
                                'mobile' => $result->mobile,
                                'password' => session('password')
                            ]))
            {
                return redirect()->route('login');
            }
        }
        elseif($result == 0){
            return redirect()->route('merchant.otp-verification-form')->with('warning', 'Invalid OTP');
        }
        else{
            Toastr::error(__('merchant.error_msg'),__('message.error'));
            return redirect()->back();
        }
    }

    public function otpVerificationForm()
    {
        return view('backend.merchant.verification');
    }

    public function resendOTP(Request $request)
    {
        $this->repo->resendOTP($request);
        return redirect()->route('merchant.otp-verification-form')->with('success', 'Resend OTP');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function view($id)
    {
        $singleMerchant = $this->repo->get($id);
        $merchant_shops =$this->repo->merchant_shops_get($id);
        if(blank($singleMerchant)){
            abort(404);
        }
        return view('backend.merchant.merchant-details',compact('singleMerchant','merchant_shops'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $hubs     = $this->repo->all_hubs();
        $merchant = $this->repo->get($id);
        if(blank($merchant)){
            abort(404);
        }
        return view('backend.merchant.edit',compact('merchant','hubs'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id, UpdateRequest $request)
    {

        if($this->repo->update($id,$request)){
            Toastr::success(__('merchant.update_msg'),__('message.success'));
            return redirect()->route('merchant.index');
        }else{
            Toastr::error(__('merchant.error_msg'),__('message.error'));
            return redirect()->back();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if($this->repo->delete($id)){
            Toastr::success(__('merchant.delete_msg'),__('message.success'));
            return back();
        }else{
            Toastr::error(__('merchant.error_msg'),__('message.error'));
            return redirect()->back();
        }
    }

    public function invoiceGenerate($id){
        try {
            $result = $this->invoiceRepo->store($id);
            if($result) {
                // Get the latest generated invoice
                $latestInvoice = \App\Models\Backend\Merchantpanel\Invoice::where('merchant_id', $id)->latest()->first();
                if($latestInvoice) {
                    $invoiceUrl = route('merchant.invoice.index', $id);
                    Toastr::success('Invoice generated successfully! Invoice ID: ' . $latestInvoice->invoice_id . ' | <a href="' . $invoiceUrl . '" class="text-white"><u>View Invoices</u></a>','Success');
                } else {
                    // Check if there are eligible parcels
                    $delivered = \App\Models\Backend\Parcel::where('merchant_id', $id)
                        ->where(function($query) {
                            $query->whereIn('status', [\App\Enums\ParcelStatus::DELIVERED]);
                            $query->orWhere('partial_delivered', \App\Enums\BooleanStatus::YES);
                        })
                        ->where('invoice_id', null)
                        ->count();
                    
                    $returns = \App\Models\Backend\Parcel::where('merchant_id', $id)
                        ->where(function($query) {
                            $query->whereIn('status', [\App\Enums\ParcelStatus::RETURN_RECEIVED_BY_MERCHANT, \App\Enums\ParcelStatus::RETURN_ASSIGN_TO_MERCHANT, \App\Enums\ParcelStatus::RETURN_TO_COURIER]);
                            $query->orWhere('return_to_courier', 1);
                        })
                        ->where('partial_delivered', \App\Enums\BooleanStatus::NO)
                        ->where('invoice_id', null)
                        ->count();
                    
                    if($delivered == 0 && $returns == 0) {
                        Toastr::info('No eligible parcels found for invoice generation. This merchant has no delivered or returned parcels to invoice.','Info');
                    } else {
                        Toastr::success('Invoice generated successfully','Success');
                    }
                }
            } else {
                // Check if there are eligible parcels
                $merchant = $this->repo->get($id);
                if($merchant) {
                    $delivered = \App\Models\Backend\Parcel::where('merchant_id', $id)
                        ->where(function($query) {
                            $query->whereIn('status', [\App\Enums\ParcelStatus::DELIVERED]);
                            $query->orWhere('partial_delivered', \App\Enums\BooleanStatus::YES);
                        })
                        ->where('invoice_id', null)
                        ->count();
                    
                    $returns = \App\Models\Backend\Parcel::where('merchant_id', $id)
                        ->where(function($query) {
                            $query->whereIn('status', [\App\Enums\ParcelStatus::RETURN_RECEIVED_BY_MERCHANT, \App\Enums\ParcelStatus::RETURN_ASSIGN_TO_MERCHANT, \App\Enums\ParcelStatus::RETURN_TO_COURIER]);
                            $query->orWhere('return_to_courier', 1);
                        })
                        ->where('partial_delivered', \App\Enums\BooleanStatus::NO)
                        ->where('invoice_id', null)
                        ->count();
                    
                    if($delivered == 0 && $returns == 0) {
                        Toastr::info('No eligible parcels found for invoice generation. All parcels may already be invoiced or no delivered/returned parcels available.','Info');
                    } else {
                        // Check if invoice already generated today
                        $todayInvoice = \App\Models\Backend\Merchantpanel\Invoice::where('merchant_id', $id)
                            ->whereBetween('created_at', [\Carbon\Carbon::today()->startOfDay(), \Carbon\Carbon::today()->endOfDay()])
                            ->count();
                        
                        if($todayInvoice > 0) {
                            Toastr::info('Invoice already generated for this merchant today.','Info');
                        } else {
                            // Check payment period
                            $merchant_date = strtotime(\Carbon\Carbon::today()->subDays($merchant->payment_period)->format('d-m-Y'));
                            $lastInvoice = \App\Models\Backend\Merchantpanel\Invoice::where('merchant_id', $id)->latest()->first();
                            if($lastInvoice) {
                                $lastInvoiceDate = strtotime(\Carbon\Carbon::parse($lastInvoice->created_at)->format('d-m-Y'));
                                if($lastInvoiceDate > $merchant_date) {
                                    Toastr::info('Payment period not yet elapsed. Next invoice can be generated after ' . \Carbon\Carbon::parse($lastInvoice->created_at)->addDays($merchant->payment_period)->format('Y-m-d'), 'Info');
                                } else {
                                    Toastr::error('Invoice generation failed. Please check logs for details.','Error');
                                }
                            } else {
                                Toastr::error('Invoice generation failed. Please check logs for details.','Error');
                            }
                        }
                    }
                } else {
                    Toastr::error('Merchant not found.','Error');
                }
            }
        } catch (\Exception $e) {
            \Log::error('Invoice generation error in controller: ' . $e->getMessage());
            Toastr::error('An error occurred while generating invoice. Please try again.','Error');
        }
        return redirect()->back();
    }
}
