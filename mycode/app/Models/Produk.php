<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    protected $table = 'produk';
    protected $primaryKey = 'id_produk'; 
    public $incrementing = false;

    public function kategoris(){
    	return $this->belongsTo('App\Models\Kategori','id_kategori','id_kategori');
    }
}
