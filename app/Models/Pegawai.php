<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Pegawai extends Model
{
    protected $table = "pegawai";
    protected $guarded = [];
    protected $casts = ['tanggal_lahir' => 'datetime'];

    public function divisi()
    {
        return $this->belongsTo(Divisi::class, 'divisi_id');
    }
}
