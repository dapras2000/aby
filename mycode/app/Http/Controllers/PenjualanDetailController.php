<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Redirect;
use Auth;
use PDF;
use App\Models\Penjualan;
use App\Models\Produk;
use App\Models\Member;
use App\Models\Setting;
use App\Models\PenjualanDetail;
use Ramsey\Uuid\Uuid;

class PenjualanDetailController extends Controller
{
   public function index(){
      $produk = Produk::all();
      $member = Member::all();
      $setting = Setting::first();

     if(!empty(session('idpenjualan'))){
       $idpenjualan = session('idpenjualan');
       return view('penjualan_detail.index', compact('produk', 'member', 'setting', 'idpenjualan'));
     }else{
       return Redirect::route('home');  
     }
   }

   public function listData($id)
   {
   
     $detail = PenjualanDetail::leftJoin('produk', 'produk.kode_produk', '=', 'penjualan_detail.kode_produk')
        ->where('id_penjualan', '=', $id)
        ->get(['produk.*','penjualan_detail.*', 'penjualan_detail.harga_jual as jualdetail']);
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
       $row[] = "Rp. ".format_uang($list->jualdetail);
       $row[] = '<input type="number" class="form-control" name="jumlah_'.$list->id_penjualan_detail.'" value="'.$list->jumlah.'" onChange="changeCount(\''.$list->id_penjualan_detail.'\')">';
       $row[] = $list->diskon."%";
       $row[] = "Rp. ".format_uang($list->sub_total);
       $row[] = '<div class="btn-group">
               <a onclick="deleteItem(\''.$list->id_penjualan_detail.'\')" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></a>';
       $data[] = $row;

       //$total += $list->jualdetail * $list->jumlah;
       $total += $list->sub_total;
       $total_item += $list->jumlah;
     }

     $datajual = Penjualan::find($id);
     $diskon = $datajual->diskon;
     $kdmember = $datajual->kode_member;
     $member = Member::where('kode_member','=',$kdmember)->first();
     $namamember = $member->nama;
     $data[] = array("<span class='hide total'>$total</span><span class='hide totalitem'>$total_item</span><span class='hide diskon'>$diskon</span><span class='hide namamember'>$namamember</span>", "", "", "", "", "", "", "");
    
