<?php

namespace App;

class GitHub
{
    private $required = [
        'CONTENT_TYPE',
        'HTTP_X_HUB_SIGNATURE',
    ];

    private $body;
    private $payload;

    public function __construct()
    {
        $this->setPayload();
    }

    public function isSigned()
    {
        foreach ($this->required as $r) {
            if (!isset($_SERVER[$r])) {
                return false;
            }
        }

        if ($_SERVER['CONTENT_TYPE'] !== 'application/json') {
            return false;
        }

        $sig = hash_hmac('sha1', $this->body, $_ENV['GITHUB_WEBHOOK_SECRET']);

        if ("sha1={$sig}" !== $_SERVER['HTTP_X_HUB_SIGNATURE']) {
            return false;
        }

        return true;
    }

    private function setPayload()
    {
        $this->body = file_get_contents('php://input');
        $this->payload = json_decode($this->body);
    }

    public function getRefBranch()
    {
        if (!isset($this->payload->ref)) {
            return false;
        }

        $refs = explode('/', $this->payload->ref);
        return end($refs);
    }

    public function getWorkflow()
    {
        if (!isset($this->payload->workflow)) {
            return false;
        }

        return $this->payload->workflow;
    }

    public function getPullPreview()
    {
        if (!isset($this->payload->data) || !isset($this->payload->data->pullpreview_url)) {
            return false;
        }

        return $this->payload->data->pullpreview_url;
    }
}
