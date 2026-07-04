<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>@yield('title', 'Inventory F&B')</title>

    <!-- Custom fonts for this template-->
    <link href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@200;300;400;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="{{ asset('css/sb-admin-2.min.css') }}" rel="stylesheet">
    <style>
        /* Responsive CSS Additions */
        /* Horizontal scroll and swipe optimizations for tables */
        .table-responsive {
            -webkit-overflow-scrolling: touch;
            overflow-x: auto;
        }

        /* Horizontal scrolling for navigation tabs/pills on mobile instead of wrapping */
        @media (max-width: 768px) {
            .nav-pills, .nav-tabs {
                flex-wrap: nowrap !important;
                overflow-x: auto !important;
                -webkit-overflow-scrolling: touch;
                padding-bottom: 8px;
            }
            .nav-pills .nav-item, .nav-tabs .nav-item {
                white-space: nowrap;
            }
            
            /* Spacing for stacked columns on mobile */
            .row > [class*="col-"] {
                margin-bottom: 12px;
            }
            .row > [class*="col-"]:last-child {
                margin-bottom: 0;
            }
            
            /* Responsive margins/padding adjustments */
            .card-body {
                padding: 16px !important;
            }
            .table td, .table th {
                padding: 12px !important;
            }
        }

        /* Toast container responsiveness */
        @media (max-width: 576px) {
            .toast-container {
                top: 12px !important;
                right: 12px !important;
                left: 12px !important;
                width: calc(100% - 24px);
            }
            .custom-toast {
                min-width: 100% !important;
                width: 100% !important;
            }
        }

        /* Responsive button layouts in tables */
        @media (max-width: 991px) {
            .btn-group, .btn-group-vertical {
                display: inline-flex !important;
                flex-wrap: wrap;
                gap: 4px;
            }
            .btn-group > .btn, .btn-group-vertical > .btn {
                flex: 1 1 auto;
                border-radius: 6px !important;
                margin-right: 0 !important;
            }
        }

        body, h1, h2, h3, h4, h5, h6,
        .h1, .h2, .h3, .h4, .h5, .h6,
        p, span, a, button, input, select, textarea, label, td, th, div {
            font-family: 'Inter', sans-serif !important;
        }
        body {
            background-color: #ffffff !important;
        }
        #content-wrapper {
            background-color: #ffffff !important;
        }
        /* Custom Modern Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #ffffff;
        }
        ::-webkit-scrollbar-thumb {
            background: #D0E7E6;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #95CCDD;
        }
        /* Global Premium CSS Tokens */
        .card {
            border: 1px solid #D0E7E6 !important;
            border-radius: 16px !important;
            box-shadow: 0 4px 12px rgba(41, 54, 129, 0.03) !important;
            transition: all 0.25s ease-in-out;
            background-color: #ffffff !important;
        }
        .card-header {
            background-color: #ffffff !important;
            border-bottom: 1px solid #D0E7E6 !important;
            padding: 18px 24px !important;
            border-top-left-radius: 16px !important;
            border-top-right-radius: 16px !important;
        }
        .card-body {
            padding: 24px !important;
            background-color: #ffffff !important;
        }
        .table {
            border-collapse: separate !important;
            border-spacing: 0 4px !important;
        }
        .table th {
            font-weight: 700 !important;
            text-transform: uppercase;
            font-size: 11px !important;
            letter-spacing: 0.8px;
            color: #293681 !important;
            border-bottom: 2px solid #95CCDD !important;
            padding: 14px 16px !important;
        }
        .table td {
            vertical-align: middle !important;
            padding: 16px !important;
            background: #ffffff;
            border-top: 1px solid #D0E7E6 !important;
            border-bottom: 1px solid #D0E7E6 !important;
            color: #293681;
        }
        .table tr:hover td {
            background: #D0E7E6;
            color: #293681 !important;
        }
        .table-responsive {
            border-radius: 12px;
            overflow: hidden;
        }
        /* Input & Controls */
        .form-control, .form-select, select {
            border-radius: 10px !important;
            border: 1px solid #95CCDD !important;
            padding: 10px 16px !important;
            height: auto !important;
            font-size: 14px !important;
            transition: all 0.2s;
            background-color: #ffffff !important;
            color: #293681 !important;
        }
        .form-control:focus, select:focus {
            border-color: #4274D9 !important;
            box-shadow: 0 0 0 3px rgba(66, 116, 217, 0.15) !important;
        }
        /* Premium Buttons */
        .btn {
            border-radius: 10px !important;
            padding: 10px 20px !important;
            font-weight: 600 !important;
            font-size: 13px !important;
            letter-spacing: 0.3px;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .btn-primary {
            background-color: #4274D9 !important;
            border-color: #4274D9 !important;
            color: #ffffff !important;
            box-shadow: 0 4px 6px -1px rgba(66, 116, 217, 0.2) !important;
        }
        .btn-primary:hover {
            background-color: #293681 !important;
            border-color: #293681 !important;
            transform: translateY(-1px);
            box-shadow: 0 10px 15px -3px rgba(41, 54, 129, 0.3) !important;
        }
        /* Accent colors keep their standard colors (Edit: warning, Delete: danger, etc) */
        .btn-warning {
            background-color: #ffc107 !important;
            border-color: #ffc107 !important;
            color: #212529 !important;
        }
        .btn-danger {
            background-color: #dc3545 !important;
            border-color: #dc3545 !important;
            color: #ffffff !important;
        }
        .btn-success {
            background-color: #28a745 !important;
            border-color: #28a745 !important;
            color: #ffffff !important;
        }
        .btn-info {
            background-color: #17a2b8 !important;
            border-color: #17a2b8 !important;
            color: #ffffff !important;
        }
        .btn-secondary {
            background-color: #6c757d !important;
            border-color: #6c757d !important;
            color: #ffffff !important;
        }
        /* Badges */
        .badge {
            border-radius: 30px !important;
            padding: 6px 14px !important;
            font-weight: 700 !important;
            font-size: 11px !important;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        .badge-primary {
            background-color: rgba(66, 116, 217, 0.1) !important;
            color: #4274D9 !important;
        }
        .badge-success {
            background-color: rgba(40, 167, 69, 0.1) !important;
            color: #28a745 !important;
        }
        .badge-warning {
            background-color: rgba(255, 193, 7, 0.1) !important;
            color: #ffc107 !important;
        }
        .badge-danger {
            background-color: rgba(220, 53, 69, 0.1) !important;
            color: #dc3545 !important;
        }
        .badge-secondary {
            background-color: rgba(108, 117, 125, 0.1) !important;
            color: #6c757d !important;
        }
        .badge-info {
            background-color: rgba(23, 162, 184, 0.1) !important;
            color: #17a2b8 !important;
        }
        /* Custom alert */
        .alert {
            border-radius: 12px !important;
            border: 1px solid #D0E7E6 !important;
            padding: 16px 24px !important;
        }
        /* Title styling */
        .page-header-title {
            font-weight: 800;
            letter-spacing: -0.5px;
            color: #293681;
        }
        /* Sidebar styling override */
        .sidebar {
            background-color: #293681 !important;
            background-image: none !important;
        }
        /* Topbar styling */
        .topbar {
            background-color: #ffffff !important;
            border-bottom: 1px solid #D0E7E6;
        }
    </style>
    @stack('styles')

</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        @include('layouts.partials.sidebar')
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                @include('layouts.partials.topbar')
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">
                    @yield('content')
                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            @include('layouts.partials.footer')
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <a class="btn btn-primary" href="#">Logout</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

    <!-- Core plugin JavaScript-->
    <script src="{{ asset('vendor/jquery-easing/jquery.easing.min.js') }}"></script>

    <!-- Custom scripts for all pages-->
    <script src="{{ asset('js/sb-admin-2.min.js') }}"></script>

    @include('layouts.partials.toast')

    @stack('scripts')

</body>

</html>
