<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    protected $table = 'member';
    protected $primaryKey = 'id_member'; 
    public $incrementing = false;

    // public function penjualan(){
    //     return $this->hasMany('App\Penjualan', 'id_supplier');
    // }
}
