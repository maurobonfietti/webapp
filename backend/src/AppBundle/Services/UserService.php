<?php

namespace AppBundle\Services;

use AppBundle\Entity\Users;
use AppBundle\Services\JwtAuth;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserService
{
    /** @var EntityManager */
    public $em;

    /** @var JwtAuth */
    public $jwtAuth;

    /** @var ValidatorInterface */
    public $validator;

    public function __construct($manager, $jwtAuth, $validator)
    {
        $this->em = $manager;
        $this->jwtAuth = $jwtAuth;
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
        if ($email === null || count($validateEmail) > 0 || $name === null || $password === null || $surname === null) {
            throw new \Exception('error: Usuario no creado.', 400);
        }
        $this->checkUserExist($email);
        $user = $this->createUser($email, $name, $surname, $password);

        return $user;
    }

    private function checkUserExist($email)
    {
        $user = $this->em->getRepository('AppBundle:Users')->findBy(["email" => $email]);
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
        $this->em->persist($user);
        $this->em->flush();
        $data = [
            'status' => 'success',
            'code' => 201,
            'msg' => 'Usuario creado.',
            'user' => $user,
        ];

        return $data;
    }

    public function update($json, $token)
    {
        $identity = $this->jwtAuth->checkToken($token);
        $user = $this->em->getRepository('AppBundle:Users')->findOneBy(["id" => $identity->sub]);
        $params = json_decode($json);
        $email = isset($params->email) ? $params->email : null;
        $name = isset($params->name) ? $params->name : null;
        $surname = isset($params->surname) ? $params->surname : null;
        $password = isset($params->password) ? $params->password : null;
        $emailConstraint = new Assert\Email();
        $validateEmail = $this->validator->validate($email, $emailConstraint);
        if ($email === null || count($validateEmail) > 0 || $name === null || $surname === null) {
            throw new \Exception('error: Usuario no actualizado.', 400);
        }
        $this->checkUserExistUpdate($email, $identity);
        $data = $this->updateUser($user, $email, $name, $surname, $password);

        return $data;
    }

    private function checkUserExistUpdate($email, $identity)
    {
        $issetUser = $this->em->getRepository('AppBundle:Users')->findBy(["email" => $email]);
        if (count($issetUser) > 0 && $identity->email != $email) {
            throw new \Exception('error: Usuario existente.', 400);
        }
    }

    private function updateUser($user, $email, $name, $surname, $password)
    {
        $user->setEmail($email);
        $user->setName($name);
        $user->setSurname($surname);
        if ($password !== null) {
            $pwd = hash('sha256', $password);
            $user->setPassword($pwd);
        }
        $this->em->persist($user);
        $this->em->flush();
        $data = [
            'status' => 'success',
            'code' => 200,
            'msg' => 'Usuario actualizado.',
            'user' => $user,
        ];

        return $data;
    }
}
