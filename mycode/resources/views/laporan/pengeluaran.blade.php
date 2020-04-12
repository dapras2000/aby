@extends('layouts.app')

@section('title')
  Laporan Pengeluaran 
@endsection

@section('breadcrumb')
   @parent
   <li>laporan</li>
@endsection

@section('content')     
<div class="row">
  <div class="col-xs-12">
    <div class="box">
      <div class="box-header">
        <!--<a onclick="periodeForm()" class="btn btn-success"><i class="fa fa-plus-circle"></i> Ubah Periode</a>
        <a href="laporan/pdf/{{$awal}}/{{$akhir}}" target="_blank" class="btn btn-info"><i class="fa fa-file-pdf-o"></i> Export PDF</a>
        <br>--><h3>
        <span id="laporan">Laporan Pengeluaran <span id="tgl1">{{ tanggal_indonesia($awal, false) }}</span> s/d <span id="tgl2">{{ tanggal_indonesia($akhir, false) }}</span></span>
        </h3>
      </div>
      
      <div class="box-body"> 
      <table border="0" cellspacing="5" cellpadding="5">
        <tbody><tr>
            <td>Tanggal Awal : </td>
            <td><input id="awal" type="text" class="form-control" name="awal" autofocus required></td>
            <td width="3%"></td>
            <td>Tanggal Akhir : </td>
            <td><input id="akhir" type="text" class="form-control" name="akhir" autofocus required></td>
        </tr>
    </tbody></table><br>
              <table id="tabellaporanbeli" class="table table-striped">
              <thead>
                <tr>
                    <th width="5%">No</th>
                    <th width="10%">Tanggal</th>
                    <th width="30%">Pengeluaran</th>
                </tr>
              </thead>
              <tbody></tbody>
              <tfoot>
                <tr>
                <th>Total</th>
                <th></th>
                <th></th>
                </tr>
              </tfoot>
          </table>
      </table>
      </div>
    </div>
  </div>
</div>

@endsection

@section('script')
<script type="text/javascript">
function currencyFormat(num) {
  return '' + num.toFixed(0).replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.')
}
var table, awal, akhir;
$.fn.datepicker.dates['ind'] = {
    days: ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu"],
    daysShort: ["Min", "Sen", "Sel", "Rab", "Kam", "Jum", "Sab"],
    daysMin: ["Min", "Sen", "Sel", "Rab", "Kam", "Jum", "Sab"],
    months: ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"],
    monthsShort: ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Agu", "Sep", "Okt", "Nov", "Des"],
    today: "Hari Ini",
    clear: "Clear",
    format: "yyyy-mm-dd",
    titleFormat: "MM yyyy", /* Leverages same syntax as 'format' */
    weekStart: 0
};

