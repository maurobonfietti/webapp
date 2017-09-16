<?php

namespace AppBundle\Services;

use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Entity\Users;

class UserService
{
    public function create($json, $validator, $em)
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
            throw new \Exception('error: User Not Created.....!', 400);
        }
        $user = new Users();
        $user->setCreatedAt($createdAt);
        $user->setRole($role);
        $user->setEmail($email);
        $user->setName($name);
        $user->setSurname($surname);
        $pwd = hash('sha256', $password);
        $user->setPassword($pwd);
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

        return $data;
    }
}
