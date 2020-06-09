<?php

namespace TNTT\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Request;

class ThuMoi extends BaseModel
{
    protected $fillable = [
        'id',
        'tai_khoan_id',
        'ngay',
        'ghi_chu',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        self::setTable('thu_moi');
    }

    /**
     * @return BelongsTo
     */
    public function tai_khoan()
    {
        return $this->belongsTo(TaiKhoan::class, 'tai_khoan_id')->withTrashed();
    }

    /**
     * @param $query
     */
    public function scopeLocDuLieu($query)
    {
        if ($q = Request::get('tai_khoan_id')) {
            $query->where('tai_khoan_id', $q);
        }
        if ($q = Request::get('ho_va_ten')) {
            $query->whereHas('tai_khoan', function ($query) use ($q) {
                $query->where('ho_va_ten', 'like', '%'.$q.'%');
            });
        }
        if ($q = Request::get('tu_ngay')) {
            $query->where('ngay', '>=', $q);
        }
        if ($q = Request::get('den_ngay')) {
            $query->where('ngay', '<=', $q);
        }
        $query->orderBy('ngay', 'desc');
    }
}
