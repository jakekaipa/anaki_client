@extends('admin.layouts.master')

@push('css')

@endpush

@section('page-title')
    @include('admin.components.page-title',['title' => __($page_title)])
@endsection

@section('breadcrumb')
    @include('admin.components.breadcrumb',['breadcrumbs' => [
        [
            'name'  => __("Dashboard"),
            'url'   => setRoute("admin.dashboard"),
        ]
    ], 'active' => __("Dashboard")])
@endsection
@section('content')
    <div class="dashboard-area">
        <div class="dashboard-item-area">
            <div class="row">
                <!-- <div class="col-xxxl-4 col-xxl-3 col-xl-3 col-lg-6 col-md-6 col-sm-12 mb-15">
                    <div class="dashbord-item">
                        <div class="dashboard-content">
                            <div class="left">
                                <h6 class="title">{{ __('Add Money Balance') }}</h6>
                                <div class="user-info">
                                    <h2 class="user-count">{{ get_default_currency_symbol() }} {{ get_amount($data['add_money_balance']) }}</h2>
                                </div>
                                <div class="user-badge">
                                    <span class="badge badge--info">{{ __('This Month') }} {{ formatNumberInKNotation($data['today_add_money']) }}</span>
                                    <span class="badge badge--warning">{{ __('Last Month') }} {{ formatNumberInKNotation($data['last_month_add_money']) }}</span>
                                </div>
                            </div>
                            <div class="right">
                                <div class="chart" id="chart7" data-percent="{{ $data['add_money_percent'] }}"><span>{{ round($data['add_money_percent']) }}%</span></div>
                            </div>
                        </div>
                    </div>
                </div> -->
                <!-- <div class="col-xxxl-4 col-xxl-3 col-xl-3 col-lg-6 col-md-6 col-sm-12 mb-15">
                    <div class="dashbord-item">
                        <div class="dashboard-content">
                            <div class="left">
                                <h6 class="title">{{ __('All Time Trade') }}</h6>
                                <div class="user-info">
                                    <h2 class="user-count">{{ get_default_currency_symbol() }} {{ get_amount($data['trade_balance']) }}</h2>
                                </div>
                                <div class="user-badge">
                                    <span class="badge badge--info">{{ __('This Month') }} {{ formatNumberInKNotation($data['today_trade']) }}</span>
                                    <span class="badge badge--warning">{{ __('Last Month') }} {{ formatNumberInKNotation($data['last_month_trade']) }}</span>
                                </div>
                            </div>
                            <div class="right">
                                <div class="chart" id="chart9" data-percent="{{  $data['trade_percent']  }}"><span>{{  round($data['trade_percent'])  }}%</span></div>
                            </div>
                        </div>
                    </div>
                </div> -->
                <!-- <div class="col-xxxl-4 col-xxl-3 col-xl-3 col-lg-6 col-md-6 col-sm-12 mb-15">
                    <div class="dashbord-item">
                        <div class="dashboard-content">
                            <div class="left">
                                <h6 class="title">{{ __('All Time Marketplace') }}</h6>
                                <div class="user-info">
                                    <h2 class="user-count">{{ get_default_currency_symbol() }} {{ get_amount($data['marketplace_balance']) }}</h2>
                                </div>
                                <div class="user-badge">
                                    <span class="badge badge--warning">{{ __('Today') }} {{ formatNumberInKNotation($data['today_marketplace']) }}</span>
                                    <span class="badge badge--success">{{ __('Last Month') }} {{ formatNumberInKNotation($data['last_month_marketplace']) }}</span>
                                </div>
                            </div>
                            <div class="right">
                                <div class="chart" id="chart10" data-percent="{{  $data['marketplace_percent']  }}"><span>{{  round($data['marketplace_percent'])  }}%</span></div>
                            </div>
                        </div>
                    </div>
                </div> -->
                <div class="col-xxxl-4 col-xxl-3 col-xl-3 col-lg-6 col-md-6 col-sm-12 mb-15">
                    <div class="dashbord-item">
                        <div class="dashboard-content">
                            <div class="left">
                                <h6 class="title">{{ __('Total Users') }}</h6>
                                <div class="user-info">
                                    <h2 class="user-count">{{ formatNumberInKNotation($data['total_user']) }}</h2>
                                </div>
                                <div class="user-badge">
                                    <span class="badge badge--info">{{ __('Active') }} {{ $data['active_user'] }}</span>
                                    <span class="badge badge--warning">{{ __('Unverified') }} {{ $data['unverified_user'] }}</span>
                                </div>
                            </div>
                            <div class="right">
                                <div class="chart" id="chart11" data-percent="{{ $data['user_percent'] }}"><span>{{ round($data['user_percent']) }}%</span></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xxxl-4 col-xxl-3 col-xl-3 col-lg-6 col-md-6 col-sm-12 mb-15">
                <div class="dashbord-item">
                    <div class="dashboard-content">
                        <div class="left">
                            <h6 class="title">{{ __('Master Wallet Balance') }}</h6>
                            <div class="user-info">
                                <h2 class="user-count">$ {{ master_wallet_balance()['result']['usdt'] }}</h2>
                                <h2 class="user-count">TRX {{ master_wallet_balance()['result']['trx'] }}</h2>
                            </div>
                            <!-- <div class="user-badge">
                                <span class="badge badge--info">{{ __('This Month') }} {{ formatNumberInKNotation($data['today_add_money']) }}</span>
                                <span class="badge badge--warning">{{ __('Last Month') }} {{ formatNumberInKNotation($data['last_month_add_money']) }}</span>
                            </div> -->
                        </div>
                        <!-- <div class="right">
                            <div class="chart" id="chart7" data-percent="{{ $data['add_money_percent'] }}"><span>{{ round($data['add_money_percent']) }}%</span></div>
                        </div> -->
                    </div>
                </div>
            </div>
                <!-- <div class="col-xxxl-4 col-xxl-3 col-xl-3 col-lg-6 col-md-6 col-sm-12 mb-15">
                    <div class="dashbord-item">
                        <div class="dashboard-content">
                            <div class="left">
                                <h6 class="title">{{ __('Subscriber') }}</h6>
                                <div class="user-info">
                                    <h2 class="user-count">{{ formatNumberInKNotation($data['total_subscriber']) }}</h2>
                                </div>
                                <div class="user-badge">
                                    <span class="badge badge--info">{{ __('This Month') }} {{ formatNumberInKNotation($data['today_subscriber']) }}</span>
                                    <span class="badge badge--warning">{{ __('Last Month') }} {{ formatNumberInKNotation($data['last_month_subscriber']) }}</span>
                                </div>
                            </div>
                            <div class="right">
                                <div class="chart" id="chart12" data-percent="{{ $data['subscriber_percent'] }}"><span>{{ round($data['subscriber_percent']) }}%</span></div>
                            </div>
                        </div>
                    </div>
                </div> -->
                <!-- <div class="col-xxxl-4 col-xxl-3 col-xl-3 col-lg-6 col-md-6 col-sm-12 mb-15">
                    <div class="dashbord-item">
                        <div class="dashboard-content">
                            <div class="left">
                                <h6 class="title">{{ __('Pending Add Money') }}</h6>
                                <div class="user-info">
                                    <h2 class="user-count">{{ get_default_currency_symbol() }} {{ get_amount($data['pending_add_money_balance']) }}</h2>
                                </div>
                                <div class="user-badge">
                                    <span class="badge badge--info">{{ __('This Month') }} {{ formatNumberInKNotation($data['today_pending_add_money']) }}</span>
                                    <span class="badge badge--warning">{{ __('Last Month') }} {{ formatNumberInKNotation($data['last_month_pending_add_money']) }}</span>
                                </div>
                            </div>
                            <div class="right">
                                <div class="chart" id="chart13" data-percent="{{ $data['pending_add_money_percent'] }}"><span>{{ round($data['pending_add_money_percent']) }}%</span></div>
                            </div>
                        </div>
                    </div>
                </div> -->
            </div>
        </div>
    </div>
    <div class="chart-area mt-15">
        <div class="row mb-15-none">
            <!-- <div class="col-xxl-6 col-xl-6 col-lg-6 mb-15">
                <div class="chart-wrapper">
                    <div class="chart-area-header">
                        <h5 class="title">
                            {{ __('Monthly Add Money Chart') }}
                        </h5>
                        <a href="" class="btn--base--sm modal-btn"> {{ __('View') }}</a>
                    </div>
                    <div class="chart-container">
                        <div id="chart1" data-chart_one_data="{{ json_encode($data['chart_one_data']) }}" data-month_day="{{ json_encode($data['month_day']) }}" class="sales-chart"></div>
                    </div>
                </div>
            </div> -->
            <!-- <div class="col-xxl-6 col-xl-6 col-lg-6 mb-15">
                <div class="chart-wrapper">
                    <div class="chart-area-header">
                        <h5 class="title">{{ __('Monthly Trade Chart') }}</h5>
                        <a href="" class="btn--base--sm modal-btn">{{ __('View') }}</a>
                    </div>
                    <div class="chart-container">
                        <div id="chart2" data-chart_two_data="{{ json_encode($data['chart_two_data']) }}" class="revenue-chart"></div>
                    </div>
                </div>
            </div> -->
            <!-- <div class="col-xxl-12 col-xl-12 col-lg-12 mb-15">
                <div class="chart-wrapper">
                    <div class="chart-area-header">
                        <h5 class="title">{{ __('Trade And Marketplace Analytics') }}</h5>
                        <div>
                            <a href="{{ setRoute('admin.trade.index') }}" class="btn--base--sm modal-btn"> {{ __('View Trade') }}</a>
                            <a href="{{ setRoute('admin.marketplace.index') }}" class="btn--base--sm modal-btn"> {{ __('View Marketplace') }}</a>
                        </div>
                    </div>
                    <div class="chart-container">
                        <div id="chart3"  data-chart_three_data="{{ json_encode($data['chart_three_data']) }}" class="order-chart"></div>
                    </div>
                </div>
            </div> -->
            <div class="col-xxxl-6 col-xxl-3 col-xl-6 col-lg-6 mb-15">
                <div class="chart-wrapper">
                    <div class="chart-area-header">
                        <h5 class="title">{{ __('User Analytics') }}</h5>
                    </div>
                    <div class="chart-container">
                        <div id="chart4" data-chart_four_data="{{ json_encode($data['chart_four_data']) }}" class="balance-chart"></div>
                    </div>
                    <div class="chart-area-footer">
                        <div class="chart-btn">
                            <a href="{{ setRoute('admin.users.index') }}" class="btn--base w-100">{{ __('View User') }}</a>
                        </div>
                    </div>
                </div>
            </div>
            <!-- <div class="col-xxxl-6 col-xxl-6 col-xl-6 col-lg-6 mb-15">
                <div class="chart-wrapper">
                    <div class="chart-area-header">
                        <h5 class="title">{{ __('Trade Growth') }}</h5>
                    </div>
                    <div class="chart-container">
                        <div id="chart5" data-chart_five_data="{{ json_encode($data['chart_five_data']) }}" class="growth-chart"></div>
                    </div>
                    <div class="chart-area-footer">
                        <div class="chart-btn">
                            <a href="" class="btn--base w-100">{{ __('View Donation') }}</a>
                        </div>
                    </div>
                </div>
            </div> -->
        </div>
    </div>
    <!-- <div class="table-area mt-15">
        <div class="table-wrapper">
            <div class="table-header">
                <h5 class="title">{{ __('Latest Marketplace Transactions') }}</h5>
                <a href="{{ setRoute('admin.marketplace.index') }}" class="btn--base--sm modal-btn"> {{ __('View All') }}</a>
            </div>
            <div class="table-responsive">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>{{ __("TRX") }}</th>
                            <th>{{ __("Email") }}</th>
                            <th>{{ __("Amount") }}</th>
                            <th>{{ __("Will Gate") }}</th>
                            <th>{{ __("Status") }}</th>
                            <th>{{ __("Time") }}</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($data['transactions'] ?? []  as $key => $item)
                            <tr>
                                <tr>
                                    <td>{{ $item->trx_id }}</td>
                                    <td>{{ $item->user->email ?? 'N/A' }}</td>
                                    <td>{{ getDynamicAmount($item->request_amount, get_default_currency_code()) }}</td>
                                    <td><span class="text--info">{{ getDynamicAmount($item->forexcrow->amount, $item->forexcrow->saleCurrency->code) }}</span></td>
                                    <td>
                                        <span class="{{ $item->stringMarketplaceStatus->class }}">{{ $item->stringMarketplaceStatus->value }}</span>
                                    </td>
                                    <td>{{ dateFormat('d M y h:i:s A', $item->created_at) }}</td>
                                <td>
                                    @if ($item->status == 0)
                                        <button type="button" class="btn btn--base bg--success"><i
                                                class="las la-check-circle"></i></button>
                                        <button type="button" class="btn btn--base bg--danger"><i
                                                class="las la-times-circle"></i></button>
                                        <a href="add-logs-edit.html" class="btn btn--base"><i class="las la-expand"></i></a>
                                    @endif
                                </td>
                                <td>
                                    @include('admin.components.link.info-default',[
                                        'href'          => setRoute('admin.marketplace.details', $item->id),
                                        'permission'    => "admin.add.money.details",
                                    ])
                                </td>
                            </tr>
                        @empty
                            @include('admin.components.alerts.empty',['colspan' => 8])
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ get_paginate($data['transactions']) }}
        </div>
    </div> -->
