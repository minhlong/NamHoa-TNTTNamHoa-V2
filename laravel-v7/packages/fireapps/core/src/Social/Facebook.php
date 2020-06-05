<?php

namespace Fireapps\Core\Social;


use \App\Helper\Common;
use \App\Models\PostInsight;
use \App\Models\SocialAccount;
use \App\Models\SpSocialPost;
use \App\Social\Facebook\FacebookLib;

use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Facades\Image;


class Facebook extends Base
{
    protected $fb;
    protected $type = 'facebook';

    const BASE_AUTHORIZATION_URL = 'https://www.facebook.com';

    const GRAPH_VERSION = 'v5.0';

    public function __construct()
    {
        $this->fb = new \Facebook\Facebook([
            'app_id' => config('fireapps.social.facebook.auth.key'),
            'app_secret' => config('fireapps.social.facebook.auth.secret'),
            'default_graph_version' => static::GRAPH_VERSION,
        ]);
    }

    function generateUrl($token)
    {
        $redirectUrl = route('social.callback');
        $appId = config('fireapps.social.facebook.auth.key');
        $permissions = config('fireapps.social.facebook.permissions');
        $verifyInfo = ['token' => $token, 'socialType' => config('fireapps.social.facebook.identify'), 'action' => config('fireapps.social.facebook.action.auth'), 'socialId' => null];
        $data = JWT::encode($verifyInfo, config('fireapps.common.jwt_token'));
        $params = [
            'client_id' => $appId,
            'state' => $data,
            'response_type' => 'code',
            'sdk' => 'php-sdk-' . static::GRAPH_VERSION,
            'redirect_uri' => $redirectUrl,
            'scope' => implode(',', $permissions)
        ];
        return static::BASE_AUTHORIZATION_URL . '/' . static::GRAPH_VERSION. '/dialog/oauth?' . http_build_query($params, null, '&');
    }

    function generateReAuthenUrl($token, $id)
    {
        $redirectUrl = route('social.callback');
        $appId = config('fireapps.social.facebook.auth.key');
        $permissions = config('fireapps.social.facebook.permissions');
        $verifyInfo = ['token' => $token, 'socialType' => config('fireapps.social.facebook.identify'), 'action' => config('fireapps.social.facebook.action.re_auth'), 'socialId' => $id];
        $data = JWT::encode($verifyInfo, config('fireapps.common.jwt_token'));
        $params = [
            'client_id' => $appId,
            'state' => $data,
            'response_type' => 'code',
            'sdk' => 'php-sdk-' . static::GRAPH_VERSION,
            'redirect_uri' => $redirectUrl,
            'scope' => implode(',', $permissions),
            'auth_type' => 'reauthenticate'
        ];
        return static::BASE_AUTHORIZATION_URL . '/' . static::GRAPH_VERSION. '/dialog/oauth?' . http_build_query($params, null, '&');
    }

    public function auth($request)
    {
        try {
            $token = $this->getVerifyToken();
            $profileData =  $this->requestFacebookData('/me?fields=id,name,picture', 'get', [], $token);
            if(!$profileData['status']) {
                return [
                    'status' => false,
                    'message' =>  __("socialpost_validation.get_facebook_infor_error"),
                    'data' => []
                ];
            }
            $secret = [
                'token' => $token,
                'user_id' => @$profileData['data']['id']
            ];
            $profile = ['name' => @$profileData['data']['name'], 'avatar' => @$profileData['data']['picture']['data']['url']];

            $response = $this->requestFacebookData('/me/accounts', 'get', [], $token);
            if(!$response['status']) {
                return [
                    'status' => false,
                    'message' => __("socialpost_validation.get_facebook_infor_error"),
                    'data' => []
                ];
            }

            $data = [];
            foreach ($response['data'] as $page) {

                $item = [
                    'social_id' => $page['id'],
                    'is_available' => filter_var(SocialAccount::where('social_id', $page['id'])->count(), FILTER_VALIDATE_BOOLEAN),
                    'name' => $page['name'],
                    'avatar' => $this->getPageAvatar($page['id']),
                    'social_type' => config('fireapps.social.facebook.identify'),
                ];
                $data[] = $item;

            }
            $result = [
                'status' => true,
                'data' => $data,
                'secret' => JWT::encode($secret, config('fireapps.common.jwt_token')),
                'message' => '',
                'personal_info' => $profile
            ];
        }catch (\Exception $ex) {
            $result = [
                'status' => false,
                'message' => $ex->getMessage(),
                'data' => []
            ];
//            throw new \Exception($ex->getMessage());
            Log::info($ex->getMessage());
        }

        return $result;
    }

