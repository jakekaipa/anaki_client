@php
    $default = get_default_language_code();
@endphp
@extends('user.layouts.master')
@push('css')

<style>
    .pagination {
        margin-top: 20px !important;
    }
</style>

@section('breadcrumb')
    @include('user.components.breadcrumb',['breadcrumbs' => [
        [
            'name'  => __("Dashboard"),
            'url'   => setRoute("user.dashboard"),
        ]
    ], 'active' => __("Dashboard")])
@endsection

@section('content')
<div class="table-wrapper pt-60">
    <div class="row justify-content-center">
        <div class="col-xl-12">
            <div class="table-area table-responsive">
                <h4 class="title">Donation History</h4>
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>Campaign Title</th>
                            <th>Campaign Goal</th>
                            <th>Raised</th>
                            <th>To Go</th>
                            <th>Donation Amount</th>
                            <th>Status</th>
                            <th>Donation Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($donation_history ?? [] as $item)
                        {{-- @dd($item->campaign) --}}
                            <tr>
                                <td>{{ @$item->campaign->title->language->$default->title }}</td>
                                <td>${{ get_amount(@$item->campaign->our_goal) }}</td>
                                <td>${{ get_amount(@$item->campaign->raised) }}</td>
                                <td>${{ get_amount(@$item->campaign->to_go) }}</td>
                                <td>${{ get_amount(@$item->request_amount) }}</td>
                                <td>
                                    <span class="badge {{ $item->stringStatus->class }}">{{ $item->stringStatus->value }}</span>
                                </td>
                                <td>{{ dateFormat('d M Y', $item->created_at) }}</td>
                            </tr>
                        @empty
                            @include('admin.components.alerts.empty',['colspan' => 7])
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $donation_history->links() }}
        </div>
    </div>
</div>
@endsection
