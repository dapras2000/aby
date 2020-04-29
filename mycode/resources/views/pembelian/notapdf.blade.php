<!DOCTYPE html>
<html>
<head>
   <title style="margin-top: -10px;">Nota Pembelian {{ $setting->nama_perusahaan }}</title>
   <style type="text/css">
      table td th{font-family: Georgia, 'Times New Roman'; font-size: 12px;}
      table.data td,      
      table.data th{
         border: 0.01em solid black;
      }

      table.data th{
         text-align: center;
      }
      table.data{ border-collapse: collapse}
      table.tableatas tr{ vertical-align: top; }
   </style>
   <style>
    @page { size: 15cm 20cm landscape;}
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
    <th>Produk</th>
    <th>Harga</th>
    <th>Jumlah</th>
    <th>Diskon</th>
    <th>Subtotal</th>
   </tr>

   <tbody>
    @foreach($detail as $data)
      
    <tr>
       <td width="5%" align="center">{{ ++$no }}.</td>
       <td width="30%" >{{ $data->nama_produk }}</td>
       <td width="20%"  align="right">{{ format_uang($data->belidetail) }}</td>
       <td width="10%"  align="center">{{ $data->jumlah }}</td>
       <td width="20%"  align="center">{{ format_uang($pembelian->diskon) }}%</td>
       <td width="20%"  align="right">{{ format_uang($data->sub_total) }}</td>
    </tr>
    @endforeach
   
   </tbody>
   <tfoot align="right">
    <tr style="border:none;"><td colspan="4" width="70%" style="border:none;"></td><td align="right">Total Harga</td><td align="right">{{ format_uang($pembelian->total_harga) }}</td></tr>
    <tr><td colspan="4" width="70%" style="border:none;"></td><td align="right">Diskon</td><td align="right">{{ format_uang($pembelian->diskon) }}%</td></tr>
    <tr><td colspan="4" width="70%" style="border:none;"></td><td align="right">Total Bayar</td><td align="right">{{ format_uang($pembelian->bayar) }}</td></tr>    
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