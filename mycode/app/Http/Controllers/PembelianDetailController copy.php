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
   public function index($id){
         $produk = Produk::all();
         // $idpembelian = session('idpembelian');
         // $supplier = Supplier::find(session('idsupplier'));
         $idpembelian = $id;
         $idsupplier = Pembelian::find($idpembelian);
         $supplier = Supplier::find($idsupplier->id_supplier);
         
      return view('pembelian_detail.index', compact('produk', 'idpembelian', 'supplier'));
   }

   // public function  detail($id){
   //    $produk = Produk::all();      
   //       $idpembelian = $id;
   //       $sp = Pembelian::find($idpembelian);
   //       $idspl = $sp->id_supplier;
   //       $supplier = Supplier::find($idspl);
   //    return view('pembelian_detail.index', compact('produk', 'idpembelian', 'supplier'));
   // }

    public function listData($id)
   {
   
     $detail = PembelianDetail::leftJoin('produk', 'produk.kode_produk', '=', 'pembelian_detail.kode_produk')
        ->where('id_pembelian', '=', $id)
        ->get(['produk.*','pembelian_detail.*', 'pembelian_detail.harga_beli as belidetail']);
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
       $row[] = "Rp. ".format_uang($list->belidetail);
       $row[] = '<input type="number" class="form-control" name="jumlah_'.$list->id_pembelian_detail.'" value="'.$list->jumlah.'" onChange="changeCount(\''.$list->id_pembelian_detail.'\')">';
       $row[] = "Rp. ".format_uang($list->belidetail * $list->jumlah);
       $row[] = '<a onclick="deleteItem(\''.$list->id_pembelian_detail.'\')" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></a>';
       $data[] = $row;

       //$total += $list->belidetail * $list->jumlah;
       //$total_item += $list->jumlah;
     }

     $databeli = Pembelian::find($id);
     $total = format_uang($databeli->total_harga);
     $totalitem = $databeli->total_item;
     $diskon = $databeli->diskon;
     $bayar = format_uang($databeli->bayar);
     //$bayarrp = format_uang($databeli->bayar);
     $terbilang = ucwords(terbilang($databeli->bayar))." Rupiah";
     
     $data[] = array("<span class='hide total'>$total</span><span class='hide totalitem'>$total_item</span><span class='hide diskon'>$diskon</span><span class='hide bayar'>$bayar</span><span class='hide terbilang'>$terbilang</span>", "", "", "", "", "", "");

     $output = array("data" => $data);
     return response()->json($output);
   }

   public function store(Request $request)
   {
      $produk = Produk::where('kode_produk', '=', $request['kode'])->first();
      $detail = new PembelianDetail;
      $idbeli = $request['idpembelian'];
      $detail->id_pembelian_detail  = Uuid::uuid4();
      $detail->id_pembelian = $request['idpembelian'];
      $detail->kode_produk = $request['kode'];
      $detail->harga_beli = $produk->harga_beli;
      $detail->jumlah = 1;
      $detail->sub_total = $produk->harga_beli;
      $detail->created_at = date('Y-m-d H:i:s');
      $detail->updated_at = date('Y-m-d H:i:s');
      $detail->save();

      $produk = Produk::where('kode_produk', '=', $request['kode'])->first();
      $produk->stok += 1;
      $produk->update();

      $infobeli = PembelianDetail::where('id_pembelian','=',$idbeli)
      ->selectraw('sum(harga_beli) as hargabeli')
      ->selectraw('sum(sub_total) as subtotal')
      ->selectraw('sum(jumlah) as jumlah')
      ->first();
      //->sum('harga_beli')->sum('jumlah')->sum('sub_total');,'sum(jumlah) as total_item', 'sum(sub_total) as bayar'
      //->get()->sum(harga_beli) as total_harga, sum(jumlah) as total_item ,sum(sub_total) as bayar';

      $pembelian = Pembelian::find($idbeli);
      $pembelian->total_item = $infobeli->jumlah;
      $pembelian->total_harga = $infobeli->hargabeli;
      $pembelian->diskon = 0;
      $pembelian->bayar = $infobeli->subtotal;
      $pembelian->update();
      
   }

   public function update(Request $request, $id)
   {
      $nama_input = "jumlah_".$id;
      $detail = PembelianDetail::find($id);
      $idbeli = $detail->id_pembelian;
      $kdproduk= $detail->kode_produk;
      $kdproduk= $detail->jumlah;
      $kdproduk2= $request[$nama_input];
      $detail->jumlah = $request[$nama_input];
      $detail->sub_total = $detail->harga_beli * $request[$nama_input];
      $detail->update();

      $produk = Produk::where('kode_produk', '=', $kdproduk)->first();
      $produk->stok += $kdproduk2-$kdproduk;
      $produk->update();        

      $infobeli = PembelianDetail::where('id_pembelian', '=', $idbeli)
      ->get('sum(harga_beli) as total_harga, sum(jumlah) as total_item ,sum(sub_total) as bayar');
      
      $ubeli = Pembelian::find($idbeli);
      $ubeli->total_item = $infobeli->total_item;
      $ubeli->total_harga = $infobeli->total_harga;
      $ubeli->bayar = $infobeli->bayar;
      $ubeli->update();
   }

   public function destroy($id)
   {
      
      $detail = PembelianDetail::find($id);

      $produk = Produk::where('kode_produk', '=', $detail->kode_produk)->first();
      $produk->stok += $detail->jumlah;
      $produk->update(); 

      $detail->delete();
   }

   // public function loadForm($diskon, $total){
   //   $bayar = $total - ($diskon / 100 * $total);
   //   $data = array(
   //      "totalrp" => format_uang($total),
   //      "bayar" => $bayar,
   //      "bayarrp" => format_uang($bayar),
   //      "terbilang" => ucwords(terbilang($bayar))." Rupiah"
   //    );
   //   return response()->json($data);
   // }
}
