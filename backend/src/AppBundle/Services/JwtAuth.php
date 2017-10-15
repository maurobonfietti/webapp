<?php

namespace AppBundle\Services;

use Firebase\JWT\JWT;
use Doctrine\ORM\EntityManager;

class JwtAuth
{
    /** @var EntityManager */
    public $em;

    /** @var string */
    public $key;

    public function __construct($manager)
    {
        $this->em = $manager;
        $this->key = 'SecretKey...123...';
    }

    public function signUp($email, $password, $getHash = null)
    {
        $user = $this->em->getRepository('AppBundle:Users')->findOneBy([
            'email' => $email,
            'password' => $password,
        ]);
        if (is_object($user)) {
            $token = [
                'sub' => $user->getId(),
                'email' => $user->getEmail(),
                'name' => $user->getName(),
                'surname' => $user->getSurname(),
                'iat' => time(),
                'exp' => time() + (7 * 24 * 60 * 60),
            ];
            $jwt = JWT::encode($token, $this->key, 'HS256');
            $decoded = JWT::decode($jwt, $this->key, ['HS256']);
            if ($getHash == null) {
                $data = $jwt;
            } else {
                $data = $decoded;
            }
        } else {
            $data = [
                'status' => 'error',
                'data' => 'Error en inicio de sesion.',
            ];
        }

        return $data;
    }

    public function checkToken($jwt, $getIdentity = false)
    {
        $auth = false;
        try {
            $decoded = JWT::decode($jwt, $this->key, ['HS256']);
        } catch (\UnexpectedValueException $e) {
            $auth = false;
        } catch (\DomainException $e) {
            $auth = false;
        }
        if (isset($decoded) && is_object($decoded) && isset($decoded->sub)) {
            $auth = true;
        }
        if ($getIdentity === false) {
            return $auth;
        } else {
            return $decoded;
        }
    }
}
