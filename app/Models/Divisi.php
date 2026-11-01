<?php

namespace App\Models;

use App\Models\Pegawai;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Divisi extends Model
{
    protected $table = "divisi";
    protected $guarded = [];

    /**
     * Get the user that owns the Divisi
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function instansi(): BelongsTo
    {
        return $this->belongsTo(Instansi::class);
    }

    public function pegawai()
    {
        return $this->hasMany(Pegawai::class, 'divisi_id');
    }
}
