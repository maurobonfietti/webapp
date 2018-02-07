<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

class TaskController extends BaseController
{
    public function createAction(Request $request)
    {
        try {
            $json = $request->get('json', null);
            $token = $request->headers->get('Authorization');
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

    public function updateStatusAction(Request $request, $id)
    {
        try {
            $token = $request->headers->get('Authorization');
            $task = $this->getTaskService()->updateStatus($token, $id);

            return $this->response($task);
        } catch (\Exception $e) {
            return $this->responseError($e);
        }
    }

    public function updatePriorityAction(Request $request, $id)
    {
        try {
            $token = $request->headers->get('Authorization');
            $task = $this->getTaskService()->updatePriority($token, $id);

            return $this->response($task);
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

    public function getAllAction(Request $request)
    {
        try {
            $page = $request->query->getInt('page', 1);
            $token = $request->headers->get('Authorization');
            $tasks = $this->getTaskService()->getAll($token, $page);

            return $this->response($tasks);
        } catch (\Exception $e) {
            return $this->responseError($e);
        }
    }

    public function searchAction(Request $request, $search = null)
    {
        try {
            $filter = $request->get('filter', null);
            $order = $request->get('order', null);
//            $page = $request->query->getInt('page', 1);
            $page = $request->get('page', 1);
            $token = $request->headers->get('Authorization');
            $tasks = $this->getTaskService()->search($token, $filter, $order, $search, (int) $page);

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
