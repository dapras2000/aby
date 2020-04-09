<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PembelianDetail extends Model
{
    protected $table = 'pembelian_detail';
    protected $primaryKey = 'id_pembelian_detail';
    public $incrementing = false;
}
