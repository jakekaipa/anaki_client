@isset($transactions)
    @forelse ($transactions as $item)
        {{-- @dd($item) --}}
        <div class="dashboard-list-item-wrapper">
            <div class="dashboard-list-item sent d-flex justify-content-between">
                <div class="dashboard-list-left">
                    <div class="dashboard-list-user-wrapper">
                        <div class="dashboard-list-user-icon">
                            <i class="las la-arrow-up"></i>
                        </div>
                        <div class="dashboard-list-user-content">
                            @if ($item->type == payment_gateway_const()::TYPEADDMONEY)
                                <h4 class="title">{{ __('Add Balance via') }} <span class="text--warning">{{ $item->currency->name }}</span></h4>
                            @elseif ($item->type == payment_gateway_const()::TYPEMONEYOUT)
                                <h4 class="title">{{ __("Withdraw Money") }} <span class="text--warning">{{ $item->currency->gateway->name }}</span></h4>
                            @endif

                            <span class="{{ $item->StringStatus->class }}">{{ $item->StringStatus->value }}</span>
                        </div>
                    </div>
                </div>
                <div class="dashboard-list-right">
                    @if ($item->type == payment_gateway_const()::TYPEADDMONEY)
                        <h4 class="main-money text--base">{{ get_amount($item->request_amount, get_default_currency_code()) }}</h4>
                        <h6 class="exchange-money">{{ get_amount($item->payable, $item->currency->currency_code) }}</h6>
                    @elseif($item->type == payment_gateway_const()::TYPEMONEYOUT)
                        <h6 class="exchange-money text--warning fw-bold">{{ get_amount($item->request_amount,get_default_currency_code()) }}</h6>
                        <h4 class="main-money ">{{ get_amount($item->payable,$item->currency->currency_code) }}</h4>
                    @endif
                </div>
            </div>
            <div class="preview-list-wrapper">
                <div class="preview-list-item">
                    <div class="preview-list-left">
                        <div class="preview-list-user-wrapper">
                            <div class="preview-list-user-icon">
                                <i class="las la-clock"></i>
                            </div>
                            <div class="preview-list-user-content">
                                <span>{{ __("Time & Date") }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="preview-list-right">
                        <span>{{ dateFormat('d M Y, h:i:s A',$item->created_at) }}</span>
                    </div>
                </div>
                <div class="preview-list-item">
                    <div class="preview-list-left">
                        <div class="preview-list-user-wrapper">
                            <div class="preview-list-user-icon">
                                <i class="lab la-tumblr"></i>
                            </div>
                            <div class="preview-list-user-content">
                                <span>{{ __("Transaction ID") }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="preview-list-right">
                        <span>{{ $item->trx_id }}</span>
                    </div>
                </div>
                <div class="preview-list-item">
                    <div class="preview-list-left">
                        <div class="preview-list-user-wrapper">
                            <div class="preview-list-user-icon">
                                <i class="las la-exchange-alt"></i>
                            </div>
                            <div class="preview-list-user-content">
                                <span>{{ __('Exchange Rate') }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="preview-list-right">
                        @if ($item->type == payment_gateway_const()::TYPEADDMONEY)
                            <span>1 {{ get_default_currency_code() }} = {{ get_amount($item->currency->rate, $item->currency->currency_code) }}</span>
                        @elseif ($item->type == payment_gateway_const()::TYPEMONEYOUT)
                            <span>1 {{ get_default_currency_code() }} = {{ get_amount($item->currency->rate,$item->currency->currency_code) }}</span>
                        @endif
                    </div>
                </div>
                <div class="preview-list-item">
                    <div class="preview-list-left">
                        <div class=" preview-list-user-wrapper">
                            <div class="preview-list-user-icon">
                                <i class="las la-battery-half"></i>
                            </div>
                            <div class="preview-list-user-content">
                                <span>{{ __('Fees & Charge') }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="preview-list-right">
                        <span class="text--danger">{{ get_amount($item->charge->total_charge ?? 0, $item->user_wallet->currency->code) }}</span>
                    </div>
                </div>
                <div class="preview-list-item">
                    <div class="preview-list-left">
                        <div class="preview-list-user-wrapper">
                            <div class="preview-list-user-icon">
                                <i class="lab la-get-pocket"></i>
                            </div>
                            <div class="preview-list-user-content">
                                @if ($item->type == payment_gateway_const()::TYPEADDMONEY)
                                    <span>{{ __('Total Amount') }}</span>
                                    @elseif ($item->type == payment_gateway_const()::TYPEMONEYOUT)
                                        <span>{{ __("Conversion Amount") }}</span>
                                @endif

                            </div>
                        </div>
                    </div>
                    <div class="preview-list-right">
                        @if ($item->type == payment_gateway_const()::TYPEADDMONEY)
                            <span class="text--danger">{{ get_amount($item->available_balance, get_default_currency_code()) }}</span>
                         @elseif ($item->type == payment_gateway_const()::TYPEMONEYOUT)
                            @php
                                $conversionAmount = $item->request_amount * $item->currency->rate;
                            @endphp
                               <span>{{ get_amount($conversionAmount,$item->currency->currency_code) }}</span>
                        @endif
                    </div>
                </div>
                <div class="preview-list-item">
                    <div class="preview-list-left">
                        <div class="preview-list-user-wrapper">
                            <div class="preview-list-user-icon">
                                <i class="las la-smoking"></i>
                            </div>
                            <div class="preview-list-user-content">
                                <span>{{ __('Status') }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="preview-list-right">
                        <span class="{{ $item->StringStatus->class }}">{{ $item->StringStatus->value }}</span>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="alert alert-primary text-center">
            {{ __("No Record Found!") }}
        </div>
    @endforelse
@endisset
