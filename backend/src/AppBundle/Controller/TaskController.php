<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use AppBundle\Services\Helpers;
use AppBundle\Services\JwtAuth;

class TaskController extends BaseController
{
    public function newAction(Request $request, $id = null)
    {
        try {
            $this->getTaskService();
            $json = $request->get('json', null);
            $token = $request->get('authorization', null);
            $jwtAuth = $this->get(JwtAuth::class);
            $task = $this->taskService->create($json, $token, $jwtAuth, $id);

            return $this->response($task);
        } catch (\Exception $e) {
            return $this->responseError($e);
        }
    }

    public function tasksAction(Request $request)
    {
        try {
            $this->getTaskService();
            $jwtAuth = $this->get(JwtAuth::class);
            $token = $request->get('authorization', null);
            $paginator = $this->get('knp_paginator');
            $page = $request->query->getInt('page', 1);
            $tasks = $this->taskService->getTasks(
                $jwtAuth, $token, $paginator, $page
            );

            return $this->response($tasks);
        } catch (\Exception $e) {
            return $this->responseError($e);
        }
    }

    public function taskAction(Request $request, $id = null)
    {
        try {
            $this->getTaskService();
            $jwtAuth = $this->get(JwtAuth::class);
            $token = $request->get('authorization', null);
            $task = $this->taskService->getTask($jwtAuth, $token, $id);

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
            $filterStr = $this->getFilter($filter);
            $orderStr = $this->getOrder($order);
            $tasks = $this->taskService->searchTasks(
                $jwtAuth, $token, $filterStr, $orderStr, $search
            );

            return $this->response($tasks);
        } catch (\Exception $e) {
            return $this->responseError($e);
        }
    }

    private function getFilter($filter)
    {
        if (empty($filter)) {
            $filterStr = null;
        } elseif ($filter == 1) {
            $filterStr = 'new';
        } elseif ($filter == 2) {
            $filterStr = 'todo';
        } else {
            $filterStr = 'finished';
        }

        return $filterStr;
    }

    private function getOrder($order)
    {
        if (empty($order) || $order == 2) {
            $order = 'DESC';
        } else {
            $order = 'ASC';
        }

        return $order;
    }

    public function removeAction(Request $request, $id = null)
    {
        $helpers = $this->get(Helpers::class);
        $jwtAuth = $this->get(JwtAuth::class);
        $token = $request->get('authorization', null);
        $authCheck = $jwtAuth->checkToken($token);
        if ($authCheck === true) {
            $identity = $jwtAuth->checkToken($token, true);
            $em = $this->getDoctrine()->getManager();
            $task = $em->getRepository('AppBundle:Tasks')->findOneBy(['id' => $id]);
            if ($task && is_object($task) && $identity->sub == $task->getUser()->getId()) {
                $em->remove($task);
                $em->flush();
                $status = 200;
                $data = [
                    'status' => 'success',
                    'code' => $status,
                    'msg' => 'Task Deleted.',
                ];
            } else {
                $status = 404;
                $data = [
                    'status' => 'error',
                    'code' => $status,
                    'msg' => 'Task not found.',
                ];
            }
        } else {
            $status = 403;
            $data = [
                'status' => 'error',
                'code' => $status,
                'msg' => 'Sin Autorizacion.',
            ];
        }

        return $helpers->json($data, $status);
    }
}
