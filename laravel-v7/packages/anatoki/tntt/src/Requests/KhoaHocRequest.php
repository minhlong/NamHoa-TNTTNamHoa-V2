<?php

namespace TNTT\Requests;

use TNTT\Models\KhoaHoc;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Validator;

class KhoaHocRequest extends FormRequest
{
    public function __construct(
        array $query = [],
        array $request = [],
        array $attributes = [],
        array $cookies = [],
        array $files = [],
        array $server = [],
        $content = null
    ) {
        parent::__construct($query, $request, $attributes, $cookies, $files, $server, $content);
        $this->customValidation();
    }

    private function customValidation()
    {
        Validator::extend('uniqueDate', function ($attribute, $value, $parameters) {
            $otherDate = $this->get($parameters[0]);
            if (strtotime($value) <= strtotime($otherDate)) {
                return false;
            }
            $counter = KhoaHoc::where('id', '<>', $this->KhoaHoc->id)
                ->whereRaw(
                    '( (ngay_bat_dau <= ? and ? <= ngay_ket_thuc) or (ngay_bat_dau <= ? and ? <= ngay_ket_thuc) )',
                    [$value, $value, $otherDate, $otherDate,]
                )->get()->count();
            if ($counter) {
                return false;
            }

            return true;
        });
    }

    /**
     * Set custom messages for validator errors.
     * @return array
     */
    public function messages()
    {
        return [
            'unique_date' => 'Thông tin ngày không hợp lệ.',
        ];
    }

    /**
     * Set custom attributes for validator errors.
     * @return array
     */
    public function attributes()
    {
        return [
            'ngay_bat_dau' => 'Từ Ngày',
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     * @return array
     */
    public function rules()
    {
        return [
            'ngay_bat_dau'  => 'required|date',
            'ngay_ket_thuc' => 'required|date|unique_date:ngay_bat_dau',
        ];
    }
}