     $output = array("data" => $data);
     return response()->json($output);
   }

   public function store(Request $request)
   {
        $produk = Produk::where('kode_produk', '=', $request['kode'])->first();

        $detail = new PenjualanDetail;
        $detail->id_penjualan_detail = Uuid::uuid4();
        $detail->id_penjualan = $request['idpenjualan'];
        $detail->kode_produk = $request['kode'];
        $detail->harga_jual = $produk->harga_jual;
        $detail->jumlah = 1;
        $detail->diskon = $produk->diskon;
        $detail->sub_total = $produk->harga_jual - ($produk->diskon/100 * $produk->harga_jual);
        $detail->save();

         $produk = Produk::where('kode_produk', '=', $request['kode'])->first();
         $produk->stok -= 1;
         $produk->update();

         $idjual = $request['idpenjualan'];
         $infojual = PenjualanDetail::where('id_penjualan','=',$idjual)
         ->selectraw('sum(sub_total) as subtotal')
         ->selectraw('sum(jumlah) as jumlah')
         ->first();

         $penjualan = Penjualan::find($idjual);
         $penjualan->total_item = $infojual->jumlah;
         $penjualan->total_harga = $infojual->subtotal;
         $bayar = $penjualan->total_harga - ($penjualan->diskon / 100 * $penjualan->total_harga);
         $penjualan->bayar = $bayar;
         $penjualan->update();
   }

   public function update(Request $request, $id)
   {
      $nama_input = "jumlah_".$id;
      $detail = PenjualanDetail::find($id);
      $jml1= $detail->jumlah;

      $total_harga = $request[$nama_input] * $detail->harga_jual;
      //$detail->sub_total = $produk->harga_jual - ($produk->diskon/100 * $produk->harga_jual);
      $detail->jumlah = $request[$nama_input];
      $detail->sub_total = $total_harga - ($detail->diskon/100 * $total_harga);
      
      $detail->update();

      $idjual = $detail->id_penjualan;
      $kdproduk= $detail->kode_produk;
      
      $jml2= $request[$nama_input];
      
      $produk = Produk::where('kode_produk', '=', $kdproduk)->first();
      $stk = $jml2-$jml1;
      $produk->stok -= $stk;
      $produk->update();        

      $infojual = PenjualanDetail::where('id_penjualan','=',$idjual)
         ->selectraw('sum(sub_total) as subtotal')
         ->selectraw('sum(jumlah) as jumlah')
         ->first();

      $penjualan = Penjualan::find($idjual);
      $penjualan->total_item = $infojual->jumlah;
      $penjualan->total_harga = $infojual->subtotal;
      $bayar = $penjualan->total_harga - ($penjualan->diskon / 100 * $penjualan->total_harga);
      $penjualan->bayar = $bayar;
      $penjualan->update();
   }

   public function destroy($id)
   {
      $detail = PenjualanDetail::find($id);

      $idjual = $detail->id_penjualan;
      $kdproduk= $detail->kode_produk;
      $jml1= $detail->jumlah;
      //$jml2= $request[$nama_input];
      
      $produk = Produk::where('kode_produk', '=', $kdproduk)->first();
      $diskonproduk = $produk->diskon; 
      $produk->stok += $jml1;
      $produk->update();        

      $infojual = PenjualanDetail::where('id_penjualan','=',$idjual)
         ->selectraw('sum(sub_total) as subtotal')
         ->selectraw('sum(jumlah) as jumlah')
         ->first();

      $penjualan = Penjualan::find($idjual);
      $penjualan->total_item = $infojual->jumlah;
      $penjualan->total_harga = $infojual->subtotal;
      $bayar = $penjualan->total_harga - ($penjualan->diskon / 100 * $penjualan->total_harga);
      $penjualan->bayar = $bayar;
      $penjualan->update();

      $detail->delete();
   }

   public function newSession()
   {
      $penjualan = new Penjualan; 
      $penjualan->id_penjualan = Uuid::uuid4();
      $penjualan->total_item = 0;    
      $penjualan->total_harga = 0;    
      $penjualan->diskon = 0;    
      $penjualan->bayar = 0;    
      $penjualan->diterima = 0;    
      $penjualan->id_user = Auth::user()->id;    
      $penjualan->save();
      
      session(['idpenjualan' => $penjualan->id_penjualan]);

      return Redirect::route('transaksi.index');    
   }

   public function saveData(Request $request)
   {
      // $penjualan = Penjualan::find($request['idpenjualan']);
      // $penjualan->kode_member = $request['member'];
      // $penjualan->total_item = $request['totalitem'];
      // $penjualan->total_harga = $request['total'];
      // $penjualan->diskon = $request['diskon'];
      // $penjualan->bayar = $request['bayar'];
      // $penjualan->diterima = $request['diterima'];
      // $penjualan->update();

      // $detail = PenjualanDetail::where('id_penjualan', '=', $request['idpenjualan'])->get();
      // foreach($detail as $data){
      //   $produk = Produk::where('kode_produk', '=', $data->kode_produk)->first();
      //   $produk->stok -= $data->jumlah;
      //   $produk->update();
      // }
      return Redirect::route('transaksi.cetak');
   }
   
   public function loadForm($diskon, $total, $diterima){
     $bayar = $total - ($diskon / 100 * $total);
     $kembali = ($diterima != 0) ? $diterima - $bayar : 0;

     $data = array(
        "totalrp" => format_uang($total),
        "bayar" => $bayar,
        "bayarrp" => format_uang($bayar),
        "terbilang" => ucwords(terbilang($bayar))." Rupiah",
        "kembalirp" => format_uang($kembali),
        "kembaliterbilang" => ucwords(terbilang($kembali))." Rupiah"
      );
     return response()->json($data);
   }

   public function printNota()
   {
      $detail = PenjualanDetail::leftJoin('produk', 'produk.kode_produk', '=', 'penjualan_detail.kode_produk')
        ->where('id_penjualan', '=', session('idpenjualan'))
        ->get(['produk.*','penjualan_detail.*', 'penjualan_detail.harga_jual as jualdetail']);

      $penjualan = Penjualan::find(session('idpenjualan'));
      $setting = Setting::find(1);
      
      if($setting->tipe_nota == 0){
        $handle = printer_open(); 
        printer_start_doc($handle, "Nota");
        printer_start_page($handle);

        $font = printer_create_font("Consolas", 100, 80, 600, false, false, false, 0);
        printer_select_font($handle, $font);
        
        printer_draw_text($handle, $setting->nama_perusahaan, 400, 100);

        $font = printer_create_font("Consolas", 72, 48, 400, false, false, false, 0);
        printer_select_font($handle, $font);
        printer_draw_text($handle, $setting->alamat, 50, 200);

        printer_draw_text($handle, date('Y-m-d'), 0, 400);
        printer_draw_text($handle, substr("             ".Auth::user()->name, -15), 600, 400);

        printer_draw_text($handle, "No : ".substr("00000000".$penjualan->id_penjualan, -8), 0, 500);

        printer_draw_text($handle, "============================", 0, 600);
        
        $y = 700;
        
        foreach($detail as $list){           
           printer_draw_text($handle, $list->kode_produk." ".$list->nama_produk, 0, $y+=100);
           printer_draw_text($handle, $list->jumlah." x ".format_uang($list->harga_jual), 0, $y+=100);
           printer_draw_text($handle, substr("                ".format_uang($list->harga_jual*$list->jumlah), -10), 850, $y);

           if($list->diskon != 0){
              printer_draw_text($handle, "Diskon", 0, $y+=100);
              printer_draw_text($handle, substr("                      -".format_uang($list->diskon/100*$list->sub_total), -10),  850, $y);
           }
        }
        
        printer_draw_text($handle, "----------------------------", 0, $y+=100);

        printer_draw_text($handle, "Total Harga: ", 0, $y+=100);
        printer_draw_text($handle, substr("           ".format_uang($penjualan->total_harga), -10), 850, $y);

        printer_draw_text($handle, "Total Item: ", 0, $y+=100);
        printer_draw_text($handle, substr("           ".$penjualan->total_item, -10), 850, $y);

        printer_draw_text($handle, "Diskon Member: ", 0, $y+=100);
        printer_draw_text($handle, substr("           ".$penjualan->diskon."%", -10), 850, $y);

        printer_draw_text($handle, "Total Bayar: ", 0, $y+=100);
        printer_draw_text($handle, substr("            ".format_uang($penjualan->bayar), -10), 850, $y);

        printer_draw_text($handle, "Diterima: ", 0, $y+=100);
        printer_draw_text($handle, substr("            ".format_uang($penjualan->diterima), -10), 850, $y);

        printer_draw_text($handle, "Kembali: ", 0, $y+=100);
        printer_draw_text($handle, substr("            ".format_uang($penjualan->diterima-$penjualan->bayar), -10), 850, $y);
        

        printer_draw_text($handle, "============================", 0, $y+=100);
        printer_draw_text($handle, "-= TERIMA KASIH =-", 250, $y+=100);
        printer_delete_font($font);
        
        printer_end_page($handle);
        printer_end_doc($handle);
        printer_close($handle);
      }
       
      return view('penjualan_detail.selesai', compact('setting'));
   }

   public function notaPDF(){
     $detail = PenjualanDetail::leftJoin('produk', 'produk.kode_produk', '=', 'penjualan_detail.kode_produk')
        ->where('id_penjualan', '=', session('idpenjualan'))
        ->get(['produk.*','penjualan_detail.*', 'penjualan_detail.harga_jual as jualdetail']);

         $penjualan = Penjualan::find(session('idpenjualan'));
         $setting = Setting::find(1);

         $member = Member::leftJoin('penjualan', 'penjualan.kode_member', '=', 'member.kode_member')
         ->where('id_penjualan', '=', session('idpenjualan'))
         ->first();
      

      $no = 0;
     
     $pdf = PDF::loadView('penjualan_detail.notapdf', compact('detail', 'penjualan', 'member', 'setting', 'no'));
     $pdf->setPaper(array(0,0,550,440), 'potrait');
      return $pdf->stream();
   }

   public function notaPDF2($id){
      $detail = PenjualanDetail::leftJoin('produk', 'produk.kode_produk', '=', 'penjualan_detail.kode_produk')
        ->where('id_penjualan', '=', $id)
        ->get(['produk.*','penjualan_detail.*', 'penjualan_detail.harga_jual as jualdetail']);

         $penjualan = Penjualan::find($id);
         $setting = Setting::find(1);

         $member = Member::leftJoin('penjualan', 'penjualan.kode_member', '=', 'member.kode_member')
         ->where('id_penjualan', '=', $id)
         ->first();
      

      $no = 0;
     
     $pdf = PDF::loadView('penjualan_detail.notapdf', compact('detail', 'penjualan', 'member', 'setting', 'no'));
     $pdf->setPaper(array(0,0,550,440), 'potrait');
      return $pdf->stream();
    }

    public function diskon($id,$diskon,$kode){     
      $penjualan = Penjualan::find($id);
      $total = $penjualan->total_harga;
      $bayar = $total - ($diskon / 100 * $total);
      $penjualan->bayar = $bayar;
      $penjualan->diskon = $diskon;
      $penjualan->kode_member = $kode;
      $penjualan->update();
    }
}