    public function reAuth($request)
    {
        try {
            $token = $this->getVerifyToken();
            $info = json_decode(json_encode($request['social_info']), true);
            $profileData =  $this->requestFacebookData('/me?fields=id,name,picture', 'get', [], $token);

            if(!$profileData['status']) {
                return [
                    'status' => false,
                    'message' => __("socialpost_validation.get_facebook_infor_error"),
                    'data' => []
                ];
            }
            $response = $this->requestFacebookData('/me/accounts', 'get', [], $token);
            if(!$response['status']) {
                return [
                    'status' => false,
                    'message' => __("socialpost_validation.get_facebook_infor_error"),
                    'data' => []
                ];
            }

            foreach ($response['data'] as $page) {
                if(!in_array($page['id'],$info)) {
                    continue;
                }
                $item = [
                    'access_token' => json_encode([config('fireapps.social.facebook.social_type.facebook')=> $page['access_token'], config('fireapps.social.facebook.social_type.user') => $token]),
                    'social_id' => $page['id'],
                    'email' => '',
                    'name' => $page['name'],
                    'social_url' => $this->getPageUrl($page['id']),
                    'avatar' => $this->getPageAvatar($page['id']),
                    'social_type' => config('fireapps.social.facebook.identify'),
                    'shop_id' => $request['userInfo']['shop']['id'],
                    'connect_error' => null,
                    'social_user_id' => $profileData['data']['id']

                ];

                $data[] = $item;

            }
            $result = [
                'status' => true,
                'data' => $data,
                'message' => ''
            ];

        }catch (\Exception $ex) {
            $result = [
                'status' => false,
                'message' => $ex->getMessage(),
                'data' => []
            ];
            Log::info($ex->getMessage());
        }

        return $result;
    }


    public function getSocialAccounts($request)
    {
        try {
            $secret = $request['secret'];
            $info = json_decode(json_encode($request['social_info']), true);

            $token = JWT::decode($secret,config('fireapps.common.jwt_token'),['HS256']);
            $token = json_decode(json_encode($token), true);

            $response = $this->requestFacebookData('/me/accounts', 'get', [], $token['token']);

            if(!$response['status']) {
                return [
                    'status' => false,
                    'message' =>  __("socialpost_validation.get_facebook_infor_error"),
                    'data' => []
                ];
            }
            $data = [];
            foreach ($response['data'] as $page) {
                if(!in_array($page['id'],$info)) {
                    continue;
                }
                $item = [
                    'access_token' => [config('fireapps.social.facebook.social_type.facebook')=> $page['access_token'], config('fireapps.social.facebook.social_type.user') => $token['token']],
                    'social_id' => $page['id'],
                    'email' => '',
                    'name' => $page['name'],
                    'social_url' => $this->getPageUrl($page['id']),
                    'avatar' => $this->getPageAvatar($page['id']),
                    'social_type' => config('fireapps.social.facebook.identify'),
                    'shop_id' => $request['userInfo']['shop']['id'],
                    'connect_error' => null,
                    'social_user_id' => $token['user_id'],
                    'init_status' => ['webhook' => false ]
                ];

                $data[] = $item;

            }
            $result = [
                'status' => true,
                'data' => $data,
                'message' => ''
            ];

        }catch (\Exception $ex) {
            $result = [
                'status' => false,
                'message' => $ex->getMessage(),
                'data' => []
            ];
            Log::info($ex->getMessage());
        }

        return $result;
    }


    public function postToSocial($data){
        $account = $data['social'];
        try {
            $token = $this->getAvailableToken($account['access_token'], $account['social_type']);

            $type = $data['post_type'];
            $source = [];

            switch ($type) {
                case config('fireapps.social.facebook.post_type.link'):
                    $source = $this->processForLinkType($token, $data);
                    break;
                case config('fireapps.social.facebook.post_type.text'):
                    $source =[
                        'status' => true,
                        'data' => [
                            'published' => true,
                            'message' => $data['message'],
                        ]
                    ] ;
                    break;
                case config('fireapps.social.facebook.post_type.image'):
                    $source = $this->processForImageType($token, $data);
                    break;
                case config('fireapps.social.facebook.post_type.video'):

                    break;
                case config('fireapps.social.facebook.post_type.product'):
                    $source = $this->processForProductType($token, $data);
                    break;
            }

            if (!$source['status']) {

                $this->processError($account['social_id'], $source);
                return $source;
            }
            $response = $this->requestFacebookData('/me/feed', 'post', $source['data'], $token);
            if ($response['status']) {
                $response['data']['post_social_id'] = $response['data']['id'];
                unset( $response['data']['id']);
            } else {
                $this->processError($account['social_id'], $response);
            }

        } catch (\Exception $ex) {

            $response = [
                'status' => false,
                'data' => '',
                'message' => $ex->getMessage(),
                'code' => $ex->getCode()
            ];
            $this->processError($account['social_id'], $response);
        }

        return $response;
    }

