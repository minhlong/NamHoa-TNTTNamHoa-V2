<?php


namespace Fireapps\Core\Social\Facebook;


class Conversations
{
    private $fb;

    public function __construct()
    {
        $this->fb = app(Facebook::class);
    }

    public function list($providerId, $accessToken, $query = [])
    {
        $query = array_merge([
            'fields' => 'unread_count,can_reply,is_subscribed,link,message_count,id,name,senders,snippet,subject,updated_time',
        ], $query);
        return $this->fb->request(
            'get',
            "/{$providerId}/conversations",
            $accessToken,
            $query
        );
    }
}
