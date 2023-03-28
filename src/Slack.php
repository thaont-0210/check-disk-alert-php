<?php

class Slack
{
    public $postMessageUrl = 'https://slack.com/api/chat.postMessage';
    public $token = '';
    public $message = '';
    public $title = '';
    public $channel = '';

    public function setToken($token)
    {
        $this->token = $token;
    }

    public function setChannel($channel)
    {
        $this->channel = $channel;
    }

    public function setMessage($message)
    {
        $this->message = $message;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function sendMessage()
    {
        $ch = curl_init($this->postMessageUrl);
        $data = http_build_query([
            'token' => $this->token,
            'channel' => $this->channel,
            'link_names' => true,
            'text' => $this->title,
            'blocks' => json_encode($this->message),
        ]);

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }
}
