@extends('layouts.adminPanel')
@section('head')
<style>
.col-cst {
    padding-right: 10px !important;
    padding-left: 10px !important;
}
</style>
@endsection
@section('dashboard_contents')
<div class="header pb-6 customHeight">
<div class="container-fluid bg-primary">
<div class="header-body">
    <!-- Card stats -->
    <div class="row">
        <div class="col-xl-3 col-md-6">
            <a href="{{ route('listContractors') }}">
                <div class="card card-stats">
                <!-- Card body -->
                    <div class="card-body">
                        <div class="row">
                            <div class="col" style="padding-left: 5px; padding-right: 5px;">
                                <h5 class="card-title text-uppercase text-muted mb-0">App Users</h5>
                                <span class="h2 font-weight-bold mb-0">{{ $contractors }}</span>
                            </div>
                            <div class="col-auto">
                                <div class="icon icon-shape bg-gradient-red text-white rounded-circle shadow">
                                <i class="fas fa-id-card-alt"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        @if(\Auth::user()->role == 0)
        <div class="col-xl-3 col-md-6">
            <a href="{{ route('listUsers') }}">
                <div class="card card-stats">
                <!-- Card body -->
                    <div class="card-body">
                        <div class="row">
                        <div class="col" style="padding-left: 5px; padding-right: 5px;">
                            <h5 class="card-title text-uppercase text-muted mb-0">Managers</h5>
                            <span class="h2 font-weight-bold mb-0">{{ $managers }}</span>
                        </div>
                        <div class="col-auto">
                            <div class="icon icon-shape bg-gradient-orange text-white rounded-circle shadow">
                            <i class="fas fa-users"></i>
                            </div>
                        </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-xl-3 col-md-6">
            <a href="{{ route('listStores') }}">
                <div class="card card-stats">
                <!-- Card body -->
                    <div class="card-body">
                        <div class="row">
                        <div class="col" style="padding-left: 5px; padding-right: 5px;">
                            <h5 class="card-title text-uppercase text-muted mb-0">Stores</h5>
                            <span class="h2 font-weight-bold mb-0">{{ $stores }}</span>
                        </div>
                        <div class="col-auto">
                            <div class="icon icon-shape bg-gradient-green text-white rounded-circle shadow">
                            <i class="fas fa-store"></i>
                            </div>
                        </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-xl-3 col-md-6">
            <a href="{{ route('listGifts') }}">
                <div class="card card-stats">
                <!-- Card body -->
                    <div class="card-body">
                        <div class="row">
                        <div class="col" style="padding-left: 5px; padding-right: 5px;">
                            <h5 class="card-title text-uppercase text-muted mb-0">Gifts</h5>
                            <span class="h2 font-weight-bold mb-0">{{ $gifts }}</span>
                        </div>
                        <div class="col-auto">
                            <div class="icon icon-shape bg-gradient-info text-white rounded-circle shadow">
                            <i class="fas fa-gift"></i>
                            </div>
                        </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        @endif
        <div class="col-xl-3 col-md-6">
            <a href="{{ route('listReferences') }}">
                <div class="card card-stats">
                <!-- Card body -->
                    <div class="card-body">
                        <div class="row">
                            <div class="col" style="padding-left: 5px; padding-right: 5px;">
                                <h5 class="card-title text-uppercase text-muted mb-0">References</h5>
                                <span class="h2 font-weight-bold mb-0">{{ $references }}</span>
                            </div>
                            <div class="col-auto">
                                <div class="icon icon-shape bg-gradient-pink text-white rounded-circle shadow">
                                <i class="fa fa-link"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-xl-3 col-md-6">
            <a href="{{ route('listRedeenRequests') }}">
                <div class="card card-stats">
                <!-- Card body -->
                    <div class="card-body">
                        <div class="row">
                            <div class="col" style="padding-left: 5px; padding-right: 5px;">
                                <h5 class="card-title text-uppercase text-muted mb-0">Redeem Requests</h5>
                                <span class="h2 font-weight-bold mb-0">{{ $redeemRequests }}</span>
                            </div>
                            <div class="col-auto">
                                <div class="icon icon-shape bg-gradient-purple text-white rounded-circle shadow">
                                <i class="ni ni-money-coins"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>
</div>
</div>
@endsection