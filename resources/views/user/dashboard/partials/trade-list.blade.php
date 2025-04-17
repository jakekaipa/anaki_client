
<style>
    .new-chat{
        color: white; 
        background-color: red; 
        font-weight: bold; 
        padding: 5px 10px; 
        border-radius: 12px; 
        font-size: 10px; 
        text-transform: uppercase;
        margin-left:10px;
    }
</style>

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
            {{-- <a href="{{ setRoute('user.mytrade.view', urlSafeEncrypt($item->id)) }}" class="go-detail">
                <span class="txt">{{ __('View') }}</span>
            </a> --}}
            <a href="#" class="go-detail" onclick="event.preventDefault(); showTradeDetails('{{ $item->id }}', '{{ urlSafeEncrypt($item->id) }}',true);">
                <span class="txt">{{ __('View') }}</span> 
            </a>
            @if($item->getLastMessage && $item->getLastMessage->read_user_id == 0 && $item->getLastMessage->receiver_id == Auth::user()->id)
                <span class="new-chat" id="{{ $item->getLastMessage->chat_id.'-'.$item->getLastMessage->receiver_id }}">New</span>
            @endif
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
