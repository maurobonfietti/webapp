<?php

namespace AppBundle\Controller;

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

    public function getUserService()
    {
        $this->userService = $this->get(UserService::class);
    }

    public function getTaskService()
    {
        $this->taskService = $this->get(TaskService::class);
    }

    public function response($data)
    {
        return $this->get(Helpers::class)->json($data);
    }

    public function responseError($e)
    {
        return $this->get(Helpers::class)->json($e->getMessage(), $e->getCode());
    }
}
