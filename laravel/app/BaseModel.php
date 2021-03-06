<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

abstract class BaseModel extends Model
{
    public $hasTaiKhoanCapNhat = true;

    public function beforeSave()
    {
        if ($this->hasTaiKhoanCapNhat && \Auth::check()) {
            $this->tai_khoan_cap_nhat = \Auth::user()->id;
        }
    }

    /**
     * @param array $options
     * @return bool
     */
    public function save(array $options = [])
    {
        $this->beforeSave();

        return parent::save($options); // TODO: Change the autogenerated stub
    }
}
