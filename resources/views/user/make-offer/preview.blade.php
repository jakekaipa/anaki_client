@php
    $default = get_default_language_code();
@endphp
@extends('user.layouts.master')

@section('content')
    <div class="dashboard-area mt-10">
        <div class="dashboard-header-wrapper">
            <h3 class="title">{{ __('Trade Offer') }}</h3>
        </div>
    </div>
    <div class="row justify-content-center mb-30-none">
        <div class="col-lg-6 mb-30">
            <div class="dash-payment-item-wrapper">
                <div class="dash-payment-item active">
                    <div class="dash-payment-body">
                        <div class="preview-list-wrapper">
                            <div class="preview-list-item">
                                <div class="preview-list-user-wrapper">
                                    <button class="btn btn-copy" id="copyButton">
                                        <i class="las la-copy"></i>
                                    </button>
                                    <div class="link-text" id="linkText">
                                        {{ $action === 'sell' ? setRoute('user.sell-list.preview', urlSafeEncrypt($order_id)) : setRoute('user.buy-list.preview', urlSafeEncrypt($order_id)) }}
                                    </div>
                                </div>
                            </div>
                            @if (isset($item['password']) && $item['password'] != null)
                                <div class="preview-list-item">
                                    <div class="preview-list-left">
                                        <div class="preview-list-user-wrapper">
                                            <div class="preview-list-user-icon">
                                                <i class="las la-funnel-dollar"></i>
                                            </div>
                                            <div class="preview-list-user-content">
                                                <span>{{ __('Password') }}:</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="preview-list-right">
                                        <span>{{ $item['password'] }}</span>
                                    </div>
                                </div>
                            @endif

                            <div class="preview-list-item">
                                <div class="preview-list-left">
                                    <div class="preview-list-user-wrapper">
                                        <div class="preview-list-user-icon">
                                            <i class="las la-funnel-dollar"></i>
                                        </div>
                                        <div class="preview-list-user-content">
                                            <span>{{ __('Price') }}:</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="preview-list-right">
                                    <span>{{ $item['priceType'] == 0 ? ($prices['KRW'] * (100 + $item['offerMargin'])) / 100 . ' ' . __('KRW') : $item['fixedPrice'] }}</span>
                                    <span>({{ __($item['priceType'] == 0 ? __('Market Order') . ' ▲' . $item['offerMargin'] . '%' : __('Limit Order')) }})</span>
                                </div>
                            </div>

                            <div class="preview-list-item">
                                <div class="preview-list-left">
                                    <div class="preview-list-user-wrapper">
                                        <div class="preview-list-user-icon">
                                            <i class="las la-battery-quarter"></i>
                                        </div>
                                        <div class="preview-list-user-content">
                                            <span>{{ __('Trade Amount Limits') }}:</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="preview-list-right">
                                    <span>{{ number_format($item['tradeVolMin']) . __('KRW') . ' ~ ' . number_format($item['tradeVolMax']) . __('KRW') }}</span>
                                </div>
                            </div>

                            <div class="preview-list-item">
                                <div class="preview-list-left">
                                    <div class="preview-list-user-wrapper">
                                        <div class="preview-list-user-icon">
                                            <i class="las la-battery-quarter"></i>
                                        </div>
                                        <div class="preview-list-user-content">
                                            <span>{{ __('Transaction Time Limit') }}:</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="preview-list-right">
                                    <span>{{ $item['offerTimeLimit'] . ' 분' }}</span>
                                </div>
                            </div>

                            <div class="preview-list-item">
                                <div class="preview-list-left">
                                    <div class="preview-list-user-wrapper">
                                        <div class="preview-list-user-icon">
                                            <i class="las la-money-check-alt"></i>
                                        </div>
                                        <div class="preview-list-user-content">
                                            <span>{{ __('Offer Condition') }}:</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="preview-list-right">
                                    <textarea readonly style="color: black" cols="70" rows="7">{{ $item['offerCondition'] }}</textarea>
                                </div>
                            </div>
                            <div class="preview-list-item">
                                <div class="preview-list-left">
                                    <div class="preview-list-user-wrapper">
                                        <div class="preview-list-user-icon">
                                            <i class="las la-money-check-alt"></i>
                                        </div>
                                        <div class="preview-list-user-content">
                                            <span>{{ __('Authentication') }}:</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="preview-list-right">
                                    <span>
                                        {{ implode(
                                            ', ',
                                            array_filter([
                                                $item['needMobileAuth'] ? __('Can Trade Mobile Verified') : null,
                                                $item['needKYCAuth'] ? __('Can Trade KYC Verified') : null,
                                                $item['needAccountAuth'] ? __('Can Trade Account Verified') : null,
                                                isset($item['checkboxAuth']) && $item['checkboxAuth'] ? __('Ask Auth') : null,
                                                isset($item['checkboxShowName']) && $item['checkboxShowName'] ? __('Show Name') : null,
                                            ]),
                                        ) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 확인 버튼 추가 -->
    <div class="row justify-content-center mt-4">
        <div class="col-lg-6">
            <div class="text-center">
                <a href="{{ setRoute('user.make-offer.index') }}" class="btn btn--base">{{ __('Close') }}</a>
            </div>
        </div>
    </div>
@endsection

@push('style')
    <style>
        .dash-payment-title-area {
            padding: 15px 20px;
            border-bottom: 1px solid #e5e5e5;
        }

        .dash-payment-title-area .left-area {
            flex: 1;
        }

        .dash-payment-title-area .right-area {
            flex-shrink: 0;
        }

        .btn-copy {
            padding: 5px 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .btn-copy:hover {
            background-color: #0056b3;
        }

        .link-copy-wrapper {
            display: flex;
            align-items: center;
        }

        .link-text {
            flex-grow: 1;
            padding: 5px 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            color: #333;
            background-color: #fff;
            margin-right: 10px;
            word-break: break-all;
        }

        .btn-copy {
            padding: 5px 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 0 4px 4px 0;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .btn-copy:hover {
            background-color: #0056b3;
        }

        .copy-feedback {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: rgba(0, 0, 0, 0.8);
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            z-index: 1000;
        }
    </style>
@endpush

@push('script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const copyButton = document.getElementById('copyButton');
            const linkText = document.getElementById('linkText');

            copyButton.addEventListener('click', function() {
                const textToCopy = linkText.textContent;

                navigator.clipboard.writeText(textToCopy).then(function() {
                    // 복사 성공
                    showFeedback('Link copied to clipboard!');
                    copyButton.innerHTML = '<i class="las la-check"></i>';
                    setTimeout(() => {
                        copyButton.innerHTML = '<i class="las la-copy"></i>';
                    }, 2000);
                }, function(err) {
                    // 복사 실패
                    console.error('Could not copy text: ', err);
                    showFeedback('Failed to copy link. Please try again.');
                });
            });

            function showFeedback(message) {
                const feedback = document.createElement('div');
                feedback.textContent = message;
                feedback.classList.add('copy-feedback');
                document.body.appendChild(feedback);
                feedback.style.display = 'block';
                setTimeout(() => {
                    feedback.style.display = 'none';
                    document.body.removeChild(feedback);
                }, 2000);
            }
        });
    </script>
@endpush
