<?php

namespace Modules\BusinessManagement\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Modules\Gateways\Entities\Setting;

class PaymentConfigSetupStoreOrUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        if($this['gateway'] == 'paymob_accept' && !$this['supported_country'] && !$this['secret_key']) {
            Setting::updateOrCreate(['key_name' => 'paymob_accept', 'settings_type' => 'payment_config'], [
                'key_name' => 'paymob_accept',
                'live_values' => [
                    'gateway' => "",
                    'mode' => "live",
                    'status' => "0",
                    'supported_country' => "",
                    'public_key' => "",
                    'secret_key' => "",
                    'integration_id' => "",
                    'hmac' => "",
                ],
                'test_values' => [
                    'gateway' => "",
                    'mode' => "test",
                    'status' => "0",
                    'supported_country' => "",
                    'public_key' => "",
                    'secret_key' => "",
                    'integration_id' => "",
                    'hmac' => "",
                ],
                'settings_type' => 'payment_config',
                'mode' => 'test',
                'is_active' => 0 ,
                'additional_data' => null,
            ]);
        }

        return [
            'gateway' => 'required|in:ssl_commerz,sixcash,worldpay,payfast,swish,esewa,maxicash,hubtel,viva_wallet,tap,thawani,moncash,pvit,ccavenue,foloosi,iyzi_pay,xendit,fatoorah,hyper_pay,amazon_pay,paypal,stripe,razor_pay,senang_pay,paytabs,paystack,paymob_accept,paytm,flutterwave,liqpay,bkash,mercadopago,cash_after_service,digital_payment,momo',
            'mode' => 'required|in:live,test',
            'gateway_image' => 'nullable|mimes:png',
            'gateway_title' => Rule::requiredIf(function () {
                return $this->input('status') == 1;
            }),
            'status' => [
                'required_if:gateway,ssl_commerz,sixcash,worldpay,payfast,swish,esewa,maxicash,hubtel,viva_wallet,tap,thawani,moncash,pvit,ccavenue,foloosi,iyzi_pay,xendit,fatoorah,hyper_pay,amazon_pay,paypal,stripe,razor_pay,senang_pay,paytabs,paystack,paymob_accept,paytm,flutterwave,liqpay,bkash,mercadopago,cash_after_service,digital_payment,momo',
                Rule::in([1, 0])
            ],
            #SSl_Commerz
            'store_id' => [
                Rule::requiredIf(function () {
                    return ($this->input('status') == 1 && $this->input('gateway') == 'ssl_commerz');
                })
            ],
            'store_password' => [
                Rule::requiredIf(function () {
                    return ($this->input('status') == 1 && $this->input('gateway') == 'ssl_commerz');
                })
            ],
            #Paypal
            'client_id' => [
                Rule::requiredIf(function () {
                    return ($this->input('status') == 1 && $this->input('gateway') == 'paypal');
                })
            ],
            'client_secret' => [
                Rule::requiredIf(function () {
                    return ($this->input('status') == 1 && $this->input('gateway') == 'paypal');
                })
            ],
            #stripe,razor_pay
            'api_key' => [
                Rule::requiredIf(function () {
                    return ($this->input('status') == 1 &&
                        ($this->input('gateway') == 'stripe' || $this->input('gateway') == 'razor_pay'));
                })
            ],
            'published_key' => [
                Rule::requiredIf(function () {
                    return ($this->input('status') == 1 && $this->input('gateway') == 'stripe');
                })
            ],
            'api_secret' => [
                Rule::requiredIf(function () {
                    return ($this->input('status') == 1 && $this->input('gateway') == 'razor_pay');
                })
            ],
            #senang_pay
            'callback_url' => [
                Rule::requiredIf(function () {
                    return ($this->input('status') == 1 &&
                        ($this->input('gateway') == 'senang_pay'));
                })
            ],
            'secret_key' => [
                Rule::requiredIf(function () {
                    return ($this->input('status') == 1 &&
                        ($this->input('gateway') == 'senang_pay' || $this->input('gateway') == 'flutterwave'
                            || $this->input('gateway') == 'paystack' || $this->input('gateway') == 'paymob_accept'));
                })
            ],
            'merchant_id' => [
                Rule::requiredIf(function () {
                    return ($this->input('status') == 1 &&
                        ($this->input('gateway') == 'senang_pay' || $this->input('gateway') == 'paytm'));
                })
            ],
            #paytabs
            'profile_id' => [
                Rule::requiredIf(function () {
                    return ($this->input('status') == 1 && $this->input('gateway') == 'paytabs');
                })
            ],
            'server_key' => [
                Rule::requiredIf(function () {
                    return ($this->input('status') == 1 && $this->input('gateway') == 'paytabs');
                })
            ],
            'base_url' => [
                Rule::requiredIf(function () {
                    return ($this->input('status') == 1 && $this->input('gateway') == 'paytabs');
                })
            ],
            #paystack
            'public_key' => [
                Rule::requiredIf(function () {
                    return ($this->input('status') == 1 &&
                        ($this->input('gateway') == 'paystack' || $this->input('gateway') == 'mercadopago'
                            || $this->input('gateway') == 'liqpay' || $this->input('gateway') == 'flutterwave' || $this->input('gateway') == 'paymob_accept'));
                })
            ],
            'merchant_email' => [
                Rule::requiredIf(function () {
                    return ($this->input('status') == 1 && $this->input('gateway') == 'paystack');
                })
            ],
            #paymob_accept
            'supported_country' => [
                Rule::requiredIf(function () {
                    return ($this->input('status') == 1 && $this->input('gateway') == 'paymob_accept');
                })
            ],
            'integration_id' => [
                Rule::requiredIf(function () {
                    return ($this->input('status') == 1 && $this->input('gateway') == 'paymob_accept');
                })
            ],
            'hmac' => [
                Rule::requiredIf(function () {
                    return ($this->input('status') == 1 && $this->input('gateway') == 'paymob_accept');
                })
            ],
            #mercadopago
            'access_token' => [
                Rule::requiredIf(function () {
                    return ($this->input('status') == 1 && $this->input('gateway') == 'mercadopago');
                })
            ],
            #liqpay
            'private_key' => [
                Rule::requiredIf(function () {
                    return ($this->input('status') == 1 && $this->input('gateway') == 'liqpay');
                })
            ],
            #flutterwave
            'hash' => [
                Rule::requiredIf(function () {
                    return ($this->input('status') == 1 && $this->input('gateway') == 'flutterwave');
                })
            ],
            #paytm
            'merchant_key' => [
                Rule::requiredIf(function () {
                    return ($this->input('status') == 1 && $this->input('gateway') == 'paytm');
                })
            ],
            'merchant_website_link' => [
                Rule::requiredIf(function () {
                    return ($this->input('status') == 1 && $this->input('gateway') == 'paytm');
                })
            ],
            #bkash
            'app_key' => [
                Rule::requiredIf(function () {
                    return ($this->input('status') == 1 && $this->input('gateway') == 'bkash');
                })
            ],
            'app_secret' => [
                Rule::requiredIf(function () {
                    return ($this->input('status') == 1 && $this->input('gateway') == 'bkash');
                })
            ],
            'username' => [
                Rule::requiredIf(function () {
                    return ($this->input('status') == 1 && $this->input('gateway') == 'bkash');
                })
            ],
            'password' => [
                Rule::requiredIf(function () {
                    return ($this->input('status') == 1 && $this->input('gateway') == 'bkash');
                })
            ]
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::check();
    }
}
