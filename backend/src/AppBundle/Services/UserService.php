<?php

namespace AppBundle\Services;

use AppBundle\Entity\Users;
use Symfony\Component\Validator\Constraints as Assert;

class UserService
{
    public $manager;

    public $validator;

    public function __construct($manager, $validator)
    {
        $this->manager = $manager;
        $this->validator = $validator;
    }

    public function create($json)
    {
        $params = json_decode($json);
        $email = isset($params->email) ? $params->email : null;
        $name = isset($params->name) ? $params->name : null;
        $surname = isset($params->surname) ? $params->surname : null;
        $password = isset($params->password) ? $params->password : null;
        $emailConstraint = new Assert\Email();
        $validateEmail = $this->validator->validate($email, $emailConstraint);
        if ($email == null || count($validateEmail) > 0 || $name == null || $password == null || $surname == null) {
            throw new \Exception('error: Usuario no creado.', 400);
        }
        $this->checkUserExist($email);
        $user = $this->createUser($email, $name, $surname, $password);

        return $user;
    }

    private function checkUserExist($email)
    {
        $user = $this->manager->getRepository('AppBundle:Users')->findBy(["email" => $email]);
        if (count($user) > 0) {
            throw new \Exception('error: Usuario existente.', 400);
        }
    }

    private function createUser($email, $name, $surname, $password)
    {
        $user = new Users();
        $user->setCreatedAt(new \DateTime("now"));
        $user->setRole('user');
        $user->setEmail($email);
        $user->setName($name);
        $user->setSurname($surname);
        $pwd = hash('sha256', $password);
        $user->setPassword($pwd);
        $this->manager->persist($user);
        $this->manager->flush();
        $data = [
            'status' => 'success',
            'code' => 200,
            'msg' => 'Usuario creado.',
            'user' => $user,
        ];

        return $data;
    }

    public function update($json, $token, $jwtAuth)
    {
        $authCheck = $jwtAuth->checkToken($token);
        if (!$authCheck) {
            throw new \Exception('error: Authorization Invalid.', 403);
        }
        $identity = $jwtAuth->checkToken($token, true);
        $user = $this->manager->getRepository('AppBundle:Users')->findOneBy(["id" => $identity->sub]);
        $params = json_decode($json);
        $email = isset($params->email) ? $params->email : null;
        $name = isset($params->name) ? $params->name : null;
        $surname = isset($params->surname) ? $params->surname : null;
        $password = isset($params->password) ? $params->password : null;
        $emailConstraint = new Assert\Email();
        $validateEmail = $this->validator->validate($email, $emailConstraint);
        if ($email == null || count($validateEmail) > 0 || $name == null || $surname == null) {
            throw new \Exception('error: Usuario no actualizado.', 400);
        }
        $this->checkUserExistUpdate($email, $identity);
        $data = $this->updateUser($user, $email, $name, $surname, $password);

        return $data;
    }

    private function checkUserExistUpdate($email, $identity)
    {
        $issetUser = $this->manager->getRepository('AppBundle:Users')->findBy(["email" => $email]);
        if (count($issetUser) > 0 && $identity->email != $email) {
            throw new \Exception('error: Usuario existente.', 400);
        }
    }

    private function updateUser($user, $email, $name, $surname, $password)
    {
        $user->setEmail($email);
        $user->setName($name);
        $user->setSurname($surname);
        if ($password != null) {
            $pwd = hash('sha256', $password);
            $user->setPassword($pwd);
        }
        $this->manager->persist($user);
        $this->manager->flush();
        $data = [
            'status' => 'success',
            'code' => 200,
            'msg' => 'Usuario actualizado.',
            'user' => $user,
        ]; 

        return $data;
    }
}
