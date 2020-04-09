<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pembelian extends Model
{
    protected $table = 'pembelian';
    protected $primaryKey = 'id_pembelian'; 
    public $incrementing = false;

    public function suppliers(){
    	return $this->belongsTo('App\Models\Supplier','id_supplier','id_supplier');
    }
}
