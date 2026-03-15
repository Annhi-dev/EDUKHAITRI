<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ThongBao extends Model
{
    protected $fillable = [
        'tieu_de',
        'noi_dung',
        'loai',
        'muc_do',
        'url',
        'icon',
        'gui_tat_ca',
        'created_by',
    ];

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'thong_bao_users')
                    ->withPivot('da_doc', 'doc_luc')
                    ->withTimestamps();
    }

    // Scopes
    public function scopeTheoLoai($query, $loai)
    {
        return $query->where('loai', $loai);
    }
}
