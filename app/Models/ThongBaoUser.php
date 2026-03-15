<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ThongBaoUser extends Model
{
    protected $table = 'thong_bao_users';

    protected $fillable = [
        'thong_bao_id',
        'user_id',
        'da_doc',
        'doc_luc',
    ];

    public function thongBao(): BelongsTo
    {
        return $this->belongsTo(ThongBao::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
