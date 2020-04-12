<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Redirect;
use App\Models\Penjualan;
use App\Models\Produk;
use App\Models\Member;
use App\Models\PenjualanDetail;
use Ramsey\Uuid\Uuid;

class PenjualanController extends Controller
{
   public function index()
   {
      return view('penjualan.index'); 
   }

   public function sessione($id){
        $penjualan = Penjualan::find($id);

        session(['idpenjualan' => $penjualan->id_penjualan]);

        return Redirect::route('transaksi.index');
    }
   public function listData()
   {
   
     $penjualan = Penjualan::leftJoin('users', 'users.id', '=', 'penjualan.id_user')
        ->leftJoin('member', 'member.kode_member', '=', 'penjualan.kode_member')
        ->select('users.*', 'penjualan.*', 'penjualan.created_at as tanggal','member.nama')
        ->orderBy('penjualan.created_at', 'desc')
        ->get();
     $no = 0;
     $data = array();

     foreach($penjualan as $list){
       $no ++;
       $row = array();
       $row[] = $no.'.';
       $row[] = tanggal_indonesia(substr($list->tanggal, 0, 10), false);
       $row[] = $list->nama;
       $row[] = $list->total_item;
       $row[] = "Rp. ".format_uang($list->total_harga);
       $row[] = $list->diskon."%";
       $row[] = "Rp. ".format_uang($list->bayar);
       $row[] = $list->name;
       $row[] = '<div class="btn-group">        
               <a onclick="showDetail(\''.$list->id_penjualan.'\')" class="btn btn-primary btn-sm"><i class="fa fa-eye"></i></a>
               <a href="penjualansession/'.$list->id_penjualan.'" class="btn btn-warning btn-sm"><i class="fa fa-pencil"></i></a>
               <a onclick="deleteData(\''.$list->id_penjualan.'\')" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></a>
               <a onclick="printData(\''.$list->id_penjualan.'\')" class="btn btn-success btn-sm"><i class="fa fa-print"></i></a>
              </div>';
       $data[] = $row;
     }

     $output = array("data" => $data);
     return response()->json($output);
   }

   public function show($id)
   {
     $detail = PenjualanDetail::leftJoin('produk', 'produk.kode_produk', '=', 'penjualan_detail.kode_produk')
        ->where('id_penjualan', '=', $id)
        ->get(['produk.*','penjualan_detail.*', 'penjualan_detail.harga_jual as jualdetail']);
     $no = 0;
     $data = array();
     foreach($detail as $list){
       $no ++;
       $row = array();
       $row[] = $no;
       $row[] = $list->kode_produk;
       $row[] = $list->nama_produk;
       $row[] = "Rp. ".format_uang($list->jualdetail);
       $row[] = $list->jumlah;
       $row[] = "Rp. ".format_uang($list->sub_total);
       $data[] = $row;
     }
    
     $output = array("data" => $data);
     return response()->json($output);
   }
   
   public function destroy($id)
   {
      $detail = PenjualanDetail::where('id_penjualan', '=', $id)->get();
      foreach($detail as $data){
        $produk = Produk::where('kode_produk', '=', $data->kode_produk)->first();
        $produk->stok += $data->jumlah;
        $produk->update();

        $detail2 = PenjualanDetail::find($data->id_penjualan_detail);
        $detail2->delete();
      }

      $penjualan = Penjualan::find($id);
      $penjualan->delete();
   }
}
