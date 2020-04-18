<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Supplier;
use PDF;
use Ramsey\Uuid\Uuid;
use Datatables;


class SupplierController extends Controller
{
   public function index()
   {
      return view('supplier.index'); 
   }

   public function listData()
   {
   
     $supplier = Supplier::orderBy('nama_supplier', 'asc')->get();
     $no = 0;
     $data = array();
     foreach($supplier as $list){
       $no ++;
       $row = array();
       $row[] = $no.'.';
       $row[] = $list->nama_supplier;
       $row[] = $list->alamat;
       $row[] = $list->telpon;
       $row[] = '<div class="btn-group">
               <a onclick="editForm(\''.$list->id_supplier.'\')" class="btn btn-primary btn-sm"><i class="fa fa-pencil"></i></a>
               <a onclick="deleteData(\''.$list->id_supplier.'\')" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></a></div>';
       $data[] = $row;
     }

     $output = array("data" => $data);
     return response()->json($output);
   }

   public function store(Request $request)
   {
      $supplier = new Supplier;
      $supplier->id_supplier     = Uuid::uuid4();
      $supplier->nama_supplier   = $request['nama'];
      $supplier->alamat = $request['alamat'];
      $supplier->telpon = $request['telpon'];
      $supplier->save();
   }

   public function edit($id)
   {
     $supplier = Supplier::find($id);
     echo json_encode($supplier);
   }

   public function update(Request $request, $id)
   {
      $supplier = Supplier::find($id);
      $supplier->nama_supplier = $request['nama'];
      $supplier->alamat = $request['alamat'];
      $supplier->telpon = $request['telpon'];
      $supplier->update();
   }

   public function destroy($id)
   {
      $supplier = Supplier::find($id);
      $supplier->delete();
   }
}
