<?php

/**
 * @SWG\Swagger(
 *     basePath="/v1",
 *     schemes={"http", "https"},
 *     host=L5_SWAGGER_CONST_HOST,
 *     @SWG\Info(
 *         version="1.0.0",
 *         title="Đoàn TNTT GX Nam Hòa - API",
 *         description="Quản lý nhân sự, thiếu nhi và các vấn đề liên quan đến học tập của các em thiếu nhi.",
 *         @SWG\Contact(
 *             email="hominhlong.it@gmail.com"
 *         ),
 *     )
 * )
 */

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
