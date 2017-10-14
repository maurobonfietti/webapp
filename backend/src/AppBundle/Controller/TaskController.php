<?php

namespace AppBundle\Controller;

use AppBundle\Services\JwtAuth;
use Symfony\Component\HttpFoundation\Request;

class TaskController extends BaseController
{
    public function createAction(Request $request)
    {
        try {
            $this->getTaskService();
            $json = $request->get('json', null);
            $token = $request->get('authorization', null);
            $jwtAuth = $this->get(JwtAuth::class);
            $task = $this->taskService->create($json, $token, $jwtAuth);

            return $this->response($task, 201);
        } catch (\Exception $e) {
            return $this->responseError($e);
        }
    }

    public function updateAction(Request $request, $id = null)
    {
        try {
            $this->getTaskService();
            $json = $request->get('json', null);
            $token = $request->headers->get('Authorization');
            $jwtAuth = $this->get(JwtAuth::class);
            $task = $this->taskService->create($json, $token, $jwtAuth, $id);

            return $this->response($task);
        } catch (\Exception $e) {
            return $this->responseError($e);
        }
    }

    public function getAllAction(Request $request)
    {
        try {
            $this->getTaskService();
            $jwtAuth = $this->get(JwtAuth::class);
            $token = $request->headers->get('Authorization');
            $paginator = $this->get('knp_paginator');
            $page = $request->query->getInt('page', 1);
            $tasks = $this->taskService->getAll(
                $jwtAuth, $token, $paginator, $page
            );

            return $this->response($tasks);
        } catch (\Exception $e) {
            return $this->responseError($e);
        }
    }

    public function getOneAction(Request $request, $id = null)
    {
        try {
            $this->getTaskService();
            $jwtAuth = $this->get(JwtAuth::class);
            $token = $request->headers->get('Authorization');
            $task = $this->taskService->getOne($jwtAuth, $token, $id);

            return $this->response($task);
        } catch (\Exception $e) {
            return $this->responseError($e);
        }
    }

    public function searchAction(Request $request, $search = null)
    {
        try {
            $this->getTaskService();
            $jwtAuth = $this->get(JwtAuth::class);
            $token = $request->get('authorization', null);
            $filter = $request->get('filter', null);
            $order = $request->get('order', null);
            $tasks = $this->taskService
                ->search($jwtAuth, $token, $filter, $order, $search);

            return $this->response($tasks);
        } catch (\Exception $e) {
            return $this->responseError($e);
        }
    }

    public function deleteAction(Request $request, $id = null)
    {
        try {
            $this->getTaskService();
            $jwtAuth = $this->get(JwtAuth::class);
            $token = $request->headers->get('Authorization');
            $this->taskService->delete($jwtAuth, $token, $id);

            return $this->response(null, 204);
        } catch (\Exception $e) {
            return $this->responseError($e);
        }
    }
}
