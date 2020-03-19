<?php

namespace App;

use Auth;
use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    /**
     * @param  array  $options
     * @return bool
     */
    public function save(array $options = [])
    {
        if (Auth::check()) {
            $this->tai_khoan_cap_nhat = Auth::user()->id;
        }

        return parent::save($options);
    }

}