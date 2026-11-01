<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1.0"
          name="viewport">
    <meta content="ie=edge"
          http-equiv="X-UA-Compatible">
    <title>SIAP AUM - {{ config('app.name') }}</title>
    <link href="{{ asset('assets/images/siap-aum/figma-raw-3.png') }}"
          rel="shortcut icon"
          type="image/x-icon">
    @vite(['resources/sass/app.scss'])
    <link href="{{ asset('assets/vendors/iconsax/style.css') }}"
          rel="stylesheet">

    {{-- datatables bootstrap --}}
    <link href="{{ asset('assets/vendors/datatables/dataTables.bootstrap5.min.css') }}"
          rel="stylesheet">
    {{-- datatables rowreorder --}}
    <link href="{{ asset('assets/vendors/datatables/rowReorder.dataTables.min.css') }}"
          rel="stylesheet">
    {{-- datatables button --}}
    <link href="{{ asset('assets/vendors/datatables/buttons.bootstrap5.min.css') }}"
          rel="stylesheet">
    {{-- datatables responsive --}}
    <link href="{{ asset('assets/vendors/datatables/responsive.dataTables.min.css') }}"
          rel="stylesheet">
    {{-- select 2 --}}
    <link href="{{ asset('assets/vendors/select2/select2.min.css') }}"
          rel="stylesheet">
    {{-- daterangepicker --}}
    <link href="{{ asset('assets/vendors/daterangepicker/daterangepicker.css') }}"
          rel="stylesheet">
    {{-- trixeditor --}}
    <link href="https://cdn.jsdelivr.net/npm/trix@2.1.5/dist/trix.min.css"
          rel="stylesheet">

    @stack('styles')
</head>

<body class="sa-dashboard-body">
    @include('components.page-loader')
    {{-- <script src="{{ asset('assets/js/initTheme.js') }}"></script> --}}
    <div id="app">
        <div id="sidebar">
            @include('layouts.dashboard.partials.sidebar')
        </div>
        <div class="layout-navbar navbar-fixed position-relative"
             id="main">
            @include('layouts.dashboard.partials.header')
            <div id="main-content">
                @yield('content')
            </div>
            @include('layouts.dashboard.partials.footer')
        </div>
    </div>

    {{-- <script src="{{ asset('assets/js/components/dark.js') }}"></script> --}}
    {{-- perfect scrollbar cdn --}}
    {{-- <script src="{{ asset('assets/js/perfect-scrollbar.min.js') }}"></script> --}}
    <!-- or, you may also directly use a CDN :-->
    {{-- <script src="{{ asset('assets/js/autonumeric.js') }}"></script> --}}
    {{-- jquery --}}
    <script src="{{ asset('assets/vendors/jquery/jquery-3.4.1.min.js') }}"></script>
    {{-- sweet alert 2 --}}
    <script src="{{ asset('assets/vendors/sweetalert2/sweetalert2@11.js') }}"></script>
    {{-- datatables --}}
    <script src="{{ asset('assets/vendors/datatables/dataTables.min.js') }}"></script>
    {{-- datatables bootstrap --}}
    <script src="{{ asset('assets/vendors/datatables/dataTables.bootstrap5.min.js') }}"></script>
    {{-- datatables responsive --}}
    <script src="{{ asset('assets/vendors/datatables/dataTables.responsive.min.js') }}"></script>
    {{-- datatables rowreorder --}}
    <script src="{{ asset('assets/vendors/datatables/dataTables.rowReorder.min.js') }}"></script>
    {{-- datatables button --}}
    <script src="{{ asset('assets/vendors/datatables/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/datatables/buttons.bootstrap5.min.js') }}"></script>
    {{-- select 2 --}}
    <script src="{{ asset('assets/vendors/select2/select2.min.js') }}"></script>
    {{-- moment --}}
    <script src="{{ asset('assets/vendors/moment/moment.min.js') }}"></script>
    {{-- dateragepicker --}}
    <script src="{{ asset('assets/vendors/daterangepicker/daterangepicker.min.js') }}"></script>
    {{-- flatpickr --}}
    <script src="{{ asset('assets/vendors/flatpickr/flatpickr.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/flatpickr/id.js') }}"></script>
    {{-- trix editor --}}
    <script src="https://cdn.jsdelivr.net/npm/trix@2.1.5/dist/trix.umd.min.js"></script>
    {{-- @yield('content') --}}
    {{-- Autonumeric --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/autonumeric/4.10.5/autoNumeric.min.js" integrity="sha512-EGJ6YGRXzV3b1ouNsqiw4bI8wxwd+/ZBN+cjxbm6q1vh3i3H19AJtHVaICXry109EVn4pLBGAwaVJLQhcazS2w==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    @stack('scripts')
    @vite(['resources/js/app.js', 'resources/js/dashboard/dashboard.js'])
    @include('components.toastr')
</body>

</html>
