@extends('layouts.app')

@section('title')
  Daftar Pengeluaran
@endsection

@section('breadcrumb')
   @parent
   <li>pengeluaran</li>
@endsection

@section('content')     
<div class="row">
  <div class="col-xs-12">
    <div class="box">
      <div class="box-header">
        <a onclick="addForm()" class="btn btn-success"><i class="fa fa-plus-circle"></i> Tambah</a>
      </div>
      <div class="box-body">  

<table id="tablepengeluaran" class="table table-striped">
<thead>
   <tr>
      <th width="30">No</th>
      <th>Tanggal</th>
      <th>Jenis Pengeluaran</th>
      <th>Nominal</th>
      <th width="100">Aksi</th>
   </tr>
</thead>
<tbody></tbody>
<tfoot>
      <tr>
       <th>Total</th>
       <th></th>
       <th></th>
       <th></th>
      </tr>
     </tfoot>
</table>

      </div>
    </div>
  </div>
</div>

@include('pengeluaran.form')
@endsection

@section('script')
<script type="text/javascript">
function currencyFormat(num) {
  return '' + num.toFixed(0).replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.')
}
var table, save_method;
$(function(){
   table = $('#tablepengeluaran').DataTable({
     "processing" : true,
     "ajax" : {
       "url" : "{{ route('pengeluaran.data') }}",
       "type" : "GET"
     },
     "footerCallback": function ( row, data, start, end, display ) {
            var api = this.api(), data;
 
            // Remove the formatting to get integer data for summation
            var intVal = function ( i ) {
                return typeof i === 'string' ?
                    i.replace(/[\$,Rp.,]/g, '')*1 :
                    typeof i === 'number' ?
                        i : 0;
            };
 
            // Total over all pages
            total = api
                .column( 3 )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
 
            // Total over this page
            pageTotal = api
                .column( 3, { page: 'current'} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
            // Update footer
            $( api.column( 3 ).footer() ).html('Rp. '+currencyFormat(pageTotal));
        },
     "dom": 'Bfrtip',
      //"buttons": ['copy', 'csv', 'excel', 'pdf', 'print']
      "buttons": [
        {
          extend: 'csv',
          text : 'CSV',
          title : '',
          filename: function(){
                    var d = new Date();
                    var n = d.getTime();
                    return 'Pengeluaran' + n;
                },
            exportOptions: {
                    columns: [ 0,1,2,3 ]
                },
            messageTop: '<h3>Data Pengeluaran</h3>',
            messageBottom: null,            
            footer: true,
        },
        {
          extend: 'excel',
          text : 'Excel',
          title : '',
          filename: function(){
                    var d = new Date();
                    var n = d.getTime();
                    return 'Pengeluaran' + n;
                },
            exportOptions: {
                    columns: [ 0,1,2,3 ]
                },
            messageTop: '<h3>Data Pengeluaran</h3>',
            messageBottom: null,
            footer: true,
        },
        {
          extend: 'pdf',
          text : 'PDF',
          title : '',
          filename: function(){
                    var d = new Date();
                    var n = d.getTime();
                    return 'Pengeluaran' + n;
                },
            exportOptions: {
                    columns: [ 0,1,2,3 ]
                },
            messageTop: '<h3>Data Pengeluaran</h3>',
            messageBottom: null,
            footer: true,
        },
        {
          extend: 'print',
          text : 'Print',
          title : '',
          filename: function(){
                    var d = new Date();
                    var n = d.getTime();
                    return 'Pengeluaran' + n;
                },
            exportOptions: {
                    columns: [ 0,1,2,3 ]
                },
            messageTop: '<h3>Data Pengeluaran</h3>',
            messageBottom: null,
            footer: true,
        },
      ],
   }); 
   
   $('#modal-form form').validator().on('submit', function(e){
      if(!e.isDefaultPrevented()){
         var id = $('#id').val();
         if(save_method == "add") url = "{{ route('pengeluaran.store') }}";
         else url = "pengeluaran/"+id;
         
         $.ajax({
           url : url,
           type : "POST",
           data : $('#modal-form form').serialize(),
           success : function(data){
             $('#modal-form').modal('hide');
             table.ajax.reload();
           },
           error : function(){
             alert("Tidak dapat menyimpan data!");
           }   
         });
         return false;
     }
   });
});

function addForm(){
   save_method = "add";
   $('input[name=_method]').val('POST');
   $('#modal-form').modal('show');
   $('#modal-form form')[0].reset();            
   $('.modal-title').text('Tambah Pengeluaran');
}

function editForm(id){
   save_method = "edit";
   $('input[name=_method]').val('PATCH');
   $('#modal-form form')[0].reset();
   $.ajax({
     url : "pengeluaran/"+id+"/edit",
     type : "GET",
     dataType : "JSON",
     success : function(data){
       $('#modal-form').modal('show');
       $('.modal-title').text('Edit Pengeluaran');
       
       $('#id').val(data.id_pengeluaran);
       $('#jenis').val(data.jenis_pengeluaran);
       $('#nominal').val(data.nominal);
       
     },
     error : function(){
       alert("Tidak dapat menampilkan data!");
     }
   });
}

function deleteData(id){
   if(confirm("Apakah yakin data akan dihapus?")){
     $.ajax({
       url : "pengeluaran/"+id,
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
</script>
@endsection