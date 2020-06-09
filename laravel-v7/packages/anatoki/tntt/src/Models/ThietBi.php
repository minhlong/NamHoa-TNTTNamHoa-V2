<?php

namespace TNTT\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ThietBi extends BaseModel
{
    protected $fillable = [
        'id',
        'ten',
        'trang_thai',
        'tai_khoan_id',
        'ngay_muon',
        'ngay_tra',
        'ghi_chu',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        self::setTable('thiet_bi');
    }

    /**
     * @return BelongsTo
     */
    public function tai_khoan()
    {
        return $this->belongsTo(TaiKhoan::class, 'tai_khoan_id')->withTrashed();
    }
}
