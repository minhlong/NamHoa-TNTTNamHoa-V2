<?php


namespace Fireapps\Core\Social\Facebook;


class User
{
    private $fb;

    public function __construct()
    {
        $this->fb = app(Facebook::class);
    }

    public function findById($providerId, $accessToken, $query = [])
    {
        $user = $this->fb->request(
            'get',
            "/{$providerId}",
            $accessToken,
            $query
        );
        return $user['data'] ?? [];
    }

    public function banned($providerId, $accessToken, $query = [])
    {
        $result = $this->fb->request(
            'post',
            "/{$providerId}/blocked",
            $accessToken,
            $query
        );
        return $result['data'] ?? [];
    }

    public function unBanned($providerId, $accessToken, $senderId)
    {
        $result = $this->fb->request(
            'delete',
            "/{$providerId}/blocked/{$senderId}",
            $accessToken
        );
        return $result['data'] ?? [];
    }

}
