@extends('layouts.app')

@section('titile')
    Dashboard
@endsection

@section('breadcrumb')
    @parent
    <li>Dashboard</li>    
@endsection

@section('content')
<br>
<div class = "row">
    <div class="col-md-12">
        <div class="box">
            <h1>Selamat Datang</h1>
            <h2>Anda Login Sebagai Admin</h2>
        </div>

    </div>
</div>

@endsection