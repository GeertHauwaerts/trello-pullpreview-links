<?php

namespace App;

use GuzzleHttp\Exception\ClientException;
use Stevenmaguire\Services\Trello\Client;

class Trello
{
    private $client;
    private $trello;

    public function __construct()
    {
        $this->client = new Client([
            'key' => $_ENV['TRELLO_API_KEY'],
            'token' => $_ENV['TRELLO_API_TOKEN']
        ]);

        $this->setTrello();
    }

    private function setTrello()
    {
        $this->getBoards();
        $this->getCards();
    }

    private function getBoards()
    {
        $boards = $this->client->getCurrentUserBoards();

        foreach ($boards as $b) {
            $this->trello[$b->name]['data'] = $b;
            $this->trello[$b->name]['custom_fields'] = $this->client->getBoardCustomFields($b->id);
        }
    }

    private function getCards()
    {
        foreach ($this->trello as $b => $d) {
            $this->trello[$b]['cards'] = $this->client->getBoardCards($d['data']->id, [
                'customFieldItems' => 'true',
            ]);
        }
    }

    private function getCard($id)
    {
        if (!$id) {
            return false;
        }

        foreach ($this->trello as $b => $d) {
            foreach ($this->trello[$b]['cards'] as $c) {
                if ($c->id === $id) {
                    return $c;
                }
            }
        }

        return false;
    }

    private function getBoardCustomFieldId($id, $field)
    {
        foreach ($this->trello as $b => $d) {
            if ($d['data']->id === $id) {
                foreach ($d['custom_fields'] as $c) {
                    if ($c->name === $field) {
                        return $c->id;
                    }
                }
            }
        }

        return false;
    }

    public function findCards($title)
    {
        $cards = [];

        if (!$title) {
            return false;
        }

        foreach ($this->trello as $b => $d) {
            foreach ($this->trello[$b]['cards'] as $c) {
                if (preg_match("/^{$title}:/", $c->name)) {
                    $cards[] = $c->id;
                }
            }
        }

        return $cards;
    }

    public function updateCardCustomField($id, $field, $value)
    {
        $card = $this->getCard($id);

        if (!isset($card->customFieldItems) || empty($card->customFieldItems)) {
            return false;
        }

        $idCustomField = $this->getBoardCustomFieldId($card->idBoard, $field);

        foreach ($card->customFieldItems as $f) {
            if ($f->idCustomField === $idCustomField) {
                if ($f->value->text === $value) {
                    return true;
                }

                try {
                    $this->client->updateCardCustomField($id, $idCustomField, [
                        'value' => [
                            'text' => $value,
                        ],
                    ]);
                } catch (\Exception $e) {
                    return false;
                }

                return true;
            }
        }

        return false;
    }
}