$(function(){    
   table = $('#tabellaporanbeli').DataTable({     
      "processing" : true,
     "serverside" : true,
     "ajax" : {
       "url" : "laporanpengeluaran/data/{{ $awal }}/{{ $akhir }}",
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
                .column( 2 )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
 
            // Total over this page
            pageTotal = api
                .column( 2, { page: 'current'} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
           
          $( api.column( 2 ).footer() ).html('Rp. '+currencyFormat(pageTotal));
            
        },
     "dom": 'Bfrtip',
      //"buttons": ['copy', 'csv', 'excel', 'pdf', 'print']
      "buttons": [
        {
          extend: 'csv',
          text : 'CSV',
          title   : '',
          filename: function(){
                    var d = new Date();
                    var n = d.getTime();
                    return 'Laporan Pengeluaran' + n;
                },
            exportOptions: {
                    columns: [ 0, 1,2 ]
                },
                messageTop: function(){
              var ttl = 'Laporan Pengeluaran '+ $('#tgl1').html() + ' s/d ' + $('#tgl2').html();
              return ttl
            },        
            messageBottom: null,
            footer: true,
        },
        {
          extend: 'excel',
          text : 'Excel',
          title   : '',
          filename: function(){
                    var d = new Date();
                    var n = d.getTime();
                    return 'Laporan Pengeluaran' + n;
                },
            exportOptions: {
                    columns: [ 0, 1,2 ]
                },
                messageTop: function(){
              var ttl = 'Laporan Pengeluaran '+ $('#tgl1').html() + ' s/d ' + $('#tgl2').html();
              return ttl
            },    
            messageBottom: null,
            footer: true,
        },
        {
            extend: 'pdfHtml5',
            text : 'PDF',
            title   : '',
            filename: function(){
                      var d = new Date();
                      var n = d.getTime();
                      return 'Laporan Pengeluaran' + n;
                },
            exportOptions: {
                    columns: [ 0, 1,2 ]
                },
                messageTop: function(){
              var ttl = 'Laporan Pengeluaran '+ $('#tgl1').html() + ' s/d ' + $('#tgl2').html();
              return ttl
            },    
            messageBottom: null,
            footer: true,
            //orientation: 'landscape',
            //pageSize: 'LEGAL'
        },
        {
            extend: 'print',
            text : 'Print',
            title   : '',
            filename: function(){
                      var d = new Date();
                      var n = d.getTime();
                      return 'Laporan Pengeluaran' + n;
                },
            exportOptions: {
                    columns: [ 0, 1,2 ]
                },
            messageTop: function(){
              var ttl = $('#laporan').html();
              return '<h3>' + ttl + '</h3>'
            },
            messageBottom: null,
            footer: true,
            //orientation: 'landscape',
            //pageSize: 'LEGAL'
        }
      ],
   }); 

   $.fn.dataTable.ext.search.push(
        function (settings, data, dataIndex) {
            var min = $('#awal').datepicker("getDate");
            var max = $('#akhir').datepicker("getDate");
            var startDate = new Date(data[1]);
            if (min == null && max == null) { return true; }
            if (min == null && startDate <= max) { return true;}
            if(max == null && startDate >= min) {return true;}
            if (startDate <= max && startDate >= min) { return true; }
           
            return false;
        }
        );

            $("#awal").datepicker({ onSelect: function () { table.draw(); }, changeMonth: true, changeYear: true,
                format: 'dd MM yyyy',language:'ind',
        autoclose: true});

            $("#akhir").datepicker({ onSelect: function () { table.draw(); }, changeMonth: true, changeYear: true,
            format: 'dd MM yyyy',language:'ind',
     autoclose: true});

            var table = $('#tabellaporanbeli').DataTable();

            // Event listener to the two range filtering inputs to redraw on input
              $('#awal, #akhir').change(function () {
                // var days= ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu"];
                // var months= ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
                // var bulan2 = $('#awal').val().getMonth();
                // var bulan = months(bulan2);
                $('#tgl1').html($('#awal').val());
                $('#tgl2').html($('#akhir').val());
                table.draw();
            });
    });

    
  //  $.fn.dataTable.ext.search.push(
  //   function( settings, data, dataIndex ) {
  //     // startdate=picker.startDate.format('YYYY-MM-DD');
  //     // enddate=picker.endDate.format('YYYY-MM-DD');
  //       var min = parseInt( $('#awal').val(), 10 );
  //       var max = parseInt( $('#akhir').val(), 10 );
  //       var age = parseFloat( data[3] ) || 0; // use data for the age column
 
  //       if ( ( isNaN( min ) && isNaN( max ) ) ||
  //            ( isNaN( min ) && age <= max ) ||
  //            ( min <= age   && isNaN( max ) ) ||
  //            ( min <= age   && age <= max ) )
  //       {
  //           return true;
  //       }
  //       return false;
  //   }
  //);
 
// $(document).ready(function() {
//     var table = $('#tabellaporanbeli').DataTable();
     
//     // Event listener to the two range filtering inputs to redraw on input
//     $('#awal, #akhir').keyup( function() {
//         table.draw();
//     } );
// } );



</script>
@endsection