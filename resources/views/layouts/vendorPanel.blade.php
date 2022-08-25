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
  <title>LM - Product</title>
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
          @php  $dash_active = "";  @endphp
          @if(request()->segment(count(request()->segments())) == "")
            @php $dash_active = "active"; @endphp
          @endif
          <ul class="navbar-nav">
            <li class="nav-item">
              <a class="nav-link {{ $dash_active }}" href="{{ url('/') }}">
                <i class="ni ni-tv-2 text-primary"></i>
                <span class="nav-link-text">Dashboard</span>
              </a>
            </li>
            @if(\Auth::user()->menus)
            @forelse(\Auth::user()->menus as $menu)
            <li class="nav-item">
              @php
              $menu_active = "";
              if(isset(request()->segments()[0])) {
                if(request()->segments()[0] == $menu['permission_name']){
                  $menu_active = "active";
                }
              }

              @endphp
              <a class="nav-link {{$menu_active}}" href="{{ url($menu['permission_name']) }}">
                {!! $menu['icon'] ?? '<i class="ni ni-planet text-orange"></i>' !!}
                <span class="nav-link-text">{{$menu['menu_name']}}</span>
              </a>
            </li>
            @empty
            @endforelse
            @endif
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
            &copy; {{date('Y')}} <a href="https://www.softradix.com/" class="font-weight-bold ml-1 text-muted" target="_blank">Softradix Technologies Pvt. Ltd.</a>
          </div>
        </div>
      </div>
    </footer>
  </div>
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
</body>

</html>