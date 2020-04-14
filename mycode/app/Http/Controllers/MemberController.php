<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Member;
use PDF;
use Ramsey\Uuid\Uuid;
use Datatables;

class MemberController extends Controller
{
   public function index()
   {
      return view('member.index'); 
   }

   public function listData()
   {
   
     $member = Member::orderby('nama', 'asc')->get();
     $no = 0;
     $data = array();
     foreach($member as $list){
       $no ++;
       $row = array();
       $row[] = '<input type="checkbox" name="id[]" value="\''.$list->id_member.'\'">';
       $row[] = $no.'.';
       $row[] = $list->kode_member;
       $row[] = $list->nama;
       $row[] = $list->diskon;
       $row[] = $list->alamat;
       $row[] = $list->telpon;
       $row[] = '<div class="btn-group">
               <a onclick="editForm(\''.$list->id_member.'\')" class="btn btn-primary btn-sm"><i class="fa fa-pencil"></i></a>
               <a onclick="deleteData(\''.$list->id_member.'\')" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></a>
               </div>';
       $data[] = $row;
     }

     $output = array("data" => $data);
     return response()->json($output);
   }

   public function store(Request $request)
   {
     $jml = Member::where('kode_member', '=', $request['kode'])->count();
     if($jml < 1){
      $member = new Member;
      $member->id_member     = Uuid::uuid4();
      $member->kode_member = $request['kode'];
      $member->nama   = $request['nama'];
      $member->nama   = $request['diskon'];
      $member->alamat = $request['alamat'];
      $member->telpon = $request['telpon'];
      $member->save();
      echo json_encode(array('msg'=>'success'));
     }else{
      echo json_encode(array('msg'=>'error'));
     }
   }

   public function edit($id)
   {
     $member = Member::find($id);
     echo json_encode($member);
   }

   public function update(Request $request, $id)
   {
      $member = Member::find($id);
      $member->nama = $request['nama'];
      $member->nama   = $request['diskon'];
      $member->alamat = $request['alamat'];
      $member->telpon = $request['telpon'];
      $member->update();
      echo json_encode(array('msg'=>'success'));
   }

   public function destroy($id)
   {
      $member = Member::find($id);
      $member->delete();
   }

    public function printCard(Request $request)
   {
      $datamember = array();
      foreach($request['id'] as $id){
         $member = Member::find($id);
         $datamember[] = $member;
      }    
      $pdf = PDF::loadView('member.card', compact('datamember'));
      //$pdf->setPaper('a4', 'potrait'); 
      $pdf->setPaper(array(0, 0, 566.93, 850.39), 'potrait');
      return $pdf->stream();

    //   $dataproduk = array();
    //     foreach($request['id'] as $id){
    //         $produk = Produk::find($id);
    //         $dataproduk[] = $produk;
    //     }
    //     $no = 1;
    //     $pdf = PDF::loadView('produk.barcode', compact('dataproduk', 'no'));
    //     $pdf->setPaper('a4', 'potrait');      
    //     return $pdf->stream();
   }
}
