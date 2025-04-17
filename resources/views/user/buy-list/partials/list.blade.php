@forelse ($listData as $item)
    <li class="plist__item" data-href="{{ setRoute('user.buy-list.preview', urlSafeEncrypt($item->id)) }}">
        <div class="plist__head">
            <div class="plist__head-col">
                <div class="flex-s-c">
                    <p class="plist__head-txt">{{ __('Buyer') }}</p>
                    <span class="plist__head-txt-outline">{{ $item->offerLabel }}</span>
                </div>
            </div>
            <div class="plist__head-col">
                <div class="flex-s-c">
                    <span class="plist__head-txt dn">{{ __('제안단가') }}</span>
                </div>
            </div>
        </div>
        <div class="plist__info">
            <div class="plist__col">
                <div class="plist__auth">
                    <span class="plist__auth1">{{ __('Buyer') }}</span>
                    <span class="plist__auth2">{{ $item->user->username }}</span>
                    <span
                        class="plist__auth3 {{ $item->user->email_verified == 1 && $item->user->kyc_verified == 1 ? 'cf' : 'uncf' }}">
                        {{ $item->user->email_verified == 1 && $item->user->kyc_verified == 1 ? __('kyc_verified') : __('kyc_unverified') }}
                    </span>
                </div>
            </div>
            <div class="plist__col">
                <div class="plist__value-wrap">
                    <span class="plist__value">
                        {{ number_format($item->priceType == 0 ? ($prices['KRW'] * (100 + $item->offerMargin)) / 100 : $item->fixedPrice) }}
                        {{ __('KRW') }}
                    </span>
                    @if ($item->priceType == 0)
                        <span
                            class="plist__arrow {{ $item->offerMargin >= 0 ? 'up' : 'down' }}">{{ abs($item->offerMargin) }}%</span>
                    @endif 
                </div> 
            </div>
            <div class="plist__col w-full">
                <div class="plist__how-wrap">
                    @if (isset($item->bankName) && strlen(trim($item->bankName)) > 0)
                        <span class="plist__how transfer">{{ __('bank_transfer') }}</span>
                    @endif
                    @if (isset($item->payQR) && strlen(trim($item->payQR)) > 0)
                        <span class="plist__how qr">{{ __('Qr Pay') }}</span>
                    @endif
                </div>
            </div>
            <div class="plist__col">
                <span class="plist__ttl">{{ __('trade_time_limit') }}: {{ $item->offerTimeLimit }}{{ __('minutes') }}</span>
                <span class="plist__log">{{ __('last_access') }}:
                    @php
                        $diff = $now->diff(new DateTime($item->user->updated_at));
                        if ($diff->days == 0 && $diff->h == 0 && $diff->i < 3) {
                            echo __('currently_logged_in');
                        } else {
                            $time = '';
                            if ($diff->days > 0) {
                                $time = trans_choice('days_ago', $diff->days, ['value' => $diff->days]);
                            } elseif ($diff->h > 0) {
                                $time = trans_choice('hours_ago', $diff->h, ['value' => $diff->h]);
                            } elseif ($diff->i > 0) {
                                $time = trans_choice('minutes_ago', $diff->i, ['value' => $diff->i]);
                            } else {
                                $time = __('just_now');
                            }
                            echo $time;
                        }
                    @endphp
                </span>
            </div>
            <div class="plist__col">
                <ul class="limit">
                    <li>{{ __('min_purchase') }}: {{ number_format($item->tradeVolMin) }} {{ __('KRW') }}</li>
                    <li>{{ __('max_purchase') }}: {{ number_format($item->tradeVolMax) }} {{ __('KRW') }}</li>
                </ul>
            </div>
            <div class="plist__btn">
                @if ($item->password != null)
                    <div class="plist__deal" style="pointer-events: none; cursor: default;"><span class="txt">{{ __('Private Trade') }}</span></div>
                @endif
                <a href="{{ setRoute('user.buy-list.preview', urlSafeEncrypt($item->id)) }}" class="plist__sell">
                    <span class="txt">{{ __('Sell') }}</span>
                </a>
            </div>
        </div>
    </li>
@empty
    <li class="plist__item">
        <h3 class="text-warning text-center">{{ __('no_data_found') }}</h3>
    </li>
@endforelse