    private function registerWebhook($token) {
        $response = $this->requestFacebookData('/me/subscribed_apps?subscribed_fields=feed', 'post', [], $token);
        return $response;
    }

    public function processInsightWebhookData($postData){
//        $data = '{"from":{"id":"100938301365306","name":"Check number 1"},"post_id":"100938301365306_134951744630628","created_time":1578474736,"item":"reaction","parent_id":"100938301365306_134951744630628","reaction_type":"like","verb":"add"}';
//        $data=  json_decode($data, true);
//        dd($data);
        $data =  $postData;
        $post = SpSocialPost::where("post_social_id", $data['post_id'])->first();

        if(empty($post)) {
            return false;
        }
        if(empty($post->insight()->count())) {
            $insight = new PostInsight(['total_action' => 0]);
//            $insight->save();
//            dd($insight);
            $post->insight()->save($insight);
        }
        $insight = $post->insight()->first();

        $item =  $data['item'];
        switch ($item) {
            case config('fireapps.social.facebook.webhook.item.comment'):
                $this->processInsightWebhookComment($data, $insight);
                break;
            case config('fireapps.social.facebook.webhook.item.reaction'):
                $this->processInsightWebhookReaction($data, $insight);
                break;
        }


    }

    public function processInsightWebhookReaction($data, $insight){
        $action = $data['verb'];
        $plus = '0';
        switch ($action) {
            case config('fireapps.social.facebook.webhook.reaction.verb.add'):
                $insight->total_action = $insight->total_action + 1;
                $plus = 1;
                break;
            case config('fireapps.social.facebook.webhook.reaction.verb.remove'):
                if($insight->total_action > 0) {
                    $insight->total_action = $insight->total_action - 1;
                    $plus = -1;
                }
                break;

        }
        $type = $data['reaction_type'];
        switch ($type) {
            case config('fireapps.social.facebook.webhook.reaction.type.wow') :
                $insight->total_wow += $plus;
                break;
            case config('fireapps.social.facebook.webhook.reaction.type.like') :
                $insight->total_like += $plus;
                break;
            case config('fireapps.social.facebook.webhook.reaction.type.love') :
                $insight->total_love += $plus;
                break;
            case config('fireapps.social.facebook.webhook.reaction.type.haha') :
                $insight->total_haha += $plus;
                break;
            case config('fireapps.social.facebook.webhook.reaction.type.sad') :
                $insight->total_sad += $plus;
                break;
            case config('fireapps.social.facebook.webhook.reaction.type.angry') :
                $insight->total_angry+= $plus;
                break;
        }

        $insight->save();

    }
    public function processInsightWebhookComment($data,$insight){
        $action = $data['verb'];
        switch ($action) {
            case config('fireapps.social.facebook.webhook.comment.verb.add'):
                $insight->total_comment = $insight->total_comment + 1;
                break;
            case config('fireapps.social.facebook.webhook.comment.verb.remove'):
                if($insight->total_comment > 0) {
                    $insight->total_comment = $insight->total_comment - 1;
                }
                break;

        }
        $insight->save();
    }

    public function getListSubscribedApp($page){
        $token = $this->getAvailableToken($page['access_token'], $page['social_type']);
        $response = $this->requestFacebookData('/me/subscribed_apps?subscribed_fields=feed', 'get', [], $token);
        dd($response);
    }

    public function getPostInsight($post){
        $id = $post['post_social_id'];
        $accountId =  $post['social_id'];

        $page =  SocialAccount::find($accountId)->toArray();
      //  dd($page);
        $token = $this->getAvailableToken($page['access_token'], $page['social_type']);
        if(empty($id)) {
            return;
        }
        $response = $this->requestFacebookData(
            "/me/feed?fields=message,attachment, full_picture,picture,attachments, permalink_url, shares.summary(1),reactions.summary(1),likes.summary(1),comments.summary(1)&limit=100",'get',[],
            $token
        );
//        $response = $this->requestFacebookData(
//            "/".$page['social_id']."?fields=posts{id,limit(100),comments.summary(true),reactions.summary(true),likes.summary(true),shares.summary(true) }",'get',[],
//            $token
//        );
   //     $response = $this->requestFacebookData('/'.$id.'/insights?metric=["page_posts_impressions*"]', 'get', [], $token);
        dd($response['data'][10]);

        $response = $this->requestFacebookData(
            "/100938301365306_134326864693116?fields=shares.summary(1),reactions.summary(1),likes.summary(1),comments.summary(1)",'get',[],
            $token
        );
        dd($response);
    }

