@extends('layouts.app')

@section('title')
  Daftar Pembelian
@endsection

@section('breadcrumb')
   @parent
   <li>pembelian</li>
@endsection

@section('content')     
<div class="row">
  <div class="col-xs-12">
    <div class="box">
      <div class="box-header">
        <a onclick="addForm()" class="btn btn-success"><i class="fa fa-plus-circle"></i> Transaksi Baru</a>
        <!--@if(!empty(session('idpembelian')))
        <a href="{{ route('pembelian_detail.index') }}" class="btn btn-info"><i class="fa fa-plus-circle"></i> Transaksi Aktif</a>
        @endif-->
      </div>
      <div class="box-body">  

<table class="table table-striped tabel-pembelian">
<thead>
   <tr>
      <th width="5%">No</th>
      <th width="10%">Tanggal</th>
      <th width="20%">Supplier</th>
      <th width="10%">Total Item</th>
      <th width="10%">Total Harga</th>
      <th width="5%">Diskon</th>
      <th width="10%">Total Bayar</th>
      <th width="10%">Aksi</th>
   </tr>
</thead>
<tbody></tbody>
</table>

      </div>
    </div>
  </div>
</div>

@include('pembelian.detail')
@include('pembelian.supplier')
@endsection

@section('script')
<script type="text/javascript">
var table, save_method, table1;
$(function(){
   table = $('.tabel-pembelian').DataTable({
     "processing" : true,
     "serverside" : true,
     "ajax" : {
       "url" : "{{ route('pembelian.data') }}",
       "type" : "GET"
     },
     "dom": 'Bfrtip',
      //"buttons": ['copy', 'csv', 'excel', 'pdf', 'print']
      "buttons": [
        {
          extend: 'csv',
          text : 'CSV',
          filename: function(){
                    var d = new Date();
                    var n = d.getTime();
                    return 'Pembelian' + n;
                },
            exportOptions: {
                columns: [ 0,1,2,3,4,5,6 ]
                },
            messageTop: 'Data Pembelian',
            messageBottom: null,
        },
        {
          extend: 'excel',
          text : 'Excel',
          filename: function(){
                    var d = new Date();
                    var n = d.getTime();
                    return 'Pembelian' + n;
                },
            exportOptions: {
              columns: [ 0,1,2,3,4,5,6 ]
                },
            messageTop: 'Data Pembelian',
            messageBottom: null,
        },
        {
          extend: 'pdf',
          text : 'PDF',
          filename: function(){
                    var d = new Date();
                    var n = d.getTime();
                    return 'Pembelian' + n;
                },
            exportOptions: {
              columns: [ 0,1,2,3,4,5,6 ]
                },
            messageTop: 'Data Pembelian',
            messageBottom: null,
        },
        {
          extend: 'print',
          text : 'Print',
          filename: function(){
                    var d = new Date();
                    var n = d.getTime();
                    return 'Pembelian' + n;
                },
            exportOptions: {
                    columns: [ 0,1,2,3,4,5,6 ]
                },
            messageTop: 'Data Pembelian',
            messageBottom: null,
        },
      ],
   }); 
   
   table1 = $('.tabel-detail').DataTable({
     "bSort" : false,
     "processing" : true
    });

   $('.tabel-supplier').DataTable();
});

function addForm(){
   $('#modal-supplier').modal('show');        
}

function showDetail(id){
    $('#modal-detail').modal('show');

    table1.ajax.url("pembelian/"+id+"/lihat");
    table1.ajax.reload();
}

function deleteData(id){
   if(confirm("Apakah yakin data akan dihapus?")){
     $.ajax({
       url : "pembelian/"+id,
       type : "POST",
       data : {'_method' : 'DELETE', '_token' : $('meta[name=csrf-token]').attr('content')},
       success : function(data){
         table.ajax.reload();
       },
       error : function(){
         alert("Tidak dapat menghapus data!");
       }
     });
   }
}

function printData(id){
  window.open("pembelian/"+id+"/notapdf", "Nota Pembelian Daniyah Herbal", "height=650,width=1024,left=150,scrollbars=yes");
} 

function editData(id,supp){
  window.sessionStorage;
  sessionStorage.removeItem("idpembelian");
  sessionStorage.removeItem("idsupplier");
  sessionStorage.setItem("idpembelian", id);
  sessionStorage.setItem("idsupplier", supp);
  alert(sessionStorage.getItem("idsupplier"));
  //window.open("pembelian_detail", '_self');
  document.location.href="{!! route('pembelian_detail.index'); !!}";
}  
</script>
@endsection