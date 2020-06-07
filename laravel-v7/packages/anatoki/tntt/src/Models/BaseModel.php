<?php

namespace TNTT\Models;

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
        $this->beforeSave();

        return parent::save($options);
    }

    public function beforeSave()
    {
        if (Auth::check()) {
            $this->tai_khoan_cap_nhat = Auth::user()->id;
        }
    }
}