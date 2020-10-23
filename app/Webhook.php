<?php

namespace App;

use App\GitHub;
use App\Trello;

class Webhook
{
    private $required = [
        'GITHUB_WEBHOOK_SECRET',
        'TRELLO_API_KEY',
        'TRELLO_API_TOKEN',
    ];

    private $trello;
    private $github;

    private $customfield = 'PullPreview';
    private $workflow = 'PullPreview';

    public function __construct()
    {
        $this->checkConfig();
        $this->github = new GitHub();
        $this->trello = new Trello();
    }

    public function run()
    {
        if (!$this->github->isSigned()) {
            $this->unauthorized();
        }

        $workflow = $this->github->getWorkflow();

        if (!$workflow || ($workflow !== $this->workflow)) {
            $this->ok();
        }

        if (!$url = $this->github->getPullPreview()) {
            $this->ok();
        }

        $cards = $this->trello->findCards($this->github->getRefBranch());

        if (!$cards || empty($cards)) {
            $this->ok();
        }

        foreach ($cards as $c) {
            if (!$this->trello->updateCardCustomField($c, $this->customfield, $url)) {
                $this->error();
            }
        }

        $this->ok();
    }

    private function checkConfig()
    {
        foreach ($this->required as $r) {
            if (!isset($_ENV[$r])) {
                $this->error();
            }
        }

        if (isset($_ENV['TRELLO_PULLPREVIEW_CUSTOM_FIELD']) &&
            !empty($_ENV['TRELLO_PULLPREVIEW_CUSTOM_FIELD']) &&
            ($_ENV['TRELLO_PULLPREVIEW_CUSTOM_FIELD'] !== $this->customfield)
        ) {
            $this->customfield = $_ENV['TRELLO_PULLPREVIEW_CUSTOM_FIELD'];
        }

        if (isset($_ENV['GITHUB_PULLPREVIEW_WORKFLOW']) &&
            !empty($_ENV['GITHUB_PULLPREVIEW_WORKFLOW']) &&
            ($_ENV['GITHUB_PULLPREVIEW_WORKFLOW'] !== $this->workflow)
        ) {
            $this->workflow = $_ENV['GITHUB_PULLPREVIEW_WORKFLOW'];
        }
    }

    private function error()
    {
        header('HTTP/1.1 500 Internal Server Error');
        exit();
    }

    private function unauthorized()
    {
        header('HTTP/1.1 401 Unauthorized');
        exit();
    }

    private function ok()
    {
        header('HTTP/1.1 204 No Content');
        exit();
    }
}