    public function deletePublishPost($data) {

        try {
            $token = $this->getAvailableToken($data['social']['access_token'], $data['social']['social_type']);
            $response = $this->requestFacebookData('/'.$data['post_social_id'], 'delete', [], $token);
            if(!$response['status']){
                $this->processError($data['social']['social_id'], $response);
                return $response;
            }
            $data  = $response['data'];
            return Common::createResponse($data['success']);
        } catch (\Exception $ex) {
            return Common::createResponse(false, [], $ex->getMessage());
        }


    }


    private function processError($id,$error){

        if ( isset($error['code']) && in_array($error['code'],config('fireapps.social.facebook.reconnect_code') )) {
            $sa = SocialAccount::where('social_id', $id)->first();
            if(empty($sa)) {
                return;
            }
            $sa->connect_error = [
                'code' => $error['code'],
                'message' => $error['message']
            ];
            $sa->save();
        }
    }

    public function likeSocial($data) {
        $type = 'facebook';

        $ids = $data['social_account'];

        $socialId = $data['social_id'];
        foreach ($ids as $id) {
            $sa = SocialAccount::find($id);
            $token = $this->getAvailableToken($sa->access_token, $type);
            $this->requestFacebookData("/$socialId/likes", 'post', [], $token);
        }

        $id = 5;
        $sa = SocialAccount::find($id);
        $token = $this->getAvailableToken($sa->access_token, $type);
        $response = $this->requestFacebookData("/$socialId/likes", 'get', [], $token);
      //  dd($response);
    }

    public function commentSocial($data) {
        $type = 'facebook';
        $id = $data['social_account'];
        $socialId = $data['social_id'];
        $message =  $data['message'];

        $sa = SocialAccount::find($id);
        $token = $this->getAvailableToken($sa->access_token, $type);
        $response = $this->requestFacebookData("/$socialId/Comments", 'post', ['message' => $message], $token);
      //  dd($response);
    }
    private function processForImageType($token,$data){
        $source = [
            'published' => true,
            'message' => $data['message'],
        ];

        $result =   $this->upMedia($token, $data['medias']);


        if (!$result['status']) {
            return $result;
        }
        $images = $result['data'];
        foreach ($images as $image){
            $attachMedia[] = ['media_fbid' => $image];
        }
        $source['attached_media'] = $attachMedia;
        return [
            'status' => true,
            'data' => $source
        ];
    }



    private function processForLinkType($token,$data){
        $source = [
            'published' => true,
            'message' => $data['message'],
            'link' => $data['meta_link']
        ];

        return [
            'status' => true,
            'data' => $source
        ];
    }

    private function processForProductType($token,$data){
        $subType =  $data['sub_type'];

        if($subType == config('fireapps.social.facebook.post_sub_type.image')) {
            return $this->processForImageType($token,$data);
        } else {

            $source = [
                'published' => true,
                'message' => $data['message'],
                'link' => $data['meta_link']
            ];
        }
        return [
            'status' => true,
            'data' => $source
        ];
    }

    private function getAvailableToken($access_token, $type) {
        $token = json_decode($access_token, true);
        return $token[ config('fireapps.social.facebook.social_type')[$type]];
    }

    private function getPageAvatar($pageId) {
        return config('fireapps.social.facebook.base_graph_url').$pageId.'/picture';
    }

    private function getPageUrl($pageId) {
        return config('fireapps.social.facebook.base_url').$pageId;
    }


    public function test(){
        $token = "EAAJlAI7Fa0MBADB2Ki421bSUaXTmmHhZATryDdrdhVIXyZCai3Mf4ZAY3BUvXvQa1w1QK82ZBr7eKVsZBwLkqj4poVmHwWZAbTeUXn4sJVQqkc82zT5kICru40U7NMnC4wZAkbAXxsVdBtlPsWpiLuztiT4U5C6hGctFT2dmL77TL5B1zNu9UWBuZBqF6YUYvQoZD";
        $media = [
            "https://s3.ap-southeast-1.amazonaws.com/static.socialhead.io/medias/47/a8519f6236684dbf1a3f80ec64b7ec02200110111341_47_60.jpg"];
        $result =   $this->upMedia($token, $media   );
    }

