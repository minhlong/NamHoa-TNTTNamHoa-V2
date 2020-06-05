<?php
/**
 * Created by PhpStorm.
 * User: buicongdang
 * Date: 7/24/19
 * Time: 9:54 AM
 */

namespace Fireapps\Core\Services\Api;

use GuzzleHttp\Client;

class AuthApi extends BaseApi
{
    /**
     * @param array $data
     * @return bool
     */
    function verifyRequest(array $data) : bool
    {
        $tmp = [];
        if (is_string($data)) {
            $each = explode('&',$data);
            foreach($each as $e) {
                list($key, $val) = explode('=', $e);
                $tmp[$key] = $val;
            }
        } elseif(is_array($data)) {
            $tmp = $data;
        } else {
            return false;
        }

        // Timestamp check; 1 hour tolerance
        if(($tmp['timestamp'] - time() > 3600 ) )
            return false;

        if(array_key_exists('hmac', $tmp)) {

            // HMAC Validation
            $queries = array_intersect_key($tmp, [
                'code'      => '',
                'shop'      => '',
                'state'     => '',
                'timestamp' => '',
            ]);
            ksort($queries);

            $queryString = http_build_query($queries);
            $match       = $tmp['hmac'];
            $calculated  = hash_hmac('sha256', $queryString, $this->_spfSecretKey);
            return $calculated === $match;
        }

        return false;
    }

    /**
     * @param $shop_domain
     * @param $appId
     * @return string
     */
    function urlInstall(string $shop_domain, string $appId): string
    {
        $this->setParameter($appId);
        $client_id = $this->_spfApiKey;
        $scopes = $this->_scopes;
        $redirect_uri = $this->_redirect_uri;

        return "https://{$shop_domain}.myshopify.com/admin/oauth/authorize?client_id={$client_id}&scope={$scopes}&redirect_uri={$redirect_uri}";
    }

    /**
     * @param string $shop
     * @param string $code
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    function getAccessToken(string $shop, string $code) : array
    {
        $client = new Client();
        try{
            $response = $client->request('POST', "https://{$shop}/admin/oauth/access_token.json",
                [
                    'headers' => [
                        'Content-Type' => 'application/json'
                    ],
                    'body' => json_encode([
                        'code' => $code,
                        'client_id' => $this->_spfApiKey,
                        'client_secret' => $this->_spfSecretKey
                    ])
                ]);
            return ['status' => true, 'data' => json_decode($response->getBody()->getContents(), true)];
        } catch (\Exception $exception) {
            return ['status' => false, 'message' => $exception->getMessage()];
        }
    }
}
