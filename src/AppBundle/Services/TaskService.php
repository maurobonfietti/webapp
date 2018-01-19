<?php

namespace AppBundle\Services;

use AppBundle\Entity\Tasks;
use AppBundle\Services\JwtAuth;
use Doctrine\ORM\EntityManager;
use Knp\Component\Pager\Paginator;

class TaskService
{
    /** @var EntityManager */
    public $em;

    /** @var JwtAuth */
    public $jwtAuth;

    /** @var Paginator */
    public $paginator;

    public function __construct($manager, $jwtAuth, $paginator)
    {
        $this->em = $manager;
        $this->jwtAuth = $jwtAuth;
        $this->paginator = $paginator;
    }

    public function create($token, $json, $id = null)
    {
        $identity = $this->jwtAuth->checkToken($token);
        if ($json === null) {
            throw new \Exception('error: Sin datos para actualizar la tarea.', 400);
        }
        $params = json_decode($json);
        $userId = ($identity->sub != null) ? $identity->sub : null;
        $title = isset($params->title) ? $params->title : null;
        $description = isset($params->description) ? $params->description : null;
        $status = isset($params->status) ? $params->status : null;
        if ($userId === null || empty($title)) {
            throw new \Exception('error: Los datos de la tarea no son validos.', 400);
        }
        $user = $this->em->getRepository('AppBundle:Users')->findOneBy(['id' => $userId]);
        if ($id === null) {
            $data = $this->createTask($user, $title, $description, $status);
        } else {
            $this->getOne($token, $id);
            $data = $this->updateTask($id, $identity, $title, $description, $status);
        }

        return $data;
    }

    private function createTask($user, $title, $description, $status)
    {
        $task = new Tasks();
        $task->setUser($user);
        $task->setTitle($title);
        $task->setDescription($description);
        $task->setStatus($status);
        $task->setCreatedAt(new \DateTime("now"));
        $task->setUpdatedAt(new \DateTime("now"));
        $this->em->persist($task);
        $this->em->flush();
        $data = [
            'status' => 'success',
            'code' => 200,
            'msg' => 'Tarea creada.',
            'task' => $task,
        ];

        return $data;
    }

    private function updateTask($id, $identity, $title, $description, $status)
    {
        $task = $this->em->getRepository('AppBundle:Tasks')->findOneBy(['id' => $id]);
        if (!isset($identity->sub) || $identity->sub != $task->getUser()->getId()) {
            throw new \Exception('error: Los datos de la tarea no son validos. [Owner task error]', 400);
        }
        $task->setTitle($title);
        $task->setDescription($description);
        $task->setStatus($status);
        $task->setUpdatedAt(new \DateTime("now"));
        $this->em->persist($task);
        $this->em->flush();
        $data = [
            'status' => 'success',
            'code' => 200,
            'msg' => 'Tarea actualizada.',
            'task' => $task,
        ];

        return $data;
    }

    public function updateStatus($token, $id)
    {
        $identity = $this->jwtAuth->checkToken($token);
        $this->getOne($token, $id);
        $task = $this->em->getRepository('AppBundle:Tasks')->findOneBy(['id' => $id]);
        if (!isset($identity->sub) || $identity->sub != $task->getUser()->getId()) {
            throw new \Exception('error: Los datos de la tarea no son validos. [Owner task error]', 400);
        }
        $status = $task->getStatus() === 'finished' ? 'todo' : 'finished';
        $task->setStatus($status);
        $task->setUpdatedAt(new \DateTime("now"));
        $this->em->persist($task);
        $this->em->flush();
        $data = [
            'status' => 'success',
            'code' => 200,
            'msg' => 'Tarea actualizada.',
            'task' => $task,
        ];

        return $data;
    }

    public function getOne($token, $id)
    {
        $identity = $this->jwtAuth->checkToken($token);
        $task = $this->em->getRepository('AppBundle:Tasks')->findOneBy(['id' => $id]);
        if ($task && is_object($task) && $identity->sub == $task->getUser()->getId()) {
            $data = [
                'status' => 'success',
                'code' => 200,
                'task' => $task,
            ];
        } else {
            throw new \Exception('error: Task not found.', 404);
        }

        return $data;
    }

    public function getAll($token, $page)
    {
        $identity = $this->jwtAuth->checkToken($token);
        $dql = "SELECT t FROM AppBundle:Tasks t WHERE t.user = $identity->sub ORDER BY t.id ASC";
        $query = $this->em->createQuery($dql);
        $itemsPerPage = 10;
        $pagination = $this->paginator->paginate($query, $page, $itemsPerPage);
        $totalItemsCount = $pagination->getTotalItemCount();
        $data = [
            'status' => 'success',
            'code' => 200,
            'totalItemsCount' => $totalItemsCount,
            'actual_page' => $page,
            'itemsPerPage' => $itemsPerPage,
            'totalPages' => ceil($totalItemsCount / $itemsPerPage),
            'tasks' => $pagination,
        ];

        return $data;
    }

    public function search($token, $filter, $order, $search, $page)
    {
        $identity = $this->jwtAuth->checkToken($token);
        $filter = $this->getFilter($filter);
        $order = $this->getOrder($order);
        $dql = "SELECT t FROM AppBundle:Tasks t WHERE t.user = $identity->sub ";
        if ($search !== null) {
            $dql.= " AND (t.title LIKE :search OR t.description LIKE :search) ";
        }
        if ($filter != null) {
            $dql.= " AND t.status = :filter ";
        }
        $dql.= " ORDER BY t.id $order ";
        $query = $this->em->createQuery($dql);
        if ($search !== null) {
            $query->setParameter('search', "%$search%");
        }
        if ($filter !== null) {
            $query->setParameter('filter', "$filter");
        }
        $itemsPerPage = 100;
        $task = $this->paginator->paginate($query, $page, $itemsPerPage);
        $totalItemsCount = $task->getTotalItemCount();
        $data = [
            'status' => 'success',
            'code' => 200,
            'totalItemsCount' => $totalItemsCount,
            'actual_page' => $page,
            'itemsPerPage' => $itemsPerPage,
            'totalPages' => ceil($totalItemsCount / $itemsPerPage),
            'data' => $task,
        ];

        return $data;
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

    public function delete($token, $id)
    {
        $identity = $this->jwtAuth->checkToken($token);
        $task = $this->em->getRepository('AppBundle:Tasks')->findOneBy(['id' => $id]);
        if ($task && is_object($task) && $identity->sub == $task->getUser()->getId()) {
            $this->em->remove($task);
            $this->em->flush();
        } else {
            throw new \Exception('error: Task not found.', 404);
        }
    }
}