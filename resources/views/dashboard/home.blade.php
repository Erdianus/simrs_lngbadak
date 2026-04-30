@extends('layouts.master')
@section('content')
    {{-- message --}}
    {!! Toastr::message() !!}
    <div class="page-wrapper">
        <div class="content container-fluid">
            <div class="page-header">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="page-sub-header">
                            <h3 class="page-title">Welcome {{ Session::get('name') }}!</h3>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                                <li class="breadcrumb-item active">{{ Session::get('name') }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-3 col-sm-6 col-12 d-flex">
                    <div class="card bg-comman w-100">
                        <div class="card-body">
                            <div class="db-widgets d-flex justify-content-between align-items-center">
                                <div class="db-info">
                                    <h6>SP3</h6>
                                    <h3>{{ $sp3s->count() }}</h3>
                                </div>
                                <div class="db-icon">
                                    <img src="{{ URL::to('assets/img/icons/dash-icon-01.svg') }}" alt="Dashboard Icon">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6 col-12 d-flex">
                    <div class="card bg-comman w-100">
                        <div class="card-body">
                            <div class="db-widgets d-flex justify-content-between align-items-center">
                                <div class="db-info">
                                    <h6>Billing</h6>
                                    <h3>{{ $billings->count() }}</h3>
                                </div>
                                <div class="db-icon">
                                    <img src="{{ URL::to('assets/img/icons/dash-icon-02.svg') }}" alt="Dashboard Icon">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12 col-lg-6">

                    <div class="card card-chart">
                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col-6">
                                    <h5 class="card-title">SP3 Overview</h5>
                                </div>
                                <div class="col-6">
                                    <ul class="chart-list-out">
                                        {{-- <li><span class="circle-blue"></span>Teacher</li> --}}
                                        <li><span class="circle-green"></span>Sp3</li>
                                        <li class="star-menus"><a href="javascript:;"><i class="fas fa-ellipsis-v"></i></a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="chart-sp3"></div>
                        </div>
                    </div>

                </div>
                <div class="col-md-12 col-lg-6">

                    <div class="card card-chart">
                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col-6">
                                    <h5 class="card-title">Billing Overview</h5>
                                </div>
                                <div class="col-6">
                                    <ul class="chart-list-out">
                                        {{-- <li><span class="circle-blue"></span>Girls</li> --}}
                                        <li><span class="circle-green"></span>Billing</li>
                                        <li class="star-menus"><a href="javascript:;"><i class="fas fa-ellipsis-v"></i></a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="chart-billing"></div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        var sp3 = {
            chart: {
                type: 'bar',
                height: 350
            },
            series: [{
                name: 'SP3',
                data: @json($nilaiSp3) // [0, 0, 5, 10, ...] per bulan
            }, ],
            xaxis: {
                categories: @json($bulan) // ['January', 'February', ...]
            },
            colors: ['#0dcaf0', '#0d6efd'], // biru info & biru primary
            plotOptions: {
                bar: {
                    borderRadius: 4,
                    columnWidth: '60%',
                }
            },
            dataLabels: {
                enabled: false
            },
            legend: {
                position: 'top'
            }
        }
        var billing = {
            chart: {
                type: 'bar',
                height: 350
            },
            series: [{
                name: 'Billing',
                data: @json($nilaiBilling) // [0, 0, 5, 10, ...] per bulan
            }, ],
            xaxis: {
                categories: @json($bulan) // ['January', 'February', ...]
            },
            colors: ['#0dcaf0', '#0d6efd'], // biru info & biru primary
            plotOptions: {
                bar: {
                    borderRadius: 4,
                    columnWidth: '60%',
                }
            },
            dataLabels: {
                enabled: false
            },
            legend: {
                position: 'top'
            }
        }

        var chartSp3 = new ApexCharts(document.querySelector("#chart-sp3"), sp3);
        var chartBilling = new ApexCharts(document.querySelector("#chart-billing"), billing);
        chartSp3.render();
        chartBilling.render();
    </script>
@endsection
