@extends('user.layouts.master')

@section('content')
    <div class="dashboard-area mt-10">
        <div class="dashboard-header-wrapper">
            <h3 class="title">{{ __('Edit Sell Offer') }}</h3>
        </div>
    </div>
    <div class="row justify-content-center mb-30-none">
        <div class="col-lg-8 mb-30">
            <div class="dash-payment-item-wrapper">
                <div class="dash-payment-item active">
                    <div class="dash-payment-title-area">
                        <span class="dash-payment-badge">!</span>
                        <h5 class="title">{{ __('Edit Offer Details') }}</h5>
                    </div>

                    <form class="card-form mt-20" action="{{ setRoute('user.sell-list.update', $item->id) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label>{{ __('Price Type') }}</label>
                                <select name="priceType" class="form--control" id="priceType">
                                    <option value="0" {{ $item->priceType == 0 ? 'selected' : '' }}>
                                        {{ __('Market Price') }}</option>
                                    <option value="1" {{ $item->priceType == 1 ? 'selected' : '' }}>
                                        {{ __('Fixed Price') }}</option>
                                </select>
                            </div>
                            <div class="col-md-6 form-group" id="MarketPriceGroup">
                                <label>{{ __('Offer Margin (%)') }}</label>
                                <input type="number" step="0.01" class="form--control" name="offerMargin"
                                    value="{{ $item->offerMargin }}">
                            </div>
                            <div class="col-md-6 form-group" id="FixedPriceGroup" style="display: none;">
                                <label>{{ __('Fixed Price (KRW)') }}</label>
                                <input type="number" step="0.01" class="form--control" name="fixedPrice"
                                    value="{{ $item->priceType == 0 ? ($prices['KRW'] * (100 + $item->offerMargin)) / 100 : $item->fixedPrice }}">
                            </div>
                            <div class="col-md-6 form-group">
                                <label>{{ __('Minimum Trade Volume (KRW)') }}</label>
                                <input type="number" class="form--control" name="tradeVolMin"
                                    value="{{ $item->tradeVolMin }}">
                            </div>
                            <div class="col-md-6 form-group">
                                <label>{{ __('Maximum Trade Volume (KRW)') }}</label>
                                <input type="number" class="form--control" name="tradeVolMax"
                                    value="{{ $item->tradeVolMax }}">
                            </div>
                            <div class="col-md-6 form-group">
                                <label>{{ __('Offer Time Limit (minutes)') }}</label>
                                <input type="number" class="form--control" name="offerTimeLimit"
                                    value="{{ $item->offerTimeLimit }}">
                            </div>
                            <div class="col-md-6 form-group">
                                <label>{{ __('Offer Tag') }}</label>
                                <select name="offerTag" class="form--control">
                                    <option value="needID" {{ $item->offerTag == 'needID' ? 'selected' : '' }}>
                                        {{ __('Need ID Card Picture') }}</option>
                                    <option value="specApproval" {{ $item->offerTag == 'specApproval' ? 'selected' : '' }}>
                                        {{ __('Specification Approval') }}</option>
                                    <option value="noNeedReceipt"
                                        {{ $item->offerTag == 'noNeedReceipt' ? 'selected' : '' }}>
                                        {{ __('No Receipt Required') }}</option>
                                    <option value="onlySameBank" {{ $item->offerTag == 'onlySameBank' ? 'selected' : '' }}>
                                        {{ __('Same Bank Only') }}</option>
                                    <option value="receiptRequired"
                                        {{ $item->offerTag == 'receiptRequired' ? 'selected' : '' }}>
                                        {{ __('Receipt Required') }}</option>
                                    <option value="noThirdParty" {{ $item->offerTag == 'noThirdParty' ? 'selected' : '' }}>
                                        {{ __('No Third Party') }}</option>
                                    <option value="noAuthRequired"
                                        {{ $item->offerTag == 'noAuthRequired' ? 'selected' : '' }}>
                                        {{ __('No Authentication Required') }}</option>
                                </select>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>{{ __('Offer Label') }}</label>
                                <input type="text" class="form--control" name="offerLabel"
                                    value="{{ $item->offerLabel }}">
                            </div>
                            <div class="col-md-12 form-group">
                                <label>{{ __('Offer Condition') }}</label>
                                <textarea class="form--control" name="offerCondition" rows="3">{{ $item->offerCondition }}</textarea>
                            </div>
                            <div class="col-md-12 form-group">
                                <label>{{ __('Transaction Guidelines') }}</label>
                                <textarea class="form--control" name="transGuide" rows="3">{{ $item->transGuide }}</textarea>
                            </div>
                            <div class="col-md-12 form-group">
                                <label>{{ __('Authentication') }}</label>
                                <div>
                                    <input type="checkbox" name="checkboxMobileAuth" value="1"
                                        style="width:20px;height:20px;border:1px;"
                                        {{ $item->needMobileAuth ? 'checked' : '' }}>
                                    {{ __('Can Trade Mobile Verified') }}
                                </div>
                                <div>
                                    <input type="checkbox" name="checkboxKYCAuth" value="1"
                                        style="width:20px;height:20px;border:1px;"
                                        {{ $item->needKYCAuth ? 'checked' : '' }}> {{ __('Can Trade KYC Verified') }}
                                </div>
                                <div>
                                    <input type="checkbox" name="checkboxAccountAuth" value="1"
                                        style="width:20px;height:20px;border:1px;"
                                        {{ $item->needAccountAuth ? 'checked' : '' }}>
                                    {{ __('Can Trade Account Verified') }}
                                </div>
                            </div>
                            <div class="col-md-12 form-group">
                                <div>
                                    <input type="checkbox" name="secretTrade" id="secretTrade" value="1"
                                        style="width:20px;height:20px;border:1px;" {{ $item->password ? 'checked' : '' }}>
                                    {{ __('Secret Trade') }}
                                </div>
                            </div>
                            <div class="col-md-6 form-group" id="passwordGroup"
                                style="{{ $item->password ? '' : 'display: none;' }}">
                                <label>{{ __('Password') }}</label>
                                <input type="password" class="form--control" name="password" value="{{ $item->password }}">
                            </div>
                            <div class="col-xl-12 col-lg-12 form-group">
                                <button type="submit" class="btn--base w-100">{{ __('Update Offer') }}<i
                                        class="fas fa-arrow-right ms-1"></i></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        $(document).ready(function() {
            // Price Type toggle
            $('#priceType').change(function() {
                if ($(this).val() == "0") {
                    $('#MarketPriceGroup').show();
                    $('#FixedPriceGroup').hide();
                } else {
                    $('#MarketPriceGroup').hide();
                    $('#FixedPriceGroup').show();
                }
            });

            // Secret Trade toggle
            $('#secretTrade').change(function() {
                if ($(this).is(':checked')) {
                    $('#passwordGroup').show();
                } else {
                    $('#passwordGroup').hide();
                }
            });

            // Trigger change event on page load
            $('#priceType').trigger('change');
        });
    </script>
@endpush
