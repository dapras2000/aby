<!DOCTYPE html>
<html lang={{ config('app.locale') }}>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
<title>{{ config('app.name','Laravel') }}</title>
<meta name="csrf-token" content="{{ csrf_token() }}">
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link rel="stylesheet" href="{{asset('AdminLTE/bower_components/bootstrap/dist/css/bootstrap.min.css')}}">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="{{asset('AdminLTE/bower_components/font-awesome/css/font-awesome.min.css')}}">
  <!-- Ionicons -->
  <link rel="stylesheet" href="{{asset('AdminLTE/bower_components/Ionicons/css/ionicons.min.css')}}">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{asset('AdminLTE/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css')}}">
  <!-- bootstrap datepicker -->
  <link rel="stylesheet" href="{{asset('AdminLTE/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css')}}">
  <link rel="stylesheet" href="{{asset('AdminLTE/dist/css/AdminLTE.min.css')}}">

  <link rel="stylesheet" href="{{asset('AdminLTE/bower_components/datatables-button/buttons.dataTables.min.css')}}">
  <!-- AdminLTE Skins. We have chosen the skin-blue for this starter
        page. However, you can choose any other skin. Make sure you
        apply the skin class to the body tag so the changes take effect. -->
  <link rel="stylesheet" href="{{asset('AdminLTE/dist/css/skins/skin-blue.min.css')}}">

  <link rel="stylesheet" href="{{asset('AdminLTE/dist/css/skins/skin-blue.min.css')}}">

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="{{asset('AdminLTE/https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js')}}"></script>
  <script src="{{asset('AdminLTE/https://oss.maxcdn.com/respond/1.4.2/respond.min.js')}}"></script>
  <![endif]-->

  <!-- Google Font -->
  <link rel="stylesheet" href="{{asset('AdminLTE/bower_components/font/css/css.css')}}">
</head>

