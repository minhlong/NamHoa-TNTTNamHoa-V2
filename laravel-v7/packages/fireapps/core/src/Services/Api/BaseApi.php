<?php
/**
 * Created by PhpStorm.
 * User: buicongdang
 * Date: 7/24/19
 * Time: 10:16 AM
 */

namespace Fireapps\Core\Services\Api;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class BaseApi
{
    /**
     * @var
     */
    protected $_shopDomain;

    /**
     * @var
     */
    protected $_accessToken, $_spfApiKey, $_spfSecretKey, $_baseUrlApi, $_client, $_scopes, $_redirect_uri, $_api_version;

    public function __construct($appId = null, $shopDomain = null, $accessToken = null, $apiVersion = null)
    {
        $this->setParameter($appId, $shopDomain, $accessToken);

        $this->_client = new Client();

        if(!empty($apiVersion)) {
            $this->_api_version = $apiVersion;
        } else {
            $this->_api_version = '2020-01';
        }
        $this->_baseUrlApi = "https://{$this->_shopDomain}/admin/api/".$this->_api_version."/";
    }

    /**
     * @param string $appId
     * @param string|null $shopDomain
     * @param string|null $accessToken
     * @param string|null $apiVersion
     * @return $this
     */
    function setParameter(string $appId = null, string $shopDomain = null, string $accessToken = null, $apiVersion = null)
    {
        $this->_shopDomain = $shopDomain;

        $this->_accessToken = $accessToken;

        if(!empty($apiVersion)) {
            $this->_api_version = $apiVersion;
        } else {
            $this->_api_version = '2020-01';
        }

        $this->_baseUrlApi = "https://{$this->_shopDomain}/admin/api/".$this->_api_version."/";

        if( ! empty($appId)) {
            $this->_spfApiKey = config("fireapps.shopify.spf_api_key");

            $this->_spfSecretKey = config("fireapps.shopify.spf_secret_key");

            $this->_scopes = implode(',', config("fireapps.shopify.scope"));

            $this->_redirect_uri = config("fireapps.shopify.redirect_url");
        }

        $this->awaitForCreditsRateLimit();

        return $this;
    }


    /**
     * @param string $url
     * @param array $data
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getRequest(string $url, array $data = []) : array
    {
        $client = new Client();
        try{
            $response = $client->request('GET', "$this->_baseUrlApi$url",
                [
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'X-Shopify-Access-Token' => $this->_accessToken
                    ],
                    'query' => $data
                ]
            );
            $header = $response->getHeaders();
            $pageInfo = '';
            if (isset($header['Link'])) {
                $linkString = $header['Link'][0];
                $linkArr = explode(",", $linkString);
                foreach($linkArr as $link) {
                    $rel = explode(";", $link);
                    if( strpos(trim($rel[1],' '), 'next') ) {
                        $search = '&page_info=';
                        $start = strpos($rel[0],$search)+strlen($search);
                        $end = strpos($rel[0],'>');
                        $pageInfo = substr($rel[0],$start,$end-$start);
                    }
                }
            }

            $this->setCreditsRateLimit($response->getHeaderLine('X-Shopify-Shop-Api-Call-Limit'));

            return [
                'status' => true,
                'page_info'    => $pageInfo,
                'data'      => json_decode($response->getBody()->getContents(), true)
            ];
        } catch (\Exception $exception)
        {
            return ['status' => false, 'message' => $exception->getMessage()];
        }
    }

    /**
     * @param string $url
     * @param array $data
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function postRequest(string $url, array $data = [])
    {
        try{
            $response = $this->_client->request('POST', "$this->_baseUrlApi$url",
                [
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'X-Shopify-Access-Token' => $this->_accessToken
                    ],
                    'body' => json_encode($data)
                ]);

            $this->setCreditsRateLimit($response->getHeaderLine('X-Shopify-Shop-Api-Call-Limit'));

            return ['status' => true, 'data' => json_decode($response->getBody()->getContents(), true)];

        } catch (\Exception $exception)
        {
            Log::info($exception->getMessage());
            return ['status' => false, 'message' => $exception->getMessage()];
        }
    }

    /**
     * @param string $url
     * @param array $data
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function putRequest(string $url, array $data = []) : array
    {
        try{
            $response = $this->_client->request(
                'PUT', "$this->_baseUrlApi$url",
                [
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'X-Shopify-Access-Token' => $this->_accessToken
                    ],
                    'body' => json_encode($data)
                ]);

            $this->setCreditsRateLimit($response->getHeaderLine('X-Shopify-Shop-Api-Call-Limit'));

            return ['status' => true, 'data' => json_decode($response->getBody()->getContents(), true)];

        } catch (\Exception $exception)
        {
            return ['status' => false, 'message' => $exception->getMessage()];
        }
    }


    /**
     * @param string $url
     * @param array $data
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function deleteRequest(string $url, array $data = []) :  array
    {
        try{
            $response = $this->_client->request('DELETE', "$this->_baseUrlApi$url",
                [
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'X-Shopify-Access-Token' => $this->_accessToken
                    ],
                    'body' => json_encode($data)
                ]);

            $this->setCreditsRateLimit($response->getHeaderLine('X-Shopify-Shop-Api-Call-Limit'));

            return ['status' => true, 'data' => json_decode($response->getBody()->getContents(), true)];
        } catch (\Exception $exception)
        {
            return ['status' => false, 'message' => $exception->getMessage()];
        }
    }

    /**
     * @param string $url
     * @param array $data
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getRequestPagination(string $url, array $data = []) : array
    {
        $client = new Client();
        try{
            $response = $client->request('GET', "$this->_baseUrlApi$url",
                [
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'X-Shopify-Access-Token' => $this->_accessToken
                    ],
                    'query' => $data,
                ]
            );
            $link = $response->getHeaderLine('Link');
            $pageInfoPrevious = '';
            $pageInfoNext = '';
            if (!empty($link)) {
                $linkArr = explode(",", $link);
                foreach($linkArr as $item) {
                    $rel = explode(";", $item);
                    $search = '&page_info=';
                    $start = strpos($rel[0],$search)+strlen($search);
                    $end = strpos($rel[0],'>');
                    $pageInfo = substr($rel[0],$start,$end-$start);
                    if( strpos(trim($rel[1],' '), 'previous') ) {
                        $pageInfoPrevious = $pageInfo;
                    }else {
                        $pageInfoNext = $pageInfo;
                    }
                }
            }

            $this->setCreditsRateLimit($response->getHeaderLine('X-Shopify-Shop-Api-Call-Limit'));

            return ['status' => true,
                    'page_info_previous' => $pageInfoPrevious,
                    'page_info_next' => $pageInfoNext,
                    'data' => json_decode($response->getBody()->getContents(), true)
            ];
        } catch (\Exception $exception)
        {
            return ['status' => false, 'message' => $exception->getMessage()];
        }
    }

    /**
     * @author Hoang-NL
     * Handle await when rate limit
     */
    private function awaitForCreditsRateLimit(): void
    {
        $key = $this->_shopDomain . ':rest_api_call_rate_limit';
        if (Cache::has($key)) {
            $value = (integer) Cache::get($key);
            if ($value >= 35) {
                sleep(10);
            }
        }
    }

    /**
     * @author Hoang-NL
     * Handle set cost rate limit
     * @param $rateLimitCount
     */
    private function setCreditsRateLimit($rateLimitCount): void
    {
        $rateLimitCount = explode('/', $rateLimitCount, 2)[0];
        $key = $this->_shopDomain . ':rest_api_call_rate_limit';
        Cache::put($key, $rateLimitCount);
    }
}
