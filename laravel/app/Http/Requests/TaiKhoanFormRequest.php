<?php
namespace App\Http\Requests;

class TaiKhoanFormRequest extends Request
{
    /**
     * Set custom attributes for validator errors.
     * @return array
     */
    public function attributes()
    {
        return [
            'ngay_sinh' => 'Ngày Sinh',
            'ngay_rua_toi' => 'Ngày Rửa Tội',
            'ngay_ruoc_le' => 'Ngày Ruớc Lễ',
            'ngay_them_suc' => 'Ngày Thêm Sức',
            'ho_va_ten' => 'Họ và Tên',
        ];
    }

    public function messages()
    {
        return [
            'ngay_sinh.date_format' => 'Trường :attribute không hợp lệ. (Vd: 24-12-2000)',
            'ngay_rua_toi.date_format' => 'Trường :attribute không hợp lệ. (Vd: 24-12-2000)',
            'ngay_ruoc_le.date_format' => 'Trường :attribute không hợp lệ. (Vd: 24-12-2000)',
            'ngay_them_suc.date_format' => 'Trường :attribute không hợp lệ. (Vd: 24-12-2000)',
        ];
    }

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
