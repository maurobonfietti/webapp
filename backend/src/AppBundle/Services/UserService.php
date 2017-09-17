<?php

namespace AppBundle\Services;

use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Entity\Users;

class UserService
{
    public $manager;

    public function __construct($manager)
    {
        $this->manager = $manager;
    }

    public function create($json, $validator)
    {
        $params = json_decode($json);
        $createdAt = new \DateTime("now");
        $role = 'user';
        $email = isset($params->email) ? $params->email : null;
        $name = isset($params->name) ? $params->name : null;
        $surname = isset($params->surname) ? $params->surname : null;
        $password = isset($params->password) ? $params->password : null;
        $emailConstraint = new Assert\Email();
        $validateEmail = $validator->validate($email, $emailConstraint);
        if ($email == null || count($validateEmail) > 0 || $name == null || $password == null || $surname == null) {
            throw new \Exception('error: User Not Created.', 400);
        }
        $user = new Users();
        $user->setCreatedAt($createdAt);
        $user->setRole($role);
        $user->setEmail($email);
        $user->setName($name);
        $user->setSurname($surname);
        $pwd = hash('sha256', $password);
        $user->setPassword($pwd);
        $issetUser = $this->manager->getRepository('AppBundle:Users')->findBy(["email" => $email]);
        if (count($issetUser) == 0) {
            $this->manager->persist($user);
            $this->manager->flush();
            $data = [
                'status' => 'success',
                'code' => 200,
                'msg' => 'User Created.',
                'user' => $user,
            ];
        } else {
            throw new \Exception('error: User exists.', 400);
        }

        return $data;
    }

    public function update($json, $validator, $jwtAuth, $token)
    {
        $authCheck = $jwtAuth->checkToken($token);
        if (!$authCheck) {
            throw new \Exception('error: Authorization Invalid.', 403);
        }
        $identity = $jwtAuth->checkToken($token, true);
        $user = $this->manager->getRepository('AppBundle:Users')->findOneBy(["id" => $identity->sub]);
        $params = json_decode($json);
        $role = 'user';
        $email = isset($params->email) ? $params->email : null;
        $name = isset($params->name) ? $params->name : null;
        $surname = isset($params->surname) ? $params->surname : null;
        $password = isset($params->password) ? $params->password : null;
        $emailConstraint = new Assert\Email();
        $validateEmail = $validator->validate($email, $emailConstraint);
        if ($email == null || count($validateEmail) > 0 || $name == null || $surname == null) {
            throw new \Exception('error: User Not Edited.', 400);
        }
        $user->setRole($role);
        $user->setEmail($email);
        $user->setName($name);
        $user->setSurname($surname);
        if ($password != null) {
            $pwd = hash('sha256', $password);
            $user->setPassword($pwd);
        }
        $issetUser = $this->manager->getRepository('AppBundle:Users')->findBy(["email" => $email]);
        if (count($issetUser) == 0 || $identity->email == $email) {
            $this->manager->persist($user);
            $this->manager->flush();
            $data = [
                'status' => 'success',
                'code' => 200,
                'msg' => 'User Updated.',
                'user' => $user,
            ];
        } else {
            throw new \Exception('error: User exists.', 400);
        }

        return $data;
    }
}
