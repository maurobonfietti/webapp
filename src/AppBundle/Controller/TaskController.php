<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use BackendBundle\Entity\Task;
use AppBundle\Services\Helpers;
use AppBundle\Services\JwtAuth;

class TaskController extends Controller
{
    public function newAction(Request $request, $id = null)
    {
        $helpers = $this->get(Helpers::class);
        $jwtAuth = $this->get(JwtAuth::class);

        $token = $request->get('authorization', null);
        $authCheck = $jwtAuth->checkToken($token);

        if ($authCheck == true) {
            $identity = $jwtAuth->checkToken($token, true);

            $json = $request->get('json', null);

            if ($json != null) {
                $params = json_decode($json);

                $createdAt = new \DateTime("now");
                $updateAt = new \DateTime("now");

                $userId = ($identity->sub != null) ? $identity->sub : null;
                $title = isset($params->title) ? $params->title : null;
                $description = isset($params->description) ? $params->description : null;
                $status = isset($params->status) ? $params->status : null;

                if ($userId != null && $title != null) {
                    $em = $this->getDoctrine()->getManager();

                    $user = $em->getRepository('BackendBundle:User')
                        ->findOneBy(['id' => $userId]);

                    if ($id == null) {
                        $task = new Task();
                        $task->setUser($user);
                        $task->setTitle($title);
                        $task->setDescription($description);
                        $task->setStatus($status);
                        $task->setCreatedAt($createdAt);
                        $task->setUpdatedAt($updateAt);
                        
                        $em->persist($task);
                        $em->flush();

                        $data = [
                            'status' => 'success',
                            'code' => 200,
                            'msg' => 'Task Created.',
                            'task' => $task,
                        ];
                        
                    } else {
                        $task = $em->getRepository('BackendBundle:Task')
                            ->findOneBy(['id' => $id]);
                        
                        if (isset($identity->sub) && $identity->sub == $task->getUser()->getId()) {
                            $task->setTitle($title);
                            $task->setDescription($description);
                            $task->setStatus($status);
                            $task->setUpdatedAt($updateAt);

                            $em->persist($task);
                            $em->flush();
                        
                            $data = [
                                'status' => 'success',
                                'code' => 200,
                                'msg' => 'Task Updated.',
                                'task' => $task,
                            ];
                        } else {
                            $data = [
                                'status' => 'error',
                                'code' => 400,
                                'msg' => 'Task validation failed. Owner task error.',
                           ];
                        }
                    }
                    



                } else {
                    $data = [
                        'status' => 'error',
                        'code' => 400,
                        'msg' => 'Task validation failed.',
                   ];
                }
            } else {
                $data = [
                    'status' => 'error',
                    'code' => 400,
                    'msg' => 'Task  ....  exists.',
                ];
            }
        } else {
            $data = [
                'status' => 'error',
                'code' => 400,
                'msg' => 'Authorization Invalid.',
            ];
        }

        return $helpers->json($data);
    }
}
