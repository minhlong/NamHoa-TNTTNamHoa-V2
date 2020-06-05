<?php


namespace Fireapps\Core\Social;

use Firebase\JWT\JWT;

class Social
{
    private $facebook, $twitter, $instagram;
    private $socialRepository;
    public function __construct(Facebook $facebook)
    {
        $this->facebook = $facebook;
    }

    /**
     * @param $socialType
     * @param $userInfo
     * @return mixed
     */
    function generateUrl($socialType, $userInfo)
    {
        $token = JWT::encode($userInfo, config('fireapps.common.jwt_token'));
        return $this->{$socialType}->generateUrl($token, $socialType);
    }

    /**
     * @param $socialType
     * @param $request
     * @return mixed
     */
    function auth($socialType, $request)
    {
        return $this->{$socialType}->auth($request);
    }

    /**
     * @param $socialType
     * @param $request
     * @return mixed
     */
    function reAuth($socialType, $request)
    {
        if(in_array($socialType, ['ig_first_auth', 'ig_last_auth']))
            return $this->instagram->auth($socialType, $request);
        else
            return $this->{$socialType}->reAuth($request);
    }

    /**
     * @param $socialType
     * @param $request
     * @return mixed
     */
    function getSocialAccounts($socialType, $request)
    {
        return $this->{$socialType}->getSocialAccounts($request);
    }

    /**
     * @param $socialType
     * @param $payload
     * @param $access_token
     * @return mixed
     */
    function postToSocial($socialType, $payload) {
        return $this->{$socialType}->postToSocial($payload);
    }


    /**
     * @param $socialType
     * @param $payload
     * @param $access_token
     * @return mixed
     */
    function generateReconectUrl($socialType, $userInfo, $id) {
        $token = JWT::encode($userInfo, config('fireapps.common.jwt_token'));
        return $this->{$socialType}->generateReAuthenUrl($token, $id);
    }


    function sync($socialType, $payload)
    {
        return $this->{$socialType}->sync($payload);
    }

    /**
     * @param $socialType
     * @param $data
     * @return mixed
     */
    function registerWebhook($socialType, $data) {
        return $this->{$socialType}->processInsightWebhookData($data);
    }

    /**
     * @param $socialType
     * @param $data
     * @return mixed
     */
    function getPostInsight($socialType, $data) {
        return $this->{$socialType}->getPostInsight($data);
    }


    /**
     * @param $socialType
     * @param $data
     * @return mixed
     */
    function processInsightWebhookData($socialType, $data) {
        return $this->{$socialType}->processInsightWebhookData($data);
    }

    function testFaceBook($socialType){
        return $this->{$socialType}->test();
    }
}
