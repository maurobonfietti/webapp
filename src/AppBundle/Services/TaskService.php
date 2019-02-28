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
        $priority = isset($params->priority) ? $params->priority : null;
        if ($userId === null || empty($title)) {
            throw new \Exception('error: Los datos de la tarea no son validos.', 400);
        }
        $user = $this->em->getRepository('AppBundle:Users')->findOneBy(['id' => $userId]);
        if ($id === null) {
            $data = $this->createTask($user, $title, $description, $status, $priority);
        } else {
            $this->getOne($token, $id);
            $data = $this->updateTask($id, $title, $description, $status, $priority);
        }

        return $data;
    }

    private function createTask($user, $title, $description, $status, $priority)
    {
        $task = new Tasks();
        $task->setUser($user);
        $task->setTitle($title);
        $task->setDescription($description);
        $task->setStatus($status);
        $task->setPriority($priority);
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

    private function updateTask($id, $title, $description, $status, $priority)
    {
        $task = $this->em->getRepository('AppBundle:Tasks')->findOneBy(['id' => $id]);
        $task->setTitle($title);
        $task->setDescription($description);
        $task->setStatus($status);
        $task->setPriority($priority);
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
        $this->jwtAuth->checkToken($token);
        $this->getOne($token, $id);
        $task = $this->em->getRepository('AppBundle:Tasks')->findOneBy(['id' => $id]);
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

    public function updatePriority($token, $id)
    {
        $this->jwtAuth->checkToken($token);
        $this->getOne($token, $id);
        $task = $this->em->getRepository('AppBundle:Tasks')->findOneBy(['id' => $id]);
        $priority = $task->getPriority() === true ? false : true;
        $task->setPriority($priority);
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

    public function search($token, $filter, $order, $search, $page, $priority)
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
        if ($priority === 1) {
            $dql.= " AND t.priority = 1 ";
        }
        if ($priority === 0) {
            $dql.= " AND (t.priority IS NULL OR t.priority = 0)";
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
        $stats = $this->getStats($token);

        return [
            'status' => 'success',
            'code' => 200,
            'totalItemsCount' => $totalItemsCount,
            'actual_page' => $page,
            'itemsPerPage' => $itemsPerPage,
            'totalPages' => ceil($totalItemsCount / $itemsPerPage),
            'stats' => $stats,
            'data' => $task,
        ];
    }

    private function getFilter($filter)
    {
        if (empty($filter)) {
            $filterStr = null;
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

    public function getStats($token)
    {
        $identity = $this->jwtAuth->checkToken($token);
        $qb = $this->em->createQueryBuilder();
        $qb->select('count(t.id)');
        $qb->from('AppBundle:Tasks', 't');
        $qb->where('t.user = :user');
        $qb->andWhere('t.status = :status');
        $qb->setParameter('user', $identity->sub);
        $qb->setParameter('status', 'todo');
        $todo = $qb->getQuery()->getSingleScalarResult();

        $qb2 = $this->em->createQueryBuilder();
        $qb2->select('count(t.id)');
        $qb2->from('AppBundle:Tasks', 't');
        $qb2->where('t.user = :user');
        $qb2->andWhere('t.status = :status');
        $qb2->setParameter('user', $identity->sub);
        $qb2->setParameter('status', 'finished');
        $done = $qb2->getQuery()->getSingleScalarResult();

        return [
            'todo' => (int) $todo,
            'done' => (int) $done,
            'total' => $todo + $done,
        ];
    }
}
