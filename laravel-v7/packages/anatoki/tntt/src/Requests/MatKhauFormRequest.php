<?php
namespace TNTT\Requests;

class MatKhauFormRequest extends Request
{
    /**
     * Set custom attributes for validator errors.
     * @return array
     */
    public function attributes()
    {
        return [
            'mat_khau_hien_tai' => 'Mật Khẩu hiện tại',
            'mat_khau_moi'      => 'Mật Khẩu mới',
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     * @return array
     */
    public function rules()
    {
        return [
            'mat_khau_hien_tai'         => 'required',
            'mat_khau_moi'              => 'required|min:6|confirmed',
            'mat_khau_moi_confirmation' => 'required',
        ];
    }
}
