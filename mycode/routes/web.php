<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|


Route::get('/', function () {
    return view('welcome');
});*/



Route::get('/', 'HomeController@index')->name('home');
Route::get('/home', 'HomeController@index')->name('home');

Route::group(['middleware'=>'auth'],function(){
        Route::get('kategori/data', 'KategoriController@listData')->name('kategori.data');
        Route::resource('kategori','KategoriController');

        Route::get('produk/data', 'ProdukController@listData')->name('produk.data');
        Route::post('produk/hapus', 'ProdukController@deleteSelected');
        Route::post('produk/cetak', 'ProdukController@printBarcode');
        Route::resource('produk', 'ProdukController');

        Route::get('member/data', 'MemberController@listData')->name('member.data');
        Route::post('member/cetak', 'MemberController@printCard');
        Route::resource('member', 'MemberController');

        Route::get('supplier/data', 'SupplierController@listData')->name('supplier.data');
        Route::resource('supplier', 'SupplierController');

        Route::get('pengeluaran/data', 'PengeluaranController@listData')->name('pengeluaran.data');
        Route::resource('pengeluaran', 'PengeluaranController');

        Route::get('pembelian/data', 'PembelianController@listData')->name('pembelian.data');
        Route::get('pembelian/{id}/tambah', 'PembelianController@create');
        Route::get('pembelian/{id}/lihat', 'PembelianController@show');
        Route::resource('pembelian', 'PembelianController');
        Route::get('pembelian/{id}/notapdf', 'PembelianController@notaPDF')->name('pembelian.pdf');
        Route::get('pembeliansession/{id}', 'PembelianController@sessione')->name('pembelian.sessione');

        Route::get('pembelian_detail/{id}/data', 'PembelianDetailController@listData')->name('pembelian_detail.data');
        Route::get('pembelian_detail/loadform/{diskon}/{total}', 'PembelianDetailController@loadForm');
        Route::resource('pembelian_detail', 'PembelianDetailController'); 
        Route::get('pembeliandetail/{id}', 'PembelianDetailController@index');
        Route::get('pembelian_detail/diskon/{id}/{diskon}', 'PembelianDetailController@diskon');
        
        Route::get('penjualan/data', 'PenjualanController@listData')->name('penjualan.data');
        Route::get('penjualan/{id}/lihat', 'PenjualanController@show');
        Route::resource('penjualan', 'PenjualanController');
        Route::get('penjualan/{id}/notajualpdf', 'PenjualanDetailController@notaPDF2')->name('notajual.pdf');
        Route::get('penjualansession/{id}', 'PenjualanController@sessione')->name('penjualan.sessione');

        Route::resource('setting', 'SettingController');

        Route::get('user/data', 'UserController@listData')->name('user.data');
        Route::resource('user', 'UserController');

        Route::get('user/profil', 'UserController@profil')->name('user.profil');
        Route::patch('user/{id}/change', 'UserController@changeProfil');

        Route::get('transaksi/baru', 'PenjualanDetailController@newSession')->name('transaksi.new');
        Route::get('transaksi/{id}/data', 'PenjualanDetailController@listData')->name('transaksi.data');
        Route::get('transaksi/cetaknota', 'PenjualanDetailController@printNota')->name('transaksi.cetak');
        Route::get('transaksi/notapdf', 'PenjualanDetailController@notaPDF')->name('transaksi.pdf');
        Route::post('transaksi/simpan', 'PenjualanDetailController@saveData');
        Route::get('transaksi/loadform/{diskon}/{total}/{diterima}', 'PenjualanDetailController@loadForm');
        Route::resource('transaksi', 'PenjualanDetailController');
        Route::get('transaksi/diskon/{id}/{diskon}/{kodemember}', 'PenjualanDetailController@diskon');

        Route::get('laporan', 'LaporanController@index')->name('laporan.index');
        Route::post('laporan', 'LaporanController@refresh')->name('laporan.refresh');
        Route::get('laporan/data/{awal}/{akhir}', 'LaporanController@listData')->name('laporan.data'); 
        Route::get('laporan/pdf/{awal}/{akhir}', 'LaporanController@exportPDF');

        Route::get('laporanpembelian', 'LaporanPembelianController@index')->name('laporanpembelian.index');
        Route::get('laporanpembelian/data/{awal}/{akhir}', 'LaporanPembelianController@listData')->name('laporanpembelian.data'); 

        Route::get('laporanpenjualan', 'LaporanPenjualanController@index')->name('laporanpenjualan.index');
        Route::get('laporanpenjualan/data/{awal}/{akhir}', 'LaporanPenjualanController@listData')->name('laporanpenjualan.data'); 

        Route::get('laporanpengeluaran', 'LaporanPengeluaranController@index')->name('laporanpengeluaran.index');
        Route::get('laporanpengeluaran/data/{awal}/{akhir}', 'LaporanPengeluaranController@listData')->name('laporanpengeluaran.data');         

        Route::get('laporanlaba', 'LaporanLabaController@index')->name('laporanlaba.index');
        Route::get('laporanlaba/data/{awal}/{akhir}', 'LaporanLabaController@listData')->name('laporanlaba.data');         

});

Auth::routes();