<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penjualan extends Model
{
    protected $table = 'penjualan';
    protected $primaryKey = 'id_penjualan'; 
    public $incrementing = false;

    public function members(){
    	return $this->belongsTo('App\Models\Member','kode_member','kode_member');
    }
}
