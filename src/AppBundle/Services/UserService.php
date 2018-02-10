<?php

namespace AppBundle\Services;

use AppBundle\Services\JwtAuth;
use AppBundle\Repository\UserRepository;
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

        $this->validateCreateUser($email, $name, $surname, $password);
        $userRepository = new UserRepository($this->em);
        $user = $userRepository->create($email, $name, $surname, $password);
        $this->sendWelcomeEmail($user);

        return $user;
    }

    private function validateCreateUser($email, $name, $surname, $password)
    {
        $validateEmail = $this->validator->validate($email, new Assert\Email());
        if (empty($email) || empty($name) || empty($surname) || empty($password) || $validateEmail->count() > 0) {
            throw new \Exception('error: The data is invalid. The user was NOT created.', 400);
        }
        $checkUserExist = $this->em->getRepository('AppBundle:Users')->findBy(["email" => $email]);
        if ($checkUserExist) {
            throw new \Exception('error: The user already exists.', 400);
        }
    }

    public function sendWelcomeEmail($user)
    {
        $email = $user->getEmail();
        $nombre = $user->getName() . ' ' . $user->getSurName();
        $from = new \SendGrid\Email('ToDo List Web App', 'mauro.bonfietti@gmail.com');
        $subject = '¡Bienvenido! Te registraste correctamente';
        $to = new \SendGrid\Email($nombre, $email);
        $content = new \SendGrid\Content('text/html', 'Te damos la bienvenida a la aplicación...');
        $mail = new \SendGrid\Mail($from, $subject, $to, $content);

        $apiKey = '...';
        $sg = new \SendGrid($apiKey);

        $response = $sg->client->mail()->send()->post($mail);
        echo $response->statusCode();
        print_r($response->headers());
        echo $response->body();
    }

    public function update($token, $json)
    {
        $identity = $this->jwtAuth->checkToken($token);
        $params = json_decode($json);
        $email = isset($params->email) ? $params->email : null;
        $name = isset($params->name) ? $params->name : null;
        $surname = isset($params->surname) ? $params->surname : null;
        $password = isset($params->password) ? $params->password : null;

        $user = $this->getAndValidateUser($email, $name, $surname, $identity);
        $userRepository = new UserRepository($this->em);
        $data = $userRepository->update($user, $email, $name, $surname, $password);
//        $this->sendWelcomeEmail($user);

        return $data;
    }

    private function getAndValidateUser($email, $name, $surname, $identity)
    {
        $validateEmail = $this->validator->validate($email, new Assert\Email());
        if (empty($email) || empty($name) || empty($surname) || $validateEmail->count() > 0) {
            throw new \Exception('error: The data is invalid. The user was NOT updated.', 400);
        }
        $checkUserExist = $this->em->getRepository('AppBundle:Users')->findOneBy(["email" => $email]);
        if ($checkUserExist && $identity->email !== $email) {
            throw new \Exception('error: The user already exists.', 400);
        }

        return $this->em->getRepository('AppBundle:Users')->findOneBy(["id" => $identity->sub]);
    }
}
