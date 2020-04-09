@extends('layouts.app')

@section('title')
  Daftar Penjualan
@endsection

@section('breadcrumb')
   @parent
   <li>penjualan</li>
@endsection

@section('content')     
<div class="row">
  <div class="col-xs-12">
    <div class="box">
      <div class="box-body">  

<table id="tabel-penjualan" class="table table-striped">
<thead>
   <tr>
   <th width="5%">No</th>
      <th width="10%">Tanggal</th>
      <th width="10%">Member</th>
      <th width="8%">Total Item</th>
      <th width="8%">Total Harga</th>
      <th width="5%">Diskon</th>
      <th width="10%">Total Bayar</th>
      <th width="10%">Kasir</th>
      <th width="10%">Aksi</th>
   </tr>
</thead>
<tbody></tbody>
</table>

      </div>
    </div>
  </div>
</div>

@include('penjualan.detail')
@endsection

@section('script')
<script type="text/javascript">
var table, save_method, table1;
// <script type="text/javascript"> 
//     $(document).ready(function () {
//         $('#table-datatables').DataTable({
//             dom: 'Bfrtip',
//             buttons: ['copy', 'csv', 'excel', 'pdf', 'print']
//         });
//     });
// script>

$(function(){
   table = $('#tabel-penjualan').DataTable({
    // "footerCallback": function ( row, data, start, end, display ) {
    //         var api = this.api(), data;
 
    //         // converting to interger to find total
    //         var intVal = function ( i ) {
    //             return typeof i === 'string' ?
    //                 i.replace(/[\$,]/g, '')*1 :
    //                 typeof i === 'number' ?
    //                     i : 0;
    //         };
 
    //         // computing column Total of the complete result 
    //         var monTotal = api
    //             .column( 4 )
    //             .data()
    //             .reduce( function (a, b) {
    //                 return intVal(a) + intVal(b);
    //             }, 0 );
			
				
    //         // Update footer by showing the total with the reference of the column index 
	  //   $( api.column( 0 ).footer() ).html('Total');
    //         $( api.column( 4 ).footer() ).html(monTotal);
    //     },

     "processing" : true,
     "serverside" : true,
     "ajax" : {
       "url" : "{{ route('penjualan.data') }}",
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
                    return 'Penjualan' + n;
                },
            exportOptions: {
                columns: [ 0,1,2,3,4,5,6,7 ]
                },
            messageTop: 'Data Penjualan',
            messageBottom: null,
        },
        {
          extend: 'excel',
          text : 'Excel',
          filename: function(){
                    var d = new Date();
                    var n = d.getTime();
                    return 'Penjualan' + n;
                },
            exportOptions: {
              columns: [ 0,1,2,3,4,5,6,7 ]
                },
            messageTop: 'Data Penjualan',
            messageBottom: null,
        },
        {
          extend: 'pdf',
          text : 'PDF',
          filename: function(){
                    var d = new Date();
                    var n = d.getTime();
                    return 'Penjualan' + n;
                },
            exportOptions: {
              columns: [ 0,1,2,3,4,5,6,7 ]
                },
            messageTop: 'Data Penjualan',
            messageBottom: null,
        },
        {
          extend: 'print',
          text : 'Print',
          filename: function(){
                    var d = new Date();
                    var n = d.getTime();
                    return 'Penjualan' + n;
                },
            exportOptions: {
                    columns: [ 0,1,2,3,4,5,6,7 ]
                },
            messageTop: 'Data Penjualan',
            messageBottom: null,
        },
      ],
   }); 
   
   table1 = $('#tabel-detail').DataTable({
      "dom" : 'Brt',
      "bSort" : false,
      "processing" : true,
      "dom": 'Bfrtip',
    });

   $('.tabel-supplier').DataTable();
});

function addForm(){
   $('#modal-supplier').modal('show');        
}

function showDetail(id){
    $('#modal-detail').modal('show');

    table1.ajax.url("penjualan/"+id+"/lihat");
    table1.ajax.reload();
}

function deleteData(id){
   if(confirm("Apakah yakin data akan dihapus?")){
     $.ajax({
       url : "penjualan/"+id,
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
  window.open("penjualan/"+id+"/notajualpdf", "Nota PDF", "height=650,width=1024,left=150,scrollbars=yes");
}  
</script>
@endsection