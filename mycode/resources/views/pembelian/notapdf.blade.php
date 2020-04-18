<!DOCTYPE html>
<html>
<head>
   <title>Nota Pembelian {{ $setting->nama_perusahaan }}</title>
   <style type="text/css">
      table td{font: arial 6px;}
      table.data td,
      table.data th{
         border: 0px solid #ccc;
         padding: 3px;
         font-weight : thin;
      }
      table.data th{
         text-align: center;
      }
      table.data{ border-collapse: collapse }
      table.tableatas tr{ vertical-align: top; }
   </style>
   <style>
    @page { size: 15cm 20cm landscape; margin-top: -3px;}
  </style>
</head>
<body>

<table width="100%" class="tabelatas">
   <tr style="vertical-align: top;">
     <td colspan="3"><center><h3>Nota Pembelian</h3></center></td>
     
  </tr>     
  <tr>
     <td width="45%">{{ $setting->nama_perusahaan }}</td>
     <td>Tanggal</td>
     <td>: {{ tanggal_indonesia(date('Y-m-d')) }}</td>
  </tr>     
  <tr>
      <td>{{ $setting->alamat }}, {{ $setting->telepon }}</td>
     <td>Supplier</td>
     <td>: {{ $supplier->nama_supplier }}</td>
  </tr>
</table>
         <br>
<table width="100%" class="data">
<thead>
   <tr>
    <th>No</th>
    <th>Kode Produk</th>
    <th>Harga</th>
    <th>Jumlah</th>
    <th>Diskon</th>
    <th>Subtotal</th>
   </tr>

   <tbody>
    @foreach($detail as $data)
      
    <tr>
       <td align="center">{{ ++$no }}.</td>
       <td>{{ $data->nama_produk }}</td>
       <td align="right">{{ format_uang($data->belidetail) }}</td>
       <td align="center">{{ $data->jumlah }}</td>
       <td align="right">{{ format_uang($pembelian->diskon) }}%</td>
       <td align="right">{{ format_uang($data->sub_total) }}</td>
    </tr>
    @endforeach
   
   </tbody>
   <tfoot>
    <tr><td colspan="5" align="right">&nbsp;</tr>
    <tr><td colspan="5" align="right">Total Harga</td><td align="right">{{ format_uang($pembelian->total_harga) }}</td></tr>
    <tr><td colspan="5" align="right">Diskon</td><td align="right">{{ format_uang($pembelian->diskon) }}%</td></tr>
    <tr><td colspan="5" align="right">Total Bayar</td><td align="right">{{ format_uang($pembelian->bayar) }}</td></tr>    
   </tfoot>
</table>
<br>
<table width="100%">
  <tr>
   <td align="center">
      Supplier<br><br><br> {{ $supplier->nama_supplier }}
    </td>
    <td align="center">
      Kasir<br><br><br> {{Auth::user()->name}}
    </td>
  </tr>
</table>
</body>
</html>