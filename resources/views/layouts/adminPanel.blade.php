<!--
=========================================================
* Argon Dashboard - v1.2.0
=========================================================
* Product Page: https://www.creative-tim.com/product/argon-dashboard


* Copyright  Creative Tim (http://www.creative-tim.com)
* Coded by www.creative-tim.com



=========================================================
* The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
-->
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Start your development with a Dashboard for Bootstrap 4.">
    <meta name="author" content="Softradix">
    <title>SUTHAR ENTERPRISES</title>
    <!-- Favicon -->
    <link rel="icon" href="{{url('/assets/img/brand/favicon.png')}}" type="image/png">
    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700">
    <!-- Icons -->
    <link rel="stylesheet" href="{{url('/assets/vendor/nucleo/css/nucleo.css')}}" type="text/css">
    <link rel="stylesheet" href="{{url('/assets/vendor/@fortawesome/fontawesome-free/css/all.min.css')}}" type="text/css">
    <!-- Page plugins -->
    <!-- Argon CSS -->
    <link rel="stylesheet" href="{{url('/assets/css/argon.css?v=1.2.0')}}" type="text/css">
    <link rel="stylesheet" href="{{url('/assets/css/style.css')}}" type="text/css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.min.css">

    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    @yield('head')
</head>

<body>
    <!-- Sidenav -->
    <nav class="sidenav navbar navbar-vertical  fixed-left  navbar-expand-xs navbar-light bg-white" id="sidenav-main">
        <div class="scrollbar-inner">
            <!-- Brand -->
            <div class="sidenav-header  align-items-center">
                <a class="navbar-brand" href="javascript:void(0)">
                    <img src="{{url('/assets/img/brand/blue.png')}}" class="navbar-brand-img" alt="...">
                </a>
            </div>
            <div class="navbar-inner">
                <!-- Collapse -->
                <div class="collapse navbar-collapse" id="sidenav-collapse-main">
                    <!-- Nav items -->
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->segment(1) == '' ? 'active' : '' }}" href="{{ url('/') }}">
                                <i class="ni ni-tv-2 text-primary"></i>
                                <span class="nav-link-text">Dashboard</span>
                            </a>
                        </li>
                        @if(\Auth::user()->role == 0)
                        <li class="nav-item">
                            <a class="nav-link {{ request()->segment(1) == 'stores' ? 'active' : '' }}" href="{{ route('listStores') }}">
                                <i class="fas fa-store text-primary"></i>
                                <span class="nav-link-text">Stores</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->segment(1) == 'managers' ? 'active' : '' }}" href="{{ route('listUsers') }}">
                                <i class="fas fa-users text-primary"></i>
                                <span class="nav-link-text">Managers</span>
                            </a>
                        </li>
                        @endif
                        <li class="nav-item">
                            <a class="nav-link {{ request()->segment(1) == 'gifts' ? 'active' : '' }}" href="{{ route('listGifts') }}">
                                <i class="fas fa-gift text-primary"></i>
                                <span class="nav-link-text">Gifts</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->segment(1) == 'points_history' ? 'active' : '' }}" href="{{ route('pointHistory') }}">
                                <i class="ni ni-planet text-primary" aria-hidden="true"></i>
                                <span class="nav-link-text">Points History</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->segment(1) == 'users' ? 'active' : '' }}" href="{{ route('listContractors') }}">
                                <i class="fas fa-id-card-alt text-primary"></i>
                                <span class="nav-link-text">Users</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->segment(1) == 'references' ? 'active' : '' }}" href="{{ route('listReferences') }}">
                                <i class="fa fa-link text-primary" aria-hidden="true"></i>
                                <span class="nav-link-text">References</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->segment(1) == 'redeem_requests' ? 'active' : '' }}" href="{{ route('listRedeenRequests') }}">
                                <i class="ni ni-money-coins text-primary" aria-hidden="true"></i>
                                <span class="nav-link-text">Redeem Requests</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>
    <!-- Main content -->
    <div class="main-content" id="panel">
        <!-- Topnav -->
        @include('layouts.sections.admin-top-nav')
        @include('layouts.sections.admin-header')
        @yield('dashboard_contents')
        <!-- Page content -->

        <div class="container-fluid mt--6">
            @if(request()->segment(count(request()->segments())) != "")
            @if(request()->segment(count(request()->segments())) != "dashboard")
            <div class="custom-panel">
                <div class="row">
                    @yield('contents')
                </div>
            </div>
            @endif
            @endif
            <!-- Footer -->
            <footer class="footer pt-0">
                <div class="row align-items-center justify-content-lg-between">
                    <div class="col-lg-12">
                        <div class="copyright text-center text-muted">
                            &copy; {{date('Y')}} <a href="https://manvikdoorframes.com/" class="font-weight-bold ml-1 text-muted" target="_blank">Suthar Enterprises</a>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>
    <div class="loader-block hide">
        <img src="{{ url('assets/img/loader.gif') }}" alt="">
    </div>

    <!-- Argon Scripts -->
    <!-- Core -->
    <script src="{{url('/assets/vendor/jquery/dist/jquery.min.js')}}"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script src="{{url('/assets/js/app.js')}}"></script>
    @yield('scripts')
    <script src="{{url('/assets/vendor/bootstrap/dist/js/bootstrap.bundle.min.js')}}"></script>
    <script src="{{url('/assets/vendor/js-cookie/js.cookie.js')}}"></script>
    <script src="{{url('/assets/vendor/jquery.scrollbar/jquery.scrollbar.min.js')}}"></script>
    <script src="{{url('/assets/vendor/jquery-scroll-lock/dist/jquery-scrollLock.min.js')}}"></script>
    <!-- Optional JS -->
    <script src="{{url('/assets/vendor/chart.js/dist/Chart.min.js')}}"></script>
    <script src="{{url('/assets/vendor/chart.js/dist/Chart.extension.js')}}"></script>
    <!-- Argon JS -->
    <script src="{{url('/assets/js/argon.js?v=1.2.0')}}"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.min.js"></script>
    <script>
        @if(session('status'))
        var type = "{{ Session::get('status') }}";
        switch (type) {
            case 'success':
                toastr.success("@if(!is_null(Session::get('block_no'))) Input {{Session::get('block_no')}}: @endif{{ Session::get('message') }}");
                break;

            case 'danger':
                toastr.error("@if(!is_null(Session::get('block_no'))) Input {{Session::get('block_no')}}: @endif{{ Session::get('message') }}");
                break;
        }
        @endif
    </script>
</body>

</html>
