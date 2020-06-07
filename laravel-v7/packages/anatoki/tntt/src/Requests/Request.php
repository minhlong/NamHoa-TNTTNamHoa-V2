<?php
namespace TNTT\Requests;

use Illuminate\Foundation\Http\FormRequest;

abstract class Request extends FormRequest
{
    public function authorize()
    {
        return \Auth::check();
    }
}
