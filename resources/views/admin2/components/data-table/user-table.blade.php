<table class="custom-table user-search-table">
    <thead>
        <tr>
            <th></th>
            <th>Username</th>
            <th>Email</th>
            <th>Email Verified Status</th>
            <th>Status</th>
            <th>Balance</th>
            <th>Pending</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($users ?? [] as $key => $item)
            <tr>
                <td>
                    <ul class="user-list">
                        <li><img src="{{ $item->userImage }}" alt="user"></li>
                    </ul>
                </td>
                <td><span>{{ $item->username }}</span></td>
                <td>{{ $item->email }}</td>
                <td>
                    <span class="{{ $item->StringEmailVerifiedStatus->class }}">{{ $item->StringEmailVerifiedStatus->value }}</span>
                </td>
                <td>
                    @if (Route::currentRouteName() == "admin.users.kyc.unverified")
                        <span class="{{ $item->kycStringStatus->class }}">{{ $item->kycStringStatus->value }}</span>
                    @else
                        <span class="{{ $item->stringStatus->class }}">{{ $item->stringStatus->value }}</span>
                    @endif
                </td>
                <td>
                    $ {{ number_format($item->usdtBalance, 2) }}
                </td>
                <td>
                    $ {{ number_format(get_pending($item->id), 2) }}
                </td>
                <td>
                    @if (Route::currentRouteName() == "admin.users.kyc.unverified")
                        @include('admin.components.link.info-default',[
                            'href'          => setRoute('admin.users.kyc.details', $item->username),
                            'permission'    => "admin.users.kyc.details",
                        ])
                    @else
                        @include('admin.components.link.info-default',[
                            'href'          => setRoute('admin.users.details', $item->username),
                            'permission'    => "admin.users.details",
                        ])
                    @endif
                </td>
            </tr>
        @empty
            @include('admin.components.alerts.empty',['colspan' => 6])
        @endforelse
    </tbody>
</table>
