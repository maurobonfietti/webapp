<?php

namespace AppBundle\Controller;

use AppBundle\Services\JwtAuth;
use AppBundle\Services\Helpers;
use AppBundle\Services\UserService;
use AppBundle\Services\TaskService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class BaseController extends Controller
{
    /** @var UserService */
    protected $userService;

    /** @var TaskService */
    protected $taskService;

    /** @var JwtAuth */
    protected $jwtService;

    public function getUserService()
    {
        $this->userService = $this->get(UserService::class);

        return $this->userService;
    }

    public function getTaskService()
    {
        $this->taskService = $this->get(TaskService::class);

        return $this->taskService;
    }

    public function getJwtService()
    {
        $this->jwtService = $this->get(JwtAuth::class);

        return $this->jwtService;
    }

    public function response($data, $status = 200)
    {
        return $this->get(Helpers::class)->json($data, $status);
    }

    public function responseError($e)
    {
        return $this->get(Helpers::class)->json($e->getMessage(), $e->getCode());
    }
}
