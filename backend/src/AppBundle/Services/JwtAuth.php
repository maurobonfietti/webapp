<?php

namespace AppBundle\Services;

use Firebase\JWT\JWT;

class JwtAuth
{
    public $manager;

    public $key;

    public function __construct($manager)
    {
        $this->manager = $manager;
        $this->key = 'SecretKey...123...';
    }

    public function signUp($email, $password, $getHash = null)
    {
        $user = $this->manager->getRepository('BackendBundle:User')->findOneBy([
            'email' => $email,
            'password' => $password,
        ]);

        $signUp = false;
        if (is_object($user)) {
            $signUp = true;
        }

        if ($signUp == true) {
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
                'data' => 'Login Failed.',
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

        if ($getIdentity == false) {
            return $auth;
        } else {
            return $decoded;
        }
    }
}
