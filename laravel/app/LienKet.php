<?php
namespace App;

use App\Services\Library;

class LienKet extends BaseModel
{
    protected $fillable = [
        'id',
        'slug',
        'url',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        self::setTable('lien_ket');
    }
}
