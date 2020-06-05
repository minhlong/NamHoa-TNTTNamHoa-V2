<?php
/**
 * Created by PhpStorm.
 * User: buicongdang
 * Date: 7/24/19
 * Time: 9:54 AM
 */

namespace Fireapps\Core\Services\Api;

class ShopApi extends BaseApi
{
    /**
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    function get(): array
    {
        return $this->getRequest('shop.json');
    }
}
