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
            $tasks = $this->taskService->getTasks($request, $jwtAuth, $token, $paginator);

            return $this->response($tasks);
        } catch (\Exception $e) {
            return $this->responseError($e);
        }
    }

    public function taskAction(Request $request, $id = null)
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
                $status = 200;
                $data = [
                    'status' => 'success',
                    'code' => $status,
                    'task' => $task,
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

    public function searchAction(Request $request, $search = null)
    {
        $helpers = $this->get(Helpers::class);
        $jwtAuth = $this->get(JwtAuth::class);
        $token = $request->get('authorization', null);
        $authCheck = $jwtAuth->checkToken($token);
        if ($authCheck === true) {
            $identity = $jwtAuth->checkToken($token, true);
            $em = $this->getDoctrine()->getManager();
            $filter = $request->get('filter', null);
            if (empty($filter)) {
                $filter = null;
            } elseif ($filter == 1) {
                $filter = 'new';
            } elseif ($filter == 2) {
                $filter = 'todo';
            } else {
                $filter = 'finished';
            }
            $order = $request->get('order', null);
            if (empty($order) || $order == 2) {
                $order = 'DESC';
            } else {
                $order = 'ASC';
            }
            if ($search != null) {
                $dql = "
                    SELECT t FROM AppBundle:Tasks t WHERE t.user = $identity->sub
                    AND t.title LIKE :search OR t.description LIKE :search
                ";
            } else {
                $dql = "SELECT t FROM AppBundle:Tasks t WHERE t.user = $identity->sub ";
            }
            if ($filter != null) {
                $dql.= " AND t.status = :filter ";
            }
            $dql.= " ORDER BY t.id $order ";
            $query = $em->createQuery($dql);
            if (!empty($search)) {
                $query->setParameter('search', "%$search%");
            }
            if ($filter != null) {
                $query->setParameter('filter', "$filter");
            }
            $task = $query->getResult();
            $status = 200;
            $data = [
                'status' => 'success',
                'code' => $status,
                'data' => $task,
            ];
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
