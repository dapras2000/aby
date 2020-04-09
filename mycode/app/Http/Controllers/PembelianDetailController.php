<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Redirect;
use App\Models\Pembelian;
use App\Models\Supplier;
use App\Models\Produk;
use App\Models\PembelianDetail;
use Ramsey\Uuid\Uuid;

class PembelianDetailController extends Controller
{
   public function  index(){
      $produk = Produk::all();
      $idpembelian = session('idpembelian');
      $supplier = Supplier::find(session('idsupplier'));
      return view('pembelian_detail.index', compact('produk', 'idpembelian', 'supplier'));
   }

    public function listData($id)
   {
   
     $detail = PembelianDetail::leftJoin('produk', 'produk.kode_produk', '=', 'pembelian_detail.kode_produk')
        ->where('id_pembelian', '=', $id)
        ->get();
     $no = 0;
     $data = array();
     $total = 0;
     $total_item = 0;
     foreach($detail as $list){
       $no ++;
       $row = array();
       $row[] = $no;
       $row[] = $list->kode_produk;
       $row[] = $list->nama_produk;
       $row[] = "Rp. ".format_uang($list->harga_beli);
       $row[] = '<input type="number" class="form-control" name="jumlah_'.$list->id_pembelian_detail.'" value="'.$list->jumlah.'" onChange="changeCount(\''.$list->id_pembelian_detail.'\')">';
       $row[] = "Rp. ".format_uang($list->harga_beli * $list->jumlah);
       $row[] = '<a onclick="deleteItem(\''.$list->id_pembelian_detail.'\')" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></a>';
       $data[] = $row;

       $total += $list->harga_beli * $list->jumlah;
       $total_item += $list->jumlah;
     }
     $databeli = Pembelian::find($id);
     $diskon = $databeli->diskon;
     $data[] = array("<span class='hide total'>$total</span><span class='hide totalitem'>$total_item</span><span class='hide diskon'>$diskon</span>", "", "", "", "", "", "");
      
     $output = array("data" => $data);
     return response()->json($output);
   }

   public function store(Request $request)
   {
      $produk = Produk::where('kode_produk', '=', $request['kode'])->first();
      $detail = new PembelianDetail;
      $detail->id_pembelian_detail  = Uuid::uuid4();
      $detail->id_pembelian = $request['idpembelian'];
      $detail->kode_produk = $request['kode'];
      $detail->harga_beli = $produk->harga_beli;
      $detail->jumlah = 1;
      $detail->sub_total = $produk->harga_beli;
      $detail->save();

      $produk = Produk::where('kode_produk', '=', $request['kode'])->first();
      $produk->stok += 1;
      $produk->update();

      $idbeli = $request['idpembelian'];
      $infobeli = PembelianDetail::where('id_pembelian','=',$idbeli)
      ->selectraw('sum(harga_beli) as hargabeli')
      ->selectraw('sum(sub_total) as subtotal')
      ->selectraw('sum(jumlah) as jumlah')
      ->first();

      $pembelian = Pembelian::find($idbeli);
      $pembelian->total_item = $infobeli->jumlah;
      $pembelian->total_harga = $infobeli->subtotal;
      $bayar = $pembelian->total_harga - ($pembelian->diskon / 100 * $pembelian->total_harga);
      $pembelian->bayar = $bayar;
      $pembelian->update();

   }

   public function update(Request $request, $id)
   {
      $nama_input = "jumlah_".$id;
      $detail = PembelianDetail::find($id);  
      $jml1= $detail->jumlah;   
      $detail->jumlah = $request[$nama_input];
      $detail->sub_total = $detail->harga_beli * $request[$nama_input];
      $detail->update();      

      $idbeli = $detail->id_pembelian;
      $kdproduk= $detail->kode_produk;
      
      $jml2= $request[$nama_input];
      
      $produk = Produk::where('kode_produk', '=', $kdproduk)->first();
      $stk = $jml2-$jml1;
      $produk->stok += $stk;
      $produk->update();        

      $infobeli = PembelianDetail::where('id_pembelian','=',$idbeli)
      ->selectraw('sum(harga_beli) as hargabeli')
      ->selectraw('sum(sub_total) as subtotal')
      ->selectraw('sum(jumlah) as jumlah')
      ->first();

      $pembelian = Pembelian::find($idbeli);
      $pembelian->total_item = $infobeli->jumlah;
      $pembelian->total_harga = $infobeli->subtotal;
      $bayar = $pembelian->total_harga - ($pembelian->diskon / 100 * $pembelian->total_harga);
      $pembelian->bayar = $bayar;
      $pembelian->update();

   }

   public function destroy($id)
   {
      $detail = PembelianDetail::find($id);

      $idbeli = $detail->id_pembelian;
      $kdproduk= $detail->kode_produk;
      $jml1= $detail->jumlah;
      //$jml2= $request[$nama_input];
      
      $produk = Produk::where('kode_produk', '=', $kdproduk)->first();
      $produk->stok -= $jml1;
      $produk->update();        

      $infobeli = PembelianDetail::where('id_pembelian','=',$idbeli)
      ->selectraw('sum(harga_beli) as hargabeli')
      ->selectraw('sum(sub_total) as subtotal')
      ->selectraw('sum(jumlah) as jumlah')
      ->first();

      $pembelian = Pembelian::find($idbeli);
      $pembelian->total_item = $infobeli->jumlah;
      $pembelian->total_harga = $infobeli->subtotal;
      $bayar = $pembelian->total_harga - ($pembelian->diskon / 100 * $pembelian->total_harga);
      $pembelian->bayar = $bayar;
      $pembelian->update();

      $detail->delete();
   }

   public function loadForm($diskon, $total){
     $bayar = $total - ($diskon / 100 * $total);
     $data = array(
        "totalrp" => format_uang($total),
        "bayar" => $bayar,
        "bayarrp" => format_uang($bayar),
        "terbilang" => ucwords(terbilang($bayar))." Rupiah"
      );
     return response()->json($data);
   }

   
   public function diskon($id,$diskon){     
      $pembelian = Pembelian::find($id);
      $total = $pembelian->total_harga;
      $bayar = $total - ($diskon / 100 * $total);
      $pembelian->bayar = $bayar;
      $pembelian->diskon = $diskon;
      $pembelian->update();
    }
}