<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <!-- Main Header -->
  <header class="main-header">

    <!-- Logo -->
    <a href="index2.html" class="logo">
      <!-- mini logo for sidebar mini 50x50 pixels -->
      <span class="logo-mini"><b>HM</b></span>
      <!-- logo for regular state and mobile devices -->
      <?php $sett = App\Models\Setting::find(1);?>
      <span class="logo-lg">{{ $sett->nama_perusahaan }}</span>
    </a>

    <!-- Header Navbar -->
    <nav class="navbar navbar-static-top" role="navigation">
      <!-- Sidebar toggle button-->
      <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
        <span class="sr-only">Toggle navigation</span>
      </a>
      <!-- Navbar Right Menu -->
      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
          <!-- Messages: style can be found in dropdown.less-->
          
          <!-- User Account Menu -->
          <li class="dropdown user user-menu">
            <!-- Menu Toggle Button -->
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <!-- The user image in the navbar-->
              <img src="{{ asset('images/'.Auth::user()->foto) }}" class="user-image" alt="User Image">
              <!-- hidden-xs hides the username on small devices so only the image appears. -->
              <span class="hidden-xs">{{ Auth::user()->name }}</span>
            </a>
            <ul class="dropdown-menu">
              <!-- The user image in the menu -->
              <li class="user-header">
                <img src="{{ asset('images/'.Auth::user()->foto) }}" class="img-circle" alt="User Image">

                <p>
                  {{ Auth::user()->name }}
                  <!--<small>Member since Nov. 2012</small>-->
                </p>
              </li>
              
              <!-- Menu Footer-->
              <li class="user-footer">
                <div class="pull-left">
                  <a href="#" class="btn btn-default btn-flat">Profile</a>
                </div>
                <div class="pull-right">
                  <a class="btn btn-default btn-flat" href="{{ route('logout') }}"
                  onclick="event.preventDefault();
                                document.getElementById('logout-form').submit();">
                   {{ __('Logout') }}
               </a>

               <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                   @csrf
               </form>

                
                </div>
              </li>
            </ul>
          </li>
         
        </ul>
      </div>
    </nav>
  </header>
  <!-- Left side column. contains the logo and sidebar -->
  <aside class="main-sidebar">

    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">

      <!-- Sidebar Menu -->
      <ul class="sidebar-menu" data-widget="tree">
        <li class="header">MENU NAVIGASI</li>
        <li><a href="{{ route('home') }}"><i class="fa fa-dashboard"></i> <span>Dashboard</span></a></li>

        @if ( Auth::user()->level == 1 )

        <li><a href="{{ route('kategori.index') }}"><i class="fa fa-cube"></i> <span>Kategori</span></a></li>
        <li><a href="{{ route('produk.index') }}"><i class="fa fa-cubes"></i> <span>Produk</span></a></li>
        <li><a href="{{ route('member.index') }}"><i class="fa fa-credit-card"></i> <span>Member</span></a></li>
        <li><a href="{{ route('supplier.index') }}"><i class="fa fa-truck"></i> <span>Supplier</span></a></li>
        <li><a href="{{ route('pengeluaran.index') }}"><i class="fa fa-money"></i> <span>Pengeluaran</span></a></li>
        <li><a href="{{ route('pembelian.index') }}"><i class="fa fa-download"></i> <span>Pembelian</span></a></li>
        <li><a href="{{ route('penjualan.index') }}"><i class="fa fa-upload"></i> <span>Rekap Penjualan</span></a></li>
        
        <li class="treeview">
          <a href="#">
            <i class="fa fa-table"></i> <span>Laporan</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">          
            <li><a href="{{ route('laporanpembelian.index') }}"><i class="fa fa-file-pdf-o"></i> <span>Laporan Pembelian</span></a></li>
            <li><a href="{{ route('laporanpenjualan.index') }}"><i class="fa fa-file-pdf-o"></i> <span>Laporan Penjualan</span></a></li>            
            <li><a href="{{ route('laporanpengeluaran.index') }}"><i class="fa fa-file-pdf-o"></i> <span>Laporan Pengeluaran</span></a></li>  
            <li><a href="{{ route('laporanlaba.index') }}"><i class="fa fa-file-pdf-o"></i> <span>Laporan Laba-Rugi</span></a></li>  
            <li><a href="{{ route('laporan.index') }}"><i class="fa fa-file-pdf-o"></i> <span>Laporan Umum</span></a></li>            
          </ul>
        </li>
        
        <li><a href="{{ route('user.index') }}"><i class="fa fa-user"></i> <span>User</span></a></li>
        <li><a href="{{ route('setting.index') }}"><i class="fa fa-gears"></i> <span>Setting</span></a></li>
      
        <!--<li><a href="{{ route('transaksi.index') }}"><i class="fa fa-shopping-cart"></i> <span>Transaksi</span></a></li>
        --><li><a href="{{ route('transaksi.new') }}"><i class="fa fa-cart-plus"></i> <span>Transaksi Baru</span></a></li>
        <li><a href="{{ route('penjualan.index') }}"><i class="fa fa-upload"></i> <span>Rekap Transaksi</span></a></li>

        @endif
        

      </ul>
      <!-- /.sidebar-menu -->
    </section>
    <!-- /.sidebar -->
  </aside>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
       @yield('title')
      </h1>
      <ol class="breadcrumb">

        @section('breadcrumb')

        <li><a href="#"><i class="fa fa-home"></i> Home</a></li>

        @show
       
      </ol>
    </section>

    <!-- Main content -->
    <section class="content container-fluid">

    @yield('content')

    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <!-- Main Footer -->
  <footer class="main-footer">
    <!-- To the right -->
    <div class="pull-right hidden-xs">
      Aplikasi Oleh time.web.id (0822242424300)
    </div>
    <!-- Default to the left -->
    <strong>Copyright &copy; 2020 <a href="#">Company</a>.</strong> All rights reserved.
  </footer>
<!-- REQUIRED JS SCRIPTS -->

<!-- jQuery 3 -->
<script src="{{asset('AdminLTE/bower_components/jquery/dist/jquery.min.js')}}"></script>
<!-- Bootstrap 3.3.7 -->
<script src="{{asset('AdminLTE/bower_components/bootstrap/dist/js/bootstrap.min.js')}}"></script>
<!-- DataTables -->
<script src="{{asset('AdminLTE/bower_components/datatables.net/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('AdminLTE/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js')}}"></script>
<!-- bootstrap datepicker -->
<script src="{{asset('AdminLTE/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js')}}"></script>


<script src="{{asset('js/validator.min.js')}}"></script>

<script src="{{asset('AdminLTE/bower_components/datatables-button/dataTables.buttons.min.js')}}"></script>
<script src="{{asset('AdminLTE/bower_components/datatables-button/buttons.flash.min.js')}}"></script>
<script src="{{asset('AdminLTE/bower_components/datatables-button/jszip.min.js')}}"></script>
<script src="{{asset('AdminLTE/bower_components/datatables-button/pdfmake.min.js')}}"></script>
<script src="{{asset('AdminLTE/bower_components/datatables-button/vfs_fonts.js')}}"></script>
<script src="{{asset('AdminLTE/bower_components/datatables-button/buttons.html5.min.js')}}"></script>
<script src="{{asset('AdminLTE/bower_components/datatables-button/buttons.print.min.js')}}"></script>

<!-- AdminLTE App -->
<script src="{{asset('AdminLTE/dist/js/adminlte.min.js')}}"></script>

<!-- Optionally, you can add Slimscroll and FastClick plugins.
     Both of these plugins are recommended to enhance the
     user experience. -->
@yield('script')
</body>
</html>