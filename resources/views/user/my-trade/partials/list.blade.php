@forelse ($listData as $item)
    <tr>
        @php
            if ($item->offer_user_id == auth()->user()->id) {
                $tradingDirection = $item->order_type;
                $dealerName = ($item->client_user->realname)? $item->client_user->realname : $item->client_user->username;
            } else {
                if ($item->order_type == 'buy') {
                    $tradingDirection = 'sell';
                } else {
                    $tradingDirection = 'buy';
                }

                $dealerName = ($item->offer_user->realname)? $item->offer_user->realname : $item->offer_user->username;
            }

            $nowText = now()->format('Y-m-d H:i:s');
            $isOngoing = $item->state == 'open' && $item->ended_at >= $nowText;

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
            } elseif ($item->state == 'dispute-solved') {
                $style = 'complete';
                $statusMsg = __('분쟁 해결됨');
            }
        @endphp
        <td>{{ __($tradingDirection) }}</td>
        <td>{{ $dealerName }}</td>
        <td>{{ number_format($item->tetherAmount, 2) }} <span class="unit">USDT</span></td>
        <td>{{ number_format($item->totalPayAmount) }} <span class="unit">{{ __('KRW') }}</span></td>
        <td>{{ number_format($item->price) }} <span class="unit">{{ __('KRW') }}</span></td>
        <td>
            <span class="condition {{ $style }}"> {{ $statusMsg }} </span>
        </td>
        <td>
            @php
                $creationDateTime = new DateTime($item->created_at);
                $creationDateTime->setTimezone($curTimeZone);
                echo $creationDateTime->format('Y-m-d H:i');
            @endphp
        </td>
        <td>
            @php
                if ($item->ended_at) {
                    $endTime = new DateTime($item->ended_at);
                    $endTime->setTimezone($curTimeZone);
                    echo $endTime->format('Y-m-d H:i');
                } else {
                    echo '-';
                }
            @endphp
        </td>
        <td>
            <a href="#" class="go-detail"
                onclick="event.preventDefault(); showTradeDetails('{{ $item->id }}', '{{ urlSafeEncrypt($item->id) }}');">
                <span class="txt">{{ __('View') }}</span>
            </a>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="9">
            <div class="deal-empty">
                <p class="empty-txt">{{ __('No Records Found') }}</p>
            </div>
        </td>
    </tr>
@endforelse
