<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Entity\Users;
use AppBundle\Services\Helpers;
use AppBundle\Services\JwtAuth;

class UserController extends BaseController
{
    public function newAction(Request $request)
    {
        try {
            $json = $request->get('json', null);
            $params = json_decode($json);
//            $status = 400;
//            $data = [
//                'status' => 'error',
//                'code' => $status,
//                'msg' => 'User Not Created.',
//            ];
            if ($json == null) {
                throw new \Exception('error: User Not Created.', 400);
            }
//            if ($json != null) {
            $createdAt = new \DateTime("now");
            $role = 'user';
            $email = isset($params->email) ? $params->email : null;
            $name = isset($params->name) ? $params->name : null;
            $surname = isset($params->surname) ? $params->surname : null;
            $password = isset($params->password) ? $params->password : null;
            $emailConstraint = new Assert\Email();
            $validateEmail = $this->get('validator')->validate($email, $emailConstraint);
            if ($email != null && count($validateEmail) == 0 && $name != null && $password != null && $surname != null) {
                $user = new Users();
                $user->setCreatedAt($createdAt);
                $user->setRole($role);
                $user->setEmail($email);
                $user->setName($name);
                $user->setSurname($surname);
                $pwd = hash('sha256', $password);
                $user->setPassword($pwd);
                $em = $this->getDoctrine()->getManager();
                $issetUser = $em->getRepository('AppBundle:Users')->findBy(["email" => $email]);
                if (count($issetUser) == 0) {
                    $em->persist($user);
                    $em->flush();
                    $status = 200;
                    $data = [
                        'status' => 'success',
                        'code' => $status,
                        'msg' => 'User Created.',
                        'user' => $user,
                    ];
                } else {
                    throw new \Exception('error: User exists.', 400);
                }
            }
//            }

            return $this->get(Helpers::class)->json($data, $status);
        } catch (\Exception $e) {
            return $this->get(Helpers::class)->json($e->getMessage(), $e->getCode());
        }
    }

    public function editAction(Request $request)
    {
        $jwtAuth = $this->get(JwtAuth::class);
        $token = $request->get('authorization', null);
        $authCheck = $jwtAuth->checkToken($token);
        if ($authCheck == true) {
            $em = $this->getDoctrine()->getManager();
            $identity = $jwtAuth->checkToken($token, true);
            $user = $em->getRepository('AppBundle:Users')->findOneBy(["id" => $identity->sub]);
            $json = $request->get('json', null);
            $params = json_decode($json);
            $status = 400;
            $data = [
                'status' => 'error',
                'code' => $status,
                'msg' => 'User Not Edited.',
            ];
            if ($json != null) {
                $role = 'user';
                $email = isset($params->email) ? $params->email : null;
                $name = isset($params->name) ? $params->name : null;
                $surname = isset($params->surname) ? $params->surname : null;
                $password = isset($params->password) ? $params->password : null;
                $emailConstraint = new Assert\Email();
                $validateEmail = $this->get('validator')->validate($email, $emailConstraint);
                if ($email != null && count($validateEmail) == 0 && $name != null && $surname != null) {
                    $user->setRole($role);
                    $user->setEmail($email);
                    $user->setName($name);
                    $user->setSurname($surname);
                    if ($password != null) {
                        $pwd = hash('sha256', $password);
                        $user->setPassword($pwd);
                    }
                    $issetUser = $em->getRepository('AppBundle:Users')->findBy(["email" => $email]);
                    if (count($issetUser) == 0 || $identity->email == $email) {
                        $em->persist($user);
                        $em->flush();
                        $status = 200;
                        $data = [
                            'status' => 'success',
                            'code' => $status,
                            'msg' => 'User Updated.',
                            'user' => $user,
                        ];
                    } else {
                        $status = 400;
                        $data = [
                            'status' => 'error',
                            'code' => $status,
                            'msg' => 'User exists.',
                        ];
                    }
                }
            }
        } else {
            $status = 403;
            $data = [
                'status' => 'error',
                'code' => $status,
                'msg' => 'Authorization Invalid.',
            ];
        }

        return $this->get(Helpers::class)->json($data, $status);
    }
}
