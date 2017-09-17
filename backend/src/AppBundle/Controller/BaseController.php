<?php

namespace AppBundle\Controller;

use AppBundle\Services\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class BaseController extends Controller
{
    /** @var UserService $userService */
    protected $userService;

    public function getUserService()
    {
        $this->userService = $this->get(UserService::class);
    }
}
