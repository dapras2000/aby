<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Redirect;
use App\Models\Pembelian;
use App\Models\Supplier;
use App\Models\PembelianDetail;
use App\Models\Produk;
use App\Models\Setting;
use Ramsey\Uuid\Uuid;
use PDF;
use Auth;


class PembelianController extends Controller
{
   public function index()
   {
      $supplier = Supplier::all();
      return view('pembelian.index', compact('supplier')); 
   }

   public function listData()
   {
   
     $pembelian = Pembelian::leftJoin('supplier', 'supplier.id_supplier', '=', 'pembelian.id_supplier')
        ->orderBy('pembelian.created_at', 'desc')
        ->get();
     $no = 0;
     $data = array();
     $total = 0;
     $total_item = 0;
     foreach($pembelian as $list){
       $no ++;
       $row = array();
       $row[] = $no.'.';
       $row[] = tanggal_indonesia(substr($list->created_at, 0, 10), false);
       $row[] = $list->nama_supplier;
       $row[] = $list->total_item;
       $row[] = "Rp. ".format_uang($list->total_harga);
       $row[] = $list->diskon."%";
       $row[] = "Rp. ".format_uang($list->bayar);
       $row[] = '<div class="btn-group">
               <a onclick="showDetail(\''.$list->id_pembelian.'\')" class="btn btn-primary btn-sm"><i class="fa fa-eye"></i></a>
               <a href="pembeliansession/'.$list->id_pembelian.'" class="btn btn-warning btn-sm"><i class="fa fa-pencil"></i></a>
               <a onclick="deleteData(\''.$list->id_pembelian.'\')" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></a>
               <a onclick="printData(\''.$list->id_pembelian.'\')" class="btn btn-success btn-sm"><i class="fa fa-print"></i></a>
              </div>';
       $data[] = $row;
       $total += $list->bayar;
       $total_item += $list->jumlah;
     }         

     $output = array("data" => $data);
          
     return response()->json($output);
   }

   public function show($id)
   {
   
     $detail = PembelianDetail::leftJoin('produk', 'produk.kode_produk', '=', 'pembelian_detail.kode_produk')
        ->where('id_pembelian', '=', $id)
        ->get();
     $no = 0;
     $data = array();
     foreach($detail as $list){
       $no ++;
       $row = array();
       $row[] = $no;
       $row[] = $list->kode_produk;
       $row[] = $list->nama_produk;
       $row[] = "Rp. ".format_uang($list->harga_beli);
       $row[] = $list->jumlah;
       $row[] = "Rp. ".format_uang($list->harga_beli * $list->jumlah);
       $data[] = $row;
     }
    
     $output = array("data" => $data);
     return response()->json($output);
   }

   public function create($id)
   {      
      $pembelian = new Pembelian;
      $pembelian->id_pembelian  = Uuid::uuid4();
      $pembelian->id_supplier = $id;     
      $pembelian->total_item = 0;     
      $pembelian->total_harga = 0;     
      $pembelian->diskon = 0;     
      $pembelian->bayar = 0;     
      $pembelian->save();

      session(['idpembelian' => $pembelian->id_pembelian]);
      session(['idsupplier' => $id]);

      return Redirect::route('pembelian_detail.index');      
   }

   public function sessione($id){
      $pembelian = Pembelian::find($id);

      session(['idpembelian' => $pembelian->id_pembelian]);
      session(['idsupplier' => $pembelian->id_supplier]);

      return Redirect::route('pembelian_detail.index');
   }

   public function store(Request $request)
   {
      // $pembelian = Pembelian::find($request['idpembelian']);
      // $pembelian->total_item = $request['totalitem'];
      // $pembelian->total_harga = $request['total'];
      // $pembelian->diskon = $request['diskon'];
      // $pembelian->bayar = $request['bayar'];
      // $pembelian->update();

      // $detail = PembelianDetail::where('id_pembelian', '=', $request['idpembelian'])->get();
      // foreach($detail as $data){
      //   $produk = Produk::where('kode_produk', '=', $data->kode_produk)->first();
      //   $produk->stok += $data->jumlah;
      //   $produk->update();
      // }
      return Redirect::route('pembelian.index');
   }
   
   public function destroy($id)
   {

      $detail = PembelianDetail::where('id_pembelian', '=', $id)->get();
      foreach($detail as $data){
        $produk = Produk::where('kode_produk', '=', $data->kode_produk)->first();
        $produk->stok -= $data->jumlah;
        $produk->update();

        $detail1 = PembelianDetail::find($data->id_pembelian_detail);
        $detail1->delete();
      }
      
      $pembelian = Pembelian::find($id);
      $pembelian->delete();
   }

   public function notaPDF($id){
      $detail = PembelianDetail::leftJoin('produk', 'produk.kode_produk', '=', 'pembelian_detail.kode_produk')
         ->where('id_pembelian', '=', $id)
         ->get(['produk.*','pembelian_detail.*', 'pembelian_detail.harga_beli as belidetail']);
 
       $pembelian = Pembelian::find($id);
       $supplier = Supplier::leftJoin('pembelian', 'pembelian.id_supplier', '=', 'supplier.id_supplier')
         ->where('id_pembelian', '=', $id)
         ->first();
       $setting = Setting::find(1);
       $no = 0;
      
       $customPaper = array(0,0,550,440);
      $pdf = PDF::loadView('pembelian.notapdf', compact('detail', 'pembelian', 'supplier', 'setting', 'no'));
      $pdf->setPaper($customPaper, 'potrait');      
       return $pdf->stream();
    }

}
