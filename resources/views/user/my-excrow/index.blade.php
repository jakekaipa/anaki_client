@php
    $default = get_default_language_code();
@endphp
@extends('user.layouts.master')

@section('content')
<div class="table-area mt-10">
    <div class="table-wrapper">
        <div class="dashboard-header-wrapper">
            <h4 class="title">{{ __($page_title) }}</h4>
            <div class="dashboard-btn-wrapper">
                <div class="dashboard-btn">
                    <a href="{{ route('user.my-excrow.transaction') }}" class="btn--base"><i class="las la-plus me-1"></i> Create Trade</a>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>TRX ID</th>
                        <th>SELLING AMOUNT</th>
                        <th>ASKING AMOUNT</th>
                        <th>EXCHANE RATE</th>
                        <th>STATUS</th>
                        <th>DATE</th>
                        <th>ACTION</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($transactions as $item)
                        <tr>
                            <td>{{ $item->trx_id }}</td>
                            <td>{{ getDynamicAmount($item->forexcrow->amount, $item->forexcrow->saleCurrency->code) }}</td>
                            <td>{{ getDynamicAmount($item->forexcrow->rate, $item->forexcrow->rateCurrency->code) }}</td>
                            <td>1 {{ $item->forexcrow->saleCurrency->code }} = {{ getDynamicAmount(($item->forexcrow->rate / $item->forexcrow->amount), $item->forexcrow->rateCurrency->code) }}</td>
                            <td><span class="badge {{ $item->scrowStringStatus->class }}">{{ $item->scrowStringStatus->value }}</span></td>
                            <td>{{ dateFormat('d M Y | h:i:s A', $item->created_at) }}</td>
                            <td>
                                <div class="offer-btn-area">
                                    @if ($item->forexcrow->status == 1)
                                        <a href="#" class="btn btn--base reject offer-btn status-button" data-id="{{ $item->id }}" data-title="Close Excrow">Close</a>
                                        <a href="{{ setRoute('user.my-excrow.edit', $item->forexcrow->id) }}" class="btn btn--base counter offer-btn">Edit</a>
                                    @else

                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center"><span class="text-danger">No Records Found</span></td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <nav>
        {{ $transactions->links() }}
    </nav>
</div>
@endsection

@push('script')
    <script>
        $(".status-button").click(function(e){
            e.preventDefault();
            var actionRoute = "{{ setRoute('user.my-excrow.cancel') }}";
            var target      = $(this).data('id');
            var title       = $(this).data('title');
            var message     = `Are you sure to <strong>`+title+`</strong>?`;
            openAlertModal(actionRoute,target,message,title,"POST");
        });
    </script>
@endpush
