<!doctype html>
<html lang="{{ app()->getLocale() }}" @if(app()->getLocale() == 'ar') dir="rtl"@endif>
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,  minimum-scale=0.8, maximum-scale = 0.8, user-scalable = no , shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <link rel="shortcut icon" href="{{ settings()->favicon_image }}" type="image/x-icon">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{static_asset('backend')}}/vendor/bootstrap-five/bootstrap.min.css">
    @if(app()->getLocale() == 'ar')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css"/>
    @endif
    {{-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css"> --}}
    <link href="{{static_asset('backend')}}/vendor/fonts/circular-std/style.css" rel="stylesheet">
    <link rel="stylesheet" href="{{static_asset('backend')}}/libs/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css" integrity="sha512-MV7K8+y+gLIBoVD59lQIYicR65iaqukzvf/nwasF0nqhPay5w/9lJmVM2hMDcnK1OnMGCdVK+iQrJ7lzPJQd1w==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link rel="stylesheet" href="{{static_asset('backend')}}/vendor/fonts/fontawesome/css/fontawesome-all.css">
    <link rel="stylesheet" href="{{static_asset('backend')}}/vendor/charts/chartist-bundle/chartist.css">
    <link rel="stylesheet" href="{{static_asset('backend')}}/vendor/charts/morris-bundle/morris.css">
    <link rel="stylesheet" href="{{static_asset('backend')}}/vendor/fonts/material-design-iconic-font/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="{{static_asset('backend')}}/vendor/charts/c3charts/c3.css">
    <link rel="stylesheet" href="{{static_asset('backend')}}/vendor/fonts/flag-icon-css/flag-icon.min.css">
    <link rel="stylesheet" href="{{static_asset('backend')}}/libs/css/datepicker.min.css">
    <link rel="stylesheet" href="{{static_asset('backend')}}/libs/css/custom.css">
    <link rel="stylesheet" href="{{static_asset('backend')}}/css/custom.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/6.5.1/css/flag-icons.min.css" /> 
    <link rel="stylesheet" href="{{ static_asset('backend/vendor') }}/toastr/toastr.min.css">
    
    <!-- Dynamic Theme Colors -->
    <style>
        :root {
            --bs-primary: {{ settings()->primary_color ?? '#d60b18' }};
            --bs-primary-rgb: {{ 
                settings()->primary_color ? 
                implode(',', sscanf(settings()->primary_color, "#%02x%02x%02x")) : 
                '214, 11, 24' 
            }};
            --bs-text-color: {{ settings()->text_color ?? '#212529' }};
            --primary-hover: color-mix(in srgb, var(--bs-primary) 85%, black);
            --primary-light: color-mix(in srgb, var(--bs-primary) 15%, transparent);
        }
        
        /* ===== PRIMARY BUTTONS ===== */
        .btn-primary {
            background-color: var(--bs-primary) !important;
            border-color: var(--bs-primary) !important;
            color: #ffffff !important;
        }
        
        .btn-primary:hover,
        .btn-primary:focus,
        .btn-primary:active,
        .btn-primary.active,
        .btn-primary.focus,
        .btn-primary:not(:disabled):not(.disabled):active,
        .btn-primary:not(:disabled):not(.disabled).active {
            background-color: var(--primary-hover) !important;
            border-color: var(--primary-hover) !important;
            color: #ffffff !important;
            box-shadow: 0 0 0 0.2rem rgba(var(--bs-primary-rgb), 0.25) !important;
            transform: translateY(-1px);
            transition: all 0.2s ease-in-out;
        }
        
        /* ===== OUTLINE BUTTONS ===== */
        .btn-outline-primary {
            color: var(--bs-primary) !important;
            border-color: var(--bs-primary) !important;
            background-color: transparent !important;
        }
        
        .btn-outline-primary:hover,
        .btn-outline-primary:focus,
        .btn-outline-primary:active,
        .btn-outline-primary.active,
        .btn-outline-primary:not(:disabled):not(.disabled):active,
        .btn-outline-primary:not(:disabled):not(.disabled).active {
            background-color: var(--bs-primary) !important;
            border-color: var(--bs-primary) !important;
            color: #ffffff !important;
            transform: translateY(-1px);
            transition: all 0.2s ease-in-out;
        }
        
        /* ===== ALL BUTTON TYPES ===== */
        button[type="submit"],
        input[type="submit"],
        .btn.primary,
        .button-primary,
        .submit-btn,
        .primary-btn {
            background-color: var(--bs-primary) !important;
            border-color: var(--bs-primary) !important;
            color: #ffffff !important;
        }
        
        button[type="submit"]:hover,
        input[type="submit"]:hover,
        .btn.primary:hover,
        .button-primary:hover,
        .submit-btn:hover,
        .primary-btn:hover {
            background-color: var(--primary-hover) !important;
            border-color: var(--primary-hover) !important;
            color: #ffffff !important;
            transform: translateY(-1px);
            transition: all 0.2s ease-in-out;
        }
        
        /* ===== LINKS AND TEXT ===== */
        .text-primary, 
        a.text-primary {
            color: var(--bs-primary) !important;
        }
        
        a.text-primary:hover,
        a.text-primary:focus,
        .link-primary:hover,
        .link-primary:focus {
            color: var(--primary-hover) !important;
            text-decoration: underline;
        }
        
        .link-primary {
            color: var(--bs-primary) !important;
        }
        
        /* ===== BACKGROUND AND BORDERS ===== */
        .bg-primary {
            background-color: var(--bs-primary) !important;
        }
        
        .border-primary {
            border-color: var(--bs-primary) !important;
        }
        
        /* ===== SIDEBAR NAVIGATION ===== */
        .dashboard-main-wrapper .sidebar .nav-link.active,
        .sidebar .nav-link.active,
        .nav-pills .nav-link.active {
            background-color: var(--bs-primary) !important;
            color: #ffffff !important;
        }
        
        .dashboard-main-wrapper .sidebar .nav-link:hover,
        .sidebar .nav-link:hover {
            background-color: var(--primary-light) !important;
            color: var(--bs-primary) !important;
        }
        
        /* ===== FORM CONTROLS ===== */
        .form-check-input:checked {
            background-color: var(--bs-primary) !important;
            border-color: var(--bs-primary) !important;
        }
        
        .form-control:focus,
        .form-select:focus,
        .form-check-input:focus {
            border-color: var(--bs-primary) !important;
            box-shadow: 0 0 0 0.2rem rgba(var(--bs-primary-rgb), 0.25) !important;
        }
        
        /* ===== BADGES ===== */
        .badge.bg-primary,
        .badge-primary {
            background-color: var(--bs-primary) !important;
            color: #ffffff !important;
        }
        
        /* ===== PAGINATION ===== */
        .page-link {
            color: var(--bs-primary) !important;
        }
        
        .page-item.active .page-link {
            background-color: var(--bs-primary) !important;
            border-color: var(--bs-primary) !important;
            color: #ffffff !important;
        }
        
        .page-link:hover {
            color: var(--primary-hover) !important;
            background-color: var(--primary-light) !important;
            border-color: var(--bs-primary) !important;
        }
        
        /* ===== DROPDOWN ===== */
        .dropdown-item.active,
        .dropdown-item:active {
            background-color: var(--bs-primary) !important;
            color: #ffffff !important;
        }
        
        .dropdown-item:hover,
        .dropdown-item:focus {
            background-color: var(--primary-light) !important;
            color: var(--bs-primary) !important;
        }
        
        /* ===== PROGRESS BARS ===== */
        .progress-bar {
            background-color: var(--bs-primary) !important;
        }
        
        /* ===== ALERTS ===== */
        .alert-primary {
            color: var(--primary-hover) !important;
            background-color: var(--primary-light) !important;
            border-color: var(--bs-primary) !important;
        }
        
        /* ===== TABLES ===== */
        .table-primary {
            --bs-table-bg: var(--primary-light) !important;
            --bs-table-color: var(--bs-primary) !important;
        }
        
        /* ===== TABS ===== */
        .nav-tabs .nav-link.active {
            color: var(--bs-primary) !important;
            border-color: var(--bs-primary) !important;
        }
        
        .nav-tabs .nav-link:hover {
            color: var(--primary-hover) !important;
        }
        
        /* ===== CUSTOM HOVER EFFECTS ===== */
        .btn:hover,
        button:hover {
            cursor: pointer;
        }
        
        /* ===== TEXT COLOR ===== */
        body {
            color: var(--bs-text-color) !important;
        }
    </style>
    
    <!-- push target to head -->
    @stack('styles')
    <title>@yield('title')</title>
</head>
<body >
    <!-- main wrapper -->
    <div class="dashboard-main-wrapper login-dashboard-main-wrapper">
