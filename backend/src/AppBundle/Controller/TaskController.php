<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

class TaskController extends BaseController
{
    public function createAction(Request $request)
    {
        try {
            $json = $request->get('json', null);
            $token = $request->get('authorization', null);
            $task = $this->getTaskService()->create($token, $json);

            return $this->response($task, 201);
        } catch (\Exception $e) {
            return $this->responseError($e);
        }
    }

    public function updateAction(Request $request, $id = null)
    {
        try {
            $json = $request->get('json', null);
            $token = $request->headers->get('Authorization');
            $task = $this->getTaskService()->create($token, $json, $id);

            return $this->response($task);
        } catch (\Exception $e) {
            return $this->responseError($e);
        }
    }

    public function updateStatusAction(Request $request, $id = null)
    {
        try {
            $json = $request->get('json', null);
            $token = $request->headers->get('Authorization');
            $task = $this->getTaskService()->updateStatus($token, $json, $id);

            return $this->response($task);
        } catch (\Exception $e) {
            return $this->responseError($e);
        }
    }

    public function getAllAction(Request $request)
    {
        try {
            $paginator = $this->get('knp_paginator');
            $page = $request->query->getInt('page', 1);
            $token = $request->headers->get('Authorization');
            $tasks = $this->getTaskService()->getAll($token, $paginator, $page);

            return $this->response($tasks);
        } catch (\Exception $e) {
            return $this->responseError($e);
        }
    }

    public function getOneAction(Request $request, $id = null)
    {
        try {
            $token = $request->headers->get('Authorization');
            $task = $this->getTaskService()->getOne($token, $id);

            return $this->response($task);
        } catch (\Exception $e) {
            return $this->responseError($e);
        }
    }

    public function searchAction(Request $request, $search = null)
    {
        try {
            $token = $request->get('authorization', null);
            $filter = $request->get('filter', null);
            $order = $request->get('order', null);
            $tasks = $this->getTaskService()->search($token, $filter, $order, $search);

            return $this->response($tasks);
        } catch (\Exception $e) {
            return $this->responseError($e);
        }
    }

    public function deleteAction(Request $request, $id = null)
    {
        try {
            $token = $request->headers->get('Authorization');
            $this->getTaskService()->delete($token, $id);

            return $this->response(null, 204);
        } catch (\Exception $e) {
            return $this->responseError($e);
        }
    }
}
