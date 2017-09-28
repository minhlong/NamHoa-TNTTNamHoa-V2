<?php
namespace App\Http\Requests;

class TaiKhoanFormRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     * @return array
     */
    public function rules()
    {
        return [
            'ngay_sinh' => 'required|date_format:Y-m-d',
            'ngay_rua_toi' => 'nullable|date_format:Y-m-d',
            'ngay_ruoc_le' => 'nullable|date_format:Y-m-d',
            'ngay_them_suc' => 'nullable|date_format:Y-m-d',
            'ho_va_ten' => 'required',
        ];
    }
}