@endsection

@push('script')
    <script>
        var chart1 = $('#chart1');
        var chart_one_data = chart1.data('chart_one_data');
        var month_day = chart1.data('month_day');
        // apex-chart
        var options = {
          series: [{
          name: 'Pending',
          color: "#5A5278",
          data: chart_one_data.pending_data
        }, {
          name: 'Completed',
          color: "#6F6593",
          data: chart_one_data.success_data
        }, {
          name: 'Canceled',
          color: "#8075AA",
          data: chart_one_data.canceled_data
        }, {
          name: 'Hold',
          color: "#A192D9",
          data: chart_one_data.hold_data
        }],
          chart: {
          type: 'bar',
          height: 350,
          stacked: true,
          toolbar: {
            show: false
          },
          zoom: {
            enabled: true
          }
        },
        responsive: [{
          breakpoint: 480,
          options: {
            legend: {
              position: 'bottom',
              offsetX: -10,
              offsetY: 0
            }
          }
        }],
        plotOptions: {
          bar: {
            horizontal: false,
            borderRadius: 10
          },
        },
        xaxis: {
          type: 'datetime',
          categories: month_day,
        },
        legend: {
          position: 'bottom',
          offsetX: 40
        },
        fill: {
          opacity: 1
        }
        };

        var chart = new ApexCharts(document.querySelector("#chart1"), options);
        chart.render();

        var chart2 = $('#chart2');
        var chart_two_data = chart2.data('chart_two_data');
        var options = {
          series: [{
          name: 'Ongoing',
          color: "#5A5278",
          data: chart_two_data.ongoing_data
        }, {
          name: 'Completed',
          color: "#6F6593",
          data: chart_two_data.complete_data
        }, {
          name: 'Closed',
          color: "#8075AA",
          data: chart_two_data.closed_data
        }],
          chart: {
          type: 'bar',
          height: 350,
          stacked: true,
          toolbar: {
            show: false
          },
          zoom: {
            enabled: true
          }
        },
        responsive: [{
          breakpoint: 480,
          options: {
            legend: {
              position: 'bottom',
              offsetX: -10,
              offsetY: 0
            }
          }
        }],
        plotOptions: {
          bar: {
            horizontal: true,
            borderRadius: 10
          },
        },
        yaxis: {
          type: 'datetime',
          labels: {
            format: 'dd/MMM',
          },
          categories: month_day,
        },
        legend: {
          position: 'bottom',
          offsetX: 40
        },
        fill: {
          opacity: 1
        }
        };

        var chart = new ApexCharts(document.querySelector("#chart2"), options);
        chart.render();

        var chart3 = $('#chart3');
        var chart_three_data = chart3.data('chart_three_data');

        var options = {
          series: [{
          name: 'Trade',
          color: "#5A5278",
          data: chart_three_data.trade_data
        },
        {
            name: 'All',
            color: "#8075AA",
            data: chart_three_data.all_data
          },{
          name: 'Marketplace',
          color: "#6F6593",
          data: chart_three_data.marketplace_data
        }],
          chart: {
          type: 'bar',
          toolbar: {
            show: false
          },
          height: 325
        },
        plotOptions: {
          bar: {
            horizontal: false,
            columnWidth: '55%',
            borderRadius: 5,
            endingShape: 'rounded'
          },
        },
        dataLabels: {
          enabled: false
        },
        stroke: {
          show: true,
          width: 2,
          colors: ['transparent']
        },
        xaxis: {
            type: 'datetime',
            categories: month_day,
        },
        fill: {
          opacity: 1
        },
        tooltip: {
          y: {
            formatter: function (val) {
              return val
            }
          }
        }
        };

        var chart = new ApexCharts(document.querySelector("#chart3"), options);
        chart.render();

        var chart4 = $('#chart4');
        var chart_four_data = chart4.data('chart_four_data');

        var options = {
          series: chart_four_data,
          chart: {
          width: 350,
          type: 'pie'
        },
        colors: ['#5A5278', '#6F6593', '#8075AA', '#A192D9'],
        labels: ['Active', 'Unverified', 'Banned', 'All'],
        responsive: [{
          breakpoint: 1480,
          options: {
            chart: {
              width: 280
            },
            legend: {
              position: 'bottom'
            }
          },
          breakpoint: 1199,
          options: {
            chart: {
              width: 380
            },
            legend: {
              position: 'bottom'
            }
          },
          breakpoint: 575,
          options: {
            chart: {
              width: 280
            },
            legend: {
              position: 'bottom'
            }
          }
        }],
        legend: {
          position: 'bottom'
        },
        };

        var chart = new ApexCharts(document.querySelector("#chart4"), options);
        chart.render();

        var chart5 = $('#chart5');
        var chart_five_data = chart5.data('chart_five_data');

        var options = {
          series: chart_five_data,
          chart: {
          width: 350,
          type: 'donut',
        },
        colors: ['#5A5278', '#6F6593', '#8075AA', '#A192D9'],
        labels: ['Today', '1 week', '1 month', '1 year'],
        legend: {
            position: 'bottom'
        },
        responsive: [{
          breakpoint: 1600,
          options: {
            chart: {
              width: 100,
            },
            legend: {
              position: 'bottom'
            }
          },
          breakpoint: 1199,
          options: {
            chart: {
              width: 380
            },
            legend: {
              position: 'bottom'
            }
          },
          breakpoint: 575,
          options: {
            chart: {
              width: 280
            },
            legend: {
              position: 'bottom'
            }
          }
        }]
        };

        var chart = new ApexCharts(document.querySelector("#chart5"), options);
        chart.render();
    </script>
@endpush
