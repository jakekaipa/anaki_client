@php
    $default = get_default_language_code();
@endphp
@extends('user.layouts.master')

@section('content')
<div class="table-area mt-10">
    <div class="table-wrapper">
        <div class="dashboard-header-wrapper">
            <h4 class="title">Client Offer</h4>
        </div>
        <div class="table-responsive">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th></th>
                        <th>Trx Id</th>
                        <th>Selling Price</th>
                        <th>Offer Price</th>
                        <th>Creator</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($get_offers as $item)
                        <tr>
                            <td>
                                <ul class="user-list">
                                    <li><img src="{{ $item->creator->userImage }}" alt="product"></li>
                                </ul>
                            </td>
                            <td>{{ $item->forexcrow->transaction->trx_id }}</td>
                            <td data-label="offer-price">{{ getDynamicAmount($item->forexcrow->amount, 4).' '. $item->forexcrow->saleCurrency->code }} @ {{ getDynamicAmount($item->forexcrow->rate, 4).' '. $item->forexcrow->rateCurrency->code }}</td>
                            <td data-label="Amount">{{ getDynamicAmount($item->amount, 4).' '. $item->saleCurrency->code }} @ {{ getDynamicAmount($item->rate, 4).' '. $item->forexcrow->rateCurrency->code }}</td>
                            <td>{{ $item->creator->fullName }}</td>
                            <td>
                                <div class="offer-btn-area">
                                    @if ($item->forexcrow->status == 1)
                                        @if (Auth::user()->id == $item->receiver_id)
                                            @if ($item->status == 4)
                                                <button class="btn btn--base reject offer-btn">Rejected</button>
                                            @elseif ($item->status == 1)
                                                @if (Auth::user()->id == $item->for_user_id)
                                                    <button class="btn btn--base accept offer-btn">Accepted</button>
                                                @else
                                                    <a href="{{ setRoute('user.offer.preview', $item->id) }}" class="btn btn--base accept offer-btn">Pay</a>
                                                @endif
                                            @else
                                                <a href="" class="btn btn--base reject status-button offer-btn" data-id="{{ $item->id }}" data-title="Reject">Reject</a>
                                                <button class="counter-btn make_offer_button btn btn--base counter offer-btn"  data-forexcrow="{{ json_encode($item) }}" data-bs-toggle="modal" data-bs-target="#counter-offer">Counter Offer</button>
                                                <button class="btn btn--base accept status-button offer-btn" data-id="{{ $item->id }}" data-title="Accept">Accept</button>
                                            @endif
                                        @else
                                            @if ($item->status == 4)
                                                <button class="btn btn--base reject offer-btn">Rejected</button>
                                            @elseif ($item->status == 1)
                                                @if (Auth::user()->id == $item->for_user_id)
                                                    <button class="btn btn--base accept offer-btn">Accepted</button>
                                                @else
                                                    <a href="{{ setRoute('user.offer.preview', $item->id) }}" class="btn btn--base accept offer-btn">Pay</a>
                                                @endif
                                            @endif
                                        @endif
                                    @else
                                        <button class="btn btn--base reject offer-btn">Sold</button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center"><span class="text-danger">No Records Found</span></td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <nav>
        {{ $get_offers->links() }}
    </nav>
</div>

<div class="modal fade" id="offerCounterModal" tabindex="-1" aria-labelledby="offerCounterModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content counter-modal">
        <h4 class="title"><i class="fas fa-sync title-icon"></i> Make Counter Offer</h4>
        <form class="card-form mt-20" id="offer_form" action="{{ setRoute('user.offer.submit') }}" method="POST">
            @csrf
            <input type="hidden" name="type" value="COUNTER_OFFER">
            <input type="hidden" name="receiver_id" value="">
            <input type="hidden" name="creator_id" value="">
            <input type="hidden" name="forexcrow_id" value="">
            <div class="row">
                <div class="col-xl-12 col-lg-12 form-group">
                    <label>Amount</label>
                    <div class="input-group">
                        <input type="number" name="amount" class="form--control" placeholder="" disabled required>
                        <div class="input-group-append">
                            <span class="input-group-text copytext sale_currency"></span>
                        </div>
                    </div>
                </div>
                <div class="col-xl-12 col-lg-12 form-group">
                    <div class="note-area">
                        <code class="d-block amount_text">--</code>
                    </div>
                </div>
                <div class="col-xl-12 col-lg-12 form-group">
                    <label>Rate</label>
                    <div class="input-group">
                        <input type="number" name="rate" class="form--control" placeholder="" required>
                        <div class="input-group-append">
                            <span class="input-group-text copytext rate_curency"></span>
                        </div>
                    </div>
                </div>
                <div class="col-xl-12 col-lg-12 form-group">
                    <div class="note-area">
                        <code class="d-block rate_text">--</code>
                    </div>
                </div>
                <div class="col-xl-12 col-lg-12">
                    <button type="submit" class="btn--base w-100 btn-loading">Send Now</button>
                </div>
            </div>
        </form>
      </div>
    </div>
</div>
@endsection

@push('script')
    <script>
        $(document).ready(function () {
            // Offer modal show
            $(document).on('click', '.make_offer_button', function(e){
                var forexcrow =  JSON.parse($(this).attr('data-forexcrow'));

                $('#offer_form input[name="forexcrow_id"]').val(forexcrow.forexcrow_id);
                $('#offer_form input[name="receiver_id"]').val(forexcrow.receiver_id);
                $('#offer_form input[name="creator_id"]').val(forexcrow.creator_id);
                $('#offer_form input[name="amount"]').val(parseFloat(forexcrow.amount).toFixed(2));
                $('#offer_form .curency').text(forexcrow.sale_currency.code);
                $('#offer_form .sale_currency').text(forexcrow.sale_currency.code);
                $('#offer_form .rate_curency').text(forexcrow.rate_currency.code);
                $('#offer_form input[name="rate"]').val(parseFloat(forexcrow.rate).toFixed(2));
                $('#offer_form .amount_text').text('Amount : '+ parseFloat(forexcrow.amount).toFixed(2) + ' ' + forexcrow.sale_currency.code);
                $('#offer_form .rate_text').text('Rate : '+ parseFloat(forexcrow.rate).toFixed(2) + ' ' + forexcrow.rate_currency.code);

                $('#offerCounterModal').modal('show');
            })

            $(".status-button").click(function(e){
                e.preventDefault();
                var actionRoute =  "{{ setRoute('user.offer.status') }}";
                var target      = $(this).data('id');
                var title      = $(this).data('title');
                var message     = `Are you sure to <strong>`+title+`</strong>?`;
                openAlertModal(actionRoute,target,message,title,"POST");
            });
        });
    </script>
@endpush
