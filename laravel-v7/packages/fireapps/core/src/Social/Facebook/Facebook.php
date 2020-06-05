<?php


namespace Fireapps\Core\Social\Facebook;


use Illuminate\Support\Facades\Log;

class Facebook extends \Fireapps\Core\Social\Facebook
{
    public function subscribedApps($pageId, $accessToken)
    {
        $query = ['subscribed_fields' => 'messages,message_echoes,feed'];

        return $this->request("post", "{$pageId}/subscribed_apps", $accessToken, $query);
    }

    public function request($method, $endpoint, $token, array $query = [])
    {
        $i = 0;
        if (count($query) > 0 && $method == 'get') {
            foreach ($query as $key => $value) {
                if ($i == 0) {
                    $endpoint .= '?';
                } else {
                    $endpoint .= '&';
                }
                if (is_array($value)) {
                    $endpoint .= "{$key}=[".implode(',', $value)."]";
                } else {
                    $endpoint .= "{$key}={$value}";
                }
                $i++;
            }
        }
//        dd($query);
        $data = [];
        $success = true;
        $code = '';
        switch ($method) {
            case 'get' :
                $response = $this->fb->get(
                    $endpoint,
                    $token
                );
                $data = $response->getDecodedBody();
                $code = $response->getHttpStatusCode();
                break;
            case 'post' :
                $response = $this->fb->post(
                    $endpoint,
                    $query,
                    $token
                );
                $data = $response->getDecodedBody();
                $code = $response->getHttpStatusCode();
                break;
            case 'put' :
                $response = $this->fb->put(
                    $endpoint,
                    $query,
                    $token
                );
                $data = $response->getDecodedBody();
                $code = $response->getHttpStatusCode();
                break;
            case 'delete' :
                $response = $this->fb->delete(
                    $endpoint,
                    $query,
                    $token
                );
                $data = $response->getDecodedBody();
                $code = $response->getHttpStatusCode();
                break;
            default :
                $success = false;
                break;
        }
        $result = [
            'status' => $success,
            'message' => '',
            'code' => $code
        ];
        if (isset($data['data'])) {
            $result = array_merge($result, $data);
        } else {
            $result['data'] = $data;
        }
        return $result;
    }
}
