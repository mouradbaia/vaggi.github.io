<?php

namespace App\Controller\Member;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\I18n\I18n;

class AppMemberController extends AppController
{
    public $paginate = [
        'limit' => 10,
        'order' => ['id' => 'DESC']
    ];

    public function isAuthorized($user)
    {
        // Admin can access every action
        if (in_array($user['role'], ['member', 'admin'])) {
            return true;
        }

        // Default deny
        return false;
    }

    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);

        $this->viewBuilder()->layout('member');

        // Check if SSL is enabled.
        if ($this->setLanguage()) {
            $protocol = (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === "off") ? "http://" : "https://";
            return $this->redirect($protocol . env('SERVER_NAME') . env('REQUEST_URI'), 301);
        }

        if (isset($_COOKIE['lang']) && in_array($_COOKIE['lang'], get_site_languages(true))) {
            I18n::locale($_COOKIE['lang']);
        }
    }
}
