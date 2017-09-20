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
            'ho_va_ten' => 'Họ và Tên',
        ];
    }

    public function messages()
    {
        return [
            'ngay_sinh.date_format' => 'Trường :attribute không hợp lệ. (Vd: 24-12-2000)',
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
            'ho_va_ten' => 'required',
        ];
    }
}
