<?php
namespace App\Http\Requests;

class ThongSoFormRequest extends Request
{
    /**
     * Set custom attributes for validator errors.
     * @return array
     */
    public function attributes()
    {
        return [
            'ky_hieu' => 'Ký Hiệu',
            'ten'     => 'Tên',
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     * @return array
     */
    public function rules()
    {
        return [
            'ky_hieu' => 'required',
            'ten'     => 'required',
        ];
    }
}