    protected function getVerifyToken(){
        $helper = $this->fb->getRedirectLoginHelper();

        if (isset($_GET['state'])) {
            $helper->getPersistentDataHandler()->set('state', $_GET['state']);
        }
        try {
            $accessToken = $helper->getAccessToken();
        } catch(Facebook\Exceptions\FacebookResponseException $e) {
            return '';
        } catch(Facebook\Exceptions\FacebookSDKException $e) {
            return '';
        }

        if (! isset($accessToken)) {
            return '';
//            if ($helper->getError()) {
//                header('HTTP/1.0 401 Unauthorized');
//                echo "Error: " . $helper->getError() . "\n";
//                echo "Error Code: " . $helper->getErrorCode() . "\n";
//                echo "Error Reason: " . $helper->getErrorReason() . "\n";
//                echo "Error Description: " . $helper->getErrorDescription() . "\n";
//            } else {
//                header('HTTP/1.0 400 Bad Request');
//                echo 'Bad request';
//            }
//            exit;
        }


        $oAuth2Client = $this->fb->getOAuth2Client();

        $tokenMetadata = $oAuth2Client->debugToken($accessToken);


        $tokenMetadata->validateAppId(config('fireapps.social.facebook.auth.key'));

        $tokenMetadata->validateExpiration();

        if (! $accessToken->isLongLived()) {
            // Exchanges a short-lived access token for a long-lived one
            try {
                $accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
            } catch (Facebook\Exceptions\FacebookSDKException $e) {
                return '';
            }


        }
        return $accessToken->getValue();
    }




    protected function requestFacebookData($endpoint, $method, $param, $token) {

        $data = [];
        $success = true;
        $code = '';
        switch ($method) {
            case 'get' :
                $response =  $this->fb->get(
                    $endpoint,
                    $token
                );
                $data = $response->getDecodedBody();
                $code = $response->getHttpStatusCode();
                break;
            case 'post' :
                $response =  $this->fb->post(
                    $endpoint,
                    $param,
                    $token
                );
                $data = $response->getDecodedBody();
                $code = $response->getHttpStatusCode();
                break;
            case 'put' :
                $response =  $this->fb->put(
                    $endpoint,

                    $param,
                    $token
                );
                $data = $response->getDecodedBody();
                $code = $response->getHttpStatusCode();
                break;
            case 'delete' :
                $response =  $this->fb->delete(
                    $endpoint,
                    $param,
                    $token
                );
                $data = $response->getDecodedBody();
                $code = $response->getHttpStatusCode();
                break;
            default :
                //request is invalid
                $success = false;
                break;
        }


        return [
            'status'=> $success,
            'message' => '',
            'data' => isset($data['data']) ? $data['data'] : $data,
            'code' => $code
        ];


    }

    protected  function upMedia($token, $sources) {
        $post_images = [];
        $photos = [];
        if (empty($sources)) {
            return [
                'status' => false,
                'data' => [],
                'message' =>  __("socialpost_validation.get_facebook_infor_error"),
            ];
        }
        try {
            foreach ($sources as $source ) {
                $data = [
                    'published' =>false,
                    'source' =>    $this->fb->fileToUpload($source)
                ];
                array_push($photos, $this->fb->request('POST','/me/photos',$data));
            }


            $uploaded_photos = $this->fb->sendBatchRequest($photos,  $token);

            if($uploaded_photos->getHttpStatusCode() == 200) {

                $response = $uploaded_photos->getDecodedBody();
                foreach ($response as $item) {
                    if(@$item['code'] == 200) {
                        $payload = (json_decode($item['body'], true));
                        $post_images[] = $payload['id'];
                    } else {
                        return [
                            'status' => false,
                            'code' =>@$item['code'],
                            'message' =>  __("socialpost_validation.upload_image_fail"). @$item['body'],
                        ];
                    }

                }

                return [
                    'status' => true,
                    'data' => $post_images
                ];
            } else {
                return [
                    'status' => false,
                    'data' => [],
                    'code' => $uploaded_photos->getHttpStatusCode()
                ];
            }

        } catch (\Exception $ex) {
            return [
                'status' => false,
                'data' => [],
                'code' => $ex->getCode(),
                'message' => "upload image error " .$ex->getMessage()
            ];
        }

    }
}
