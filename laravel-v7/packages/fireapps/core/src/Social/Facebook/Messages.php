<?php


namespace Fireapps\Core\Social\Facebook;


class Messages
{
    private $fb;

    public function __construct()
    {
        $this->fb = app(Facebook::class);
    }

    public function list($providerId, $accessToken, array $query = [])
    {
        $query = array_merge([
            'fields' => 'created_time,from,id,message,sticker,attachments,shares,tags',
        ], $query);
        return $this->fb->request(
            'get',
            "/{$providerId}/messages",
            $accessToken,
            $query
        );
    }

    public function send($accessToken, array $query = [])
    {
        return $this->fb->request(
            'post',
            "me/messages",
            $accessToken,
            $query
        );
    }
}
