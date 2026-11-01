<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Instansi extends Model
{
    protected $table = 'instansi';
    protected $guarded = [];

    /**
     * Get all of the comments for the Instansi
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function divisi(): HasMany
    {
        return $this->hasMany(Divisi::class, 'instansi_id');
    }
}
