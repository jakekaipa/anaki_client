@php
    $default = get_default_language_code();

    $style = '';
    $statusMsg = '';

    if ($item->state == 'open' || $item->state == 'send') {
        $style = 'going';
        $statusMsg = __('Ongoing');
    } elseif ($item->state == 'dispute') {
        $style = 'going';
        $statusMsg = __('분쟁중');
    } elseif ($item->state == 'done') {
        $style = 'complete';
        $statusMsg = __('Complete');
    } elseif ($item->state == 'cancel') {
        $style = 'cancel';
        $statusMsg = __('Cancel');
    }
@endphp
@extends('user.layouts.master')

@section('content')
    <div class="dashboard-area mt-10">
        <div class="dashboard-header-wrapper">
            <h3 class="title">{{ $page_title }}</h3>
        </div>
    </div>
    <div class="row justify-content-center mb-30-none">
        <div class="col-lg-6 mb-30">
            <div class="dash-payment-item-wrapper">
                <div class="dash-payment-item active">
                    <div class="dash-payment-title-area">
                        <span class="dash-payment-badge">!</span>
                        <h4 class="title">{{ __('Transaction Details') }}</h4>
                    </div>

                    <p>
                        <a href="javascript:;" onclick="copyLink({{ $item->id }});">
                            <h5>링크복사</h5>
                        </a>
                    </p>

                    <div class="dash-payment-body">
                        <div class="preview-list-wrapper">

                            <div class="preview-list-item">
                                <div class="preview-list-left">
                                    <div class="preview-list-user-wrapper">
                                        <div class="preview-list-user-icon">
                                            <i class="las la-money-check-alt"></i>
                                        </div>
                                        <div class="preview-list-user-content">
                                            <span class="last">{{ __('Status') }}:</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="preview-list-right">
                                    <span
                                        class="text--warning last">{{ $statusMsg }}</span>
                                </div>
                            </div>

                            <div class="preview-list-item">
                                <div class="preview-list-left">
                                    <div class="preview-list-user-wrapper">
                                        <div class="preview-list-user-icon">
                                            <i class="las la-battery-quarter"></i>
                                        </div>
                                        <div class="preview-list-user-content">
                                            <span>{{ __('Seller Name') }}:</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="preview-list-right">
                                    <span>{{ $item->seller_user->username }}</span>
                                </div>
                            </div>

                            <div class="preview-list-item">
                                <div class="preview-list-left">
                                    <div class="preview-list-user-wrapper">
                                        <div class="preview-list-user-icon">
                                            <i class="las la-battery-quarter"></i>
                                        </div>
                                        <div class="preview-list-user-content">
                                            <span>{{ __('Buyer Name') }}:</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="preview-list-right">
                                    <span>{{ $item->buyer_user->username }}</span>
                                </div>
                            </div>

                            <div class="preview-list-item">
                                <div class="preview-list-left">
                                    <div class="preview-list-user-wrapper">
                                        <div class="preview-list-user-icon">
                                            <i class="las la-battery-quarter"></i>
                                        </div>
                                        <div class="preview-list-user-content">
                                            <span>{{ __('Tether buy amount') }}:</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="preview-list-right">
                                    <span>{{ $item->tetherAmount }}</span>
                                </div>
                            </div>

                            <div class="preview-list-item">
                                <div class="preview-list-left">
                                    <div class="preview-list-user-wrapper">
                                        <div class="preview-list-user-icon">
                                            <i class="las la-funnel-dollar"></i>
                                        </div>
                                        <div class="preview-list-user-content">
                                            <span>{{ __('Suggested Price') }}:</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="preview-list-right">
                                    <span
                                        style="color:yellow">{{ $item->price }}({{ __($item->priceType == 0 ? __('Market Order') . ' ▲' . $item->margin . '%' : __('Limit Order')) }})
                                        <br>{{ __('Market price') }} : {{ $prices['KRW'] . ' KRW' }}
                                    </span>
                                </div>
                            </div>

                            <div class="preview-list-item">
                                <div class="preview-list-left">
                                    <div class="preview-list-user-wrapper">
                                        <div class="preview-list-user-icon">
                                            <i class="las la-battery-quarter"></i>
                                        </div>
                                        <div class="preview-list-user-content">
                                            <span>{{ __('Buy Price') }}:</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="preview-list-right">
                                    <span>{{ $item->totalPayAmount }}</span>
                                </div>
                            </div>

                            <div class="preview-list-item">
                                <div class="preview-list-left">
                                    <div class="preview-list-user-wrapper">
                                        <div class="preview-list-user-icon">
                                            <i class="las la-battery-quarter"></i>
                                        </div>
                                        <div class="preview-list-user-content">
                                            <span>{{ __('Trade start time') }}:</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="preview-list-right">
                                    <span>
                                        <?php
                                        $creationDateTime = new DateTime($item->created_at);
                                        $creationDateTime->setTimezone($curTimeZone);
                                        echo $creationDateTime->format('Y-m-d H:i') . ' ';
                                        ?>
                                    </span>
                                </div>
                            </div>
                            <div class="preview-list-item">
                                <div class="preview-list-left">
                                    <div class="preview-list-user-wrapper">
                                        <div class="preview-list-user-icon">
                                            <i class="las la-battery-quarter"></i>
                                        </div>
                                        <div class="preview-list-user-content">
                                            <span>{{ __('Trade end time') }}:</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="preview-list-right">
                                    <span>
                                        <?php
                                        if ($item->ended_at == null) {
                                            echo __('Ongoing');
                                        } else {
                                            $endDateTime = new DateTime($item->ended_at);
                                            $endDateTime->setTimezone($curTimeZone);
                                            echo $endDateTime->format('Y-m-d H:i') . ' ';
                                        }
                                        ?>
                                    </span>
                                </div>
                            </div>


                            <?php if($isBuyer) { ?>
                            <div class="preview-list-item">
                                <div class="preview-list-left">
                                    <div class="preview-list-user-wrapper">
                                        <div class="preview-list-user-icon">
                                            <i class="las la-battery-quarter"></i>
                                        </div>
                                        <div class="preview-list-user-content">
                                            <span>{{ __('Bank Name') }}:</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="preview-list-right">
                                    <span>{{ $item->bankName }}</span>
                                </div>
                            </div>

                            <div class="preview-list-item">
                                <div class="preview-list-left">
                                    <div class="preview-list-user-wrapper">
                                        <div class="preview-list-user-icon">
                                            <i class="las la-battery-quarter"></i>
                                        </div>
                                        <div class="preview-list-user-content">
                                            <span>{{ __('Bank Account Number') }}:</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="preview-list-right">
                                    <span>{{ $item->accountNumber }}</span>
                                </div>
                            </div>

                            <div class="preview-list-item">
                                <div class="preview-list-left">
                                    <div class="preview-list-user-wrapper">
                                        <div class="preview-list-user-icon">
                                            <i class="las la-battery-quarter"></i>
                                        </div>
                                        <div class="preview-list-user-content">
                                            <span>{{ __('Bank Account Holder') }}:</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="preview-list-right">
                                    <span>{{ $item->accountName }}</span>
                                </div>
                            </div>
                            <?php } ?>

                            <?php if($isBuyer) { ?>
                            <div class="preview-list-item">
                                <div class="preview-list-left">
                                    <div class="preview-list-user-wrapper">
                                        <div class="preview-list-user-icon">
                                            <i class="las la-battery-quarter"></i>
                                        </div>
                                        <div class="preview-list-user-content">
                                            <span>QR Code:</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="avatar-preview">
                                    <img src="{{ get_image($item->QRImage, 'QRImage') }}">
                                </div>
                            </div>
                            <?php } ?>

                            <?php if($isBuyer == false) { ?>
                            <form class="card-form" action="{{ setRoute('user.mytrade.updatebankinfo') }}"
                                method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <div class="preview-list-item">
                                    <div class="preview-list-left">
                                        <div class="preview-list-user-wrapper">
                                            <div class="preview-list-user-icon">
                                                <i class="las la-battery-quarter"></i>
                                            </div>
                                            <div class="preview-list-user-content">
                                                <span>{{ __('Bank Name') }}:</span>
                                            </div>
                                        </div>
                                    </div>
                                    <input type="hidden" name="itemId" value="{{ $item->id }}">
                                    <div class="preview-list-right">
                                        <input type="text" size="10" style="color: black"
                                            value="{{ $item->bankName }}" name="bankName" id="bankName" />
                                    </div>
                                </div>

                                <div class="preview-list-item">
                                    <div class="preview-list-left">
                                        <div class="preview-list-user-wrapper">
                                            <div class="preview-list-user-icon">
                                                <i class="las la-battery-quarter"></i>
                                            </div>
                                            <div class="preview-list-user-content">
                                                <span>{{ __('Bank Account Number') }}:</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="preview-list-right">
                                        <input type="number" size="10" style="color: black"
                                            value="{{ $item->accountNumber }}" name="accountNumber"
                                            id="accountNumber" />
                                    </div>
                                </div>

                                <div class="preview-list-item">
                                    <div class="preview-list-left">
                                        <div class="preview-list-user-wrapper">
                                            <div class="preview-list-user-icon">
                                                <i class="las la-battery-quarter"></i>
                                            </div>
                                            <div class="preview-list-user-content">
                                                <span>{{ __('Bank Account Holder') }}:</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="preview-list-right">
                                        <input type="text" size="10" style="color: black"
                                            value="{{ $item->accountName }}" name="accountName" id="accountName" />
                                    </div>
                                </div>
                                <div class="col-xl-12 col-lg-12">
                                    <button type="submit"
                                        class="btn--base w-100">{{ __('Update Bank Information') }}</button>
                                </div>
                            </form>
                            <?php } ?>

                            <?php if($isBuyer == false) { ?>

                            <form class="card-form" action="{{ setRoute('user.mytrade.uploadQRImage') }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <div class="preview-list-item">
                                    <div class="preview-list-left">
                                        <div class="preview-list-user-wrapper">
                                            <div class="preview-list-user-icon">
                                                <i class="las la-battery-quarter"></i>
                                            </div>
                                            <div class="preview-list-user-content">
                                                <span>QR Code:</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="preview-thumb profile-thumb">
                                        <img src="{{ get_image($item->QRImage, 'QRImage') }}">
                                        <input type="hidden" name="itemId" value="{{ $item->id }}">
                                        <?php if($isBuyer == false && $item->state == 'open') { ?>
                                        <div class="avatar-edit">
                                            <input type="file" class="profilePicUpload" name="QRImage"
                                                id="QRImage">
                                        </div>
                                        <?php } ?>
                                    </div>
                                </div>
                                <?php if($isBuyer == false && $item->state == 'open') { ?>
                                <div class="col-xl-12 col-lg-12">
                                    <button type="submit" class="btn--base w-100">{{ __('Upload QR Image') }}</button>
                                </div>
                                <?php } ?>
                            </form>

                            <?php } ?>

                            <div class="preview-list-item">
                                <div class="preview-list-left">
                                    <div class="preview-list-user-wrapper">
                                        <div class="preview-list-user-icon">
                                            <i class="las la-battery-quarter"></i>
                                        </div>
                                        <div class="preview-list-user-content">
                                            <span>{{ __('Proof for payment') }}:</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="preview-thumb profile-thumb">
                                    <?php if(str_contains($item->payProof, 'pdf')) { ?>
                                    <a href="{{ URL::to(get_image($item->payProof, 'proof')) }}"
                                        target="_blank">{{ __('View PDF') }}</a>
                                    <?php }else { ?>
                                    <img src="{{ get_image($item->payProof, 'proof') }}" name="img"><br>
                                    <?php } ?>


                                    <?php if($isBuyer && $item->state == 'open') { ?>
                                    <form class="card-form" action="{{ setRoute('user.mytrade.uploadproof') }}"
                                        method="POST" enctype="multipart/form-data">
                                        @csrf
                                        @method('PUT')
                                        <input type="text" name="itemId" value="{{ $item->id }}" hidden>
                                        <div class="avatar-edit">
                                            <input type="file" class="profilePicUpload" name="imageProof"
                                                id="imageProof">
                                        </div>
                                        <div class="col-xl-12 col-lg-12">
                                            <button type="submit"
                                                class="btn--base w-100">{{ __('Upload Proof') }}</button>
                                        </div>
                                    </form>
                                    <?php } ?>
                                </div>

                            </div>


                            <?php
                            if($item->state == 'cancel') {
                            ?>
                            <div class="preview-list-item">
                                <div class="preview-list-left">
                                    <div class="preview-list-user-wrapper">
                                        <div class="preview-list-user-icon">
                                            <i class="las la-money-check-alt"></i>
                                        </div>
                                        <div class="preview-list-user-content">
                                            <span class="last">{{ __('Cancel Reason') }}:</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="preview-list-right">
                                    <textarea readonly style="color: black" cols="70" rows="7">{{ $item->cancelReason }}</textarea>
                                </div>
                            </div>
                            <?php } ?>

                            <br>
                            <?php if($isBuyer == false && $item->state == 'open') { ?>
                            <div class="col-xl-12 col-lg-12">
                                <span>USDT {{ __('Balance') }}:{{ $usdtBalance }}</span><br>
                                <button type="button" class="btn--base w-100"
                                    onclick="onSendButton()">{{ __('Send Tether') }}</button>
                            </div>
                            <?php } ?>

                            <?php if($item->state == 'open') { ?>
                            <br>
                            <div class="col-xl-12 col-lg-12">
                                <button type="button" class="btn--base w-100"
                                    onclick="onCancelButton()">{{ __('Cancel Trade') }}</button>
                            </div>
                            <?php } ?>

                            <?php if($item->state == 'done') { ?>
                            <br>
                            <div class="col-xl-12 col-lg-12">
                                <button type="button" class="btn--base w-100"
                                    onclick="onDownloadPDFButton()">{{ __('Download PDF') }}</button>
                            </div>
                            <?php } ?>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="row justify-content-center mb-30-none">
        <div class="col-lg-6 mb-30">
            <div class="dash-payment-item-wrapper">
                <div class="dash-payment-item active">
                    <div class="dashboard-header-wrapper d-flex justify-content-between align-items-center">
                        <h4 class="title">
                            <i class="las la-user-circle"></i> {{ __('Trader Profile') }}
                        </h4>
                        <button id="toggleProfile" class="btn btn-sm btn-outline-primary">
                            <i class="las la-angle-down"></i>
                        </button>
                    </div>
                    <div class="card-body profile-body-wrapper" id="profileContent" style="display: none;">
                        <div class="profile-settings-wrapper">
                            <div class="profile-thumb-content">
                                <div class="preview-thumb profile-thumb">
                                    <div class="avatar-preview">
                                        <div class="profilePicPreview bg_img"
                                            data-background="{{ $otherUser->userImage }}"
                                            style="background-image: url(&quot;{{ $otherUser->userImage }}&quot;);"></div>
                                    </div>
                                </div>
                                <div class="profile-content">
                                    <ul class="user-info-list mt-md-2">
                                        <li><i class="las la-envelope"></i>{{ $otherUser->email }}</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="profile-form-area">
                            <div class="col-xl-6 col-lg-6 form-group">
                                <label>{{ __('Nickname') }}</label>
                                <input type="text" readonly class="form--control" name="username"
                                    value="{{ old('username', $otherUser->username ?? '') }}">
                            </div>
                            <div class="row">
                                <div class="col-xl-6 col-lg-6 form-group">
                                    <label>{{ __('Grade') }}</label>
                                    <input type="text" readonly class="form--control" name="last_name"
                                        value="{{ $otherUser->grade == 2 ? 'Gold' : ($otherUser->grade == 1 ? 'Silver' : 'Common') }}">
                                </div>
                                <div class="col-xl-6 col-lg-6 form-group">
                                    <label>{{ __('Status') }}</label>
                                    <input type="text" readonly class="form--control" name="last_name"
                                        value="{{ $otherUser->status == 1 ? 'Active' : 'Block' }}">
                                </div>
                                <div class="col-xl-6 col-lg-6 form-group">
                                    <label>SMS {{ __('verified') }}</label>
                                    <input type="text" readonly class="form--control" name="last_name"
                                        value="{{ $otherUser->sms_verified == 1 ? 'verified' : 'Unverified' }}">
                                </div>
                                <div class="col-xl-6 col-lg-6 form-group">
                                    <label>Email {{ __('verified') }}</label>
                                    <input type="text" readonly class="form--control" name="last_name"
                                        value="{{ $otherUser->email_verified == 1 ? 'verified' : 'Unverified' }}">
                                </div>
                                <div class="col-xl-6 col-lg-6 form-group">
                                    <label>KYC {{ __('verified') }}</label>
                                    <input type="text" readonly class="form--control" name="last_name"
                                        value="{{ $otherUser->kyc_verified == 1 ? 'verified' : 'Unverified' }}">
                                </div>
                                <div class="col-xl-6 col-lg-6 form-group">
                                    <label>{{ __('First Name') }}</label>
                                    <input type="text" readonly class="form--control" name="first_name"
                                        value="{{ old('first_name', $otherUser->firstname ?? '') }}">
                                </div>
                                <div class="col-xl-6 col-lg-6 form-group">
                                    <label>{{ __('Last Name') }}</label>
                                    <input type="text" readonly class="form--control" name="last_name"
                                        value="{{ old('last_name', $otherUser->lastname ?? '') }}">
                                </div>
                                <!-- <div class="col-xl-6 col-lg-6 form-group">
                                                                                                                                    <label>Country</label>
                                                                                                                                    <input type="text" readonly class="form--control" name="last_name" value="{{ old('country', $otherUser->address->country ?? '') }}">
                                                                                                                                </div> -->
                                <div class="col-xl-6 col-lg-6 form-group">
                                    <label>{{ __('Mobile Number') }}</label>
                                    <input type="number" readonly class="form--control" name="mobile"
                                        value="{{ old('mobile', $otherUser->mobile ?? '') }}">
                                </div>
                                <div class="col-xl-6 col-lg-6 form-group">
                                    <label>{{ __('Total Buy Count') }}</label>
                                    <input type="number" readonly class="form--control"
                                        value="{{ $otherUser->totalBuyCount }}">
                                </div>
                                <div class="col-xl-6 col-lg-6 form-group">
                                    <label>{{ __('Total Buy Amount') }}(USDT)</label>
                                    <input type="number" readonly class="form--control"
                                        value="{{ $otherUser->totalBuyAmount }}">
                                </div>
                                <div class="col-xl-6 col-lg-6 form-group">
                                    <label>{{ __('Total Sell Count') }}</label>
                                    <input type="number" readonly class="form--control"
                                        value="{{ $otherUser->totalSellCount }}">
                                </div>
                                <div class="col-xl-6 col-lg-6 form-group">
                                    <label>{{ __('Total Sell Amount') }}(USDT)</label>
                                    <input type="number" readonly class="form--control"
                                        value="{{ $otherUser->totalSellAmount }}">
                                </div>
                                <div class="col-xl-6 col-lg-6 form-group">
                                    <label>{{ __('Total Cancel Count') }}</label>
                                    <input type="number" readonly class="form--control"
                                        value="{{ $otherUser->totalCancelCount }}">
                                </div>
                                <div class="col-xl-6 col-lg-6 form-group">
                                    <label>{{ __('Last Login At') }}</label>
                                    <input type="text" readonly class="form--control" name="state"
                                        value="<?php
                                        $endDateTime = new DateTime($otherUser->update_at);
                                        $endDateTime->setTimezone($curTimeZone);
                                        echo $endDateTime->format('Y-m-d H:i') . ' ';
                                        ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    {{-- <script type="text/javascript" src="https://cdn.skypack.dev/jquery@3.5.1"></script> --}}

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // document.getElementById('start-chat-header').click();

            const toggleButton = document.getElementById('toggleProfile');
            const profileContent = document.getElementById('profileContent');

            toggleButton.addEventListener('click', function() {
                if (profileContent.style.display === 'none') {
                    profileContent.style.display = 'block';
                    toggleButton.innerHTML = '<i class="las la-angle-up"></i>';
                } else {
                    profileContent.style.display = 'none';
                    toggleButton.innerHTML = '<i class="las la-angle-down"></i>';
                }
            });
        });

        var msg = '{{ Session::get('alert') }}';
        var exist = '{{ Session::has('alert') }}';
        if (exist) {
            setTimeout(() => {
                //alert(msg);
                openMsgModal(msg);
            }, 1000);

        }

        function copyLink(itemId) {
            var url = location.href;
            console.log("copyLink : " + url);
            var $temp = $('<input>');
            $('body').append($temp);
            $temp.val(url).select();
            document.execCommand('copy');
            $temp.remove();
            openMsgModal('URL이 복사되었습니다.');
            //alert('URL이 복사되었습니다.');
        };

        function onViewTransInfo() {

            var hash = @json($item->transactionHash);
            location.href = "https://etherscan.io/tx/" + hash;

        }

        function onSendButton() {

            window.location.href = '{{ route('user.mytrade.send', $item->id) }}';
        }

        function onCancelButton() {

            openCancelModal("{{ setRoute('user.mytrade.cancel') }}");
        }

        function onDownloadPDFButton() {

            window.location.href = '{{ route('user.mytrade.downloadpdf', $item->id) }}';
        }

        function openCancelModal(URL) {


            openModalByContent({
                    content: `<div class="card modal-alert border-0">
                                <div class="card-body">
                                    <form class="card-form" action="${URL}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="_token" value="${laravelCsrf()}">
                                        <input type="hidden" name="itemId" value="{{ urlSafeEncrypt($item->id) }}">
                                        <div class="head mb-3" style="color: #ffffff">
                                            Do you really want to cancel this trade?<br>
                                            If you want to cancel, please enter cancel reason
                                        </div>
                                        <textarea style="color: black" cols="70" rows="7" name="cancelReason"></textarea>
                                        <div class="foot d-flex align-items-center justify-content-between">
                                            <button type="button" class="modal-close btn btn--info rounded text-light">Close</button>
                                            <button type="submit" class="alert-submit-btn btn btn--danger btn-loading rounded text-light">Confirm cancel</button>
                                        </div>
                                    </form>
                                </div>
                            </div>`,
                },

            );
        }

        function openMsgModal(message) {


            openModalByContent({
                    content: `<div class="card modal-alert border-0">
                                <div class="card-body">
                                    <form class="card-form" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        @method('PUT')
                                        <div class="head mb-3" style="color: #ffffff">
                                            ${message}<br>
                                        </div>
                                        <div class="foot d-flex align-items-center justify-content-between">
                                            <button type="button" class="modal-close btn btn--info rounded text-light">확인</button>
                                        </div>
                                    </form>
                                </div>
                            </div>`,
                },

            );
        }
    </script>
@endpush
