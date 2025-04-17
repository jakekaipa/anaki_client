@if ($basic_settings->kyc_verification == true && isset($user_kyc) && $user_kyc != null && $user_kyc->fields != null)
<body  onload="initKYC()">
    <h3 class="title">{{ __("KYC Information") }} &nbsp; <span class="{{ auth()->user()->kycStringStatus->class }}">{{ auth()->user()->kycStringStatus->value }}</span></h3>

    @if (auth()->user()->kyc_verified == global_const()::APPROVED)
        <div class="approved text--success kyc-text">{{ __("Your KYC information is verified") }}</div>
    @else
    <p>{{ __("Please submit your KYC information with valid data.") }}</p>
    <form action="{{ setRoute('user.authorize.kyc.submit') }}" class="account-form" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row ml-b-20">

            <!-- <div class="col-lg-12 form-group">
                <label>{{ __("Name")}}</label>
                <input type="text" placeholder="{{ __('Enter name') }}" id="nameInput" class="form--control" value="한경두" required >
            </div>

            <div class="col-lg-12 form-group">
                <label>{{ __("Phone Number")}}</label>
                <input type="number" placeholder="{{ __('Enter only number') }}" id="phoneInput" class="form--control" value="01030076398" required >
            </div>

            <div class="col-lg-12 form-group">
                <label>{{ __("Birth Date")}}</label>
                <input type="text" placeholder="{{ __('YYYY-MM-DD') }}" id="birthDateInput" class="form--control" value="1979-06-11" required >
            </div>

            <div class="col-lg-12 form-group">
                <label>{{ __("Email")}}</label>
                <input type="email" placeholder="{{ __('example@email.com') }}" id="emailInput" class="form--control" value="mrttoo0@gmail.com" required >
            </div>

            <div class="col-lg-12 form-group">
                <div class="forgot-item">
                    <label>{{ __("Back to") }}<a href="{{ setRoute('user.dashboard') }}" class="text--base">{{ __("Dashboard") }}</a></label>
                </div>
            </div> -->

            <div class="col-lg-12 form-group text-center">
                <button type="button" onclick="onSubmit()" class="btn--base w-100 btn-loading">{{ __("submit") }}</button>
            </div>
        </div>
    </form>   
    @endif
</body>
@endif

@push('script')

@endpush