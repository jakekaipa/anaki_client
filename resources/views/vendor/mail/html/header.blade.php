@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === 'Laravel')
<img src="https://laravel.com/img/notification-logo.png" class="logo" alt="Laravel Logo">
@else
<img src="https://anakip2p.com/public/pub/img/logo-basics@2x.png" alt="anaki">
<!--{{ $slot }}-->
@endif
</a>
</td>
</tr>
