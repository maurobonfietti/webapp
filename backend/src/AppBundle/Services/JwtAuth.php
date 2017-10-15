<?php

namespace AppBundle\Services;

use Firebase\JWT\JWT;

class JwtAuth
{
    public $key;

    public function __construct()
    {
        $this->key = 'SecretKey...123...';
    }

    public function signUp($user, $getData = false)
    {
        $token = [
            'sub' => $user->getId(),
            'email' => $user->getEmail(),
            'name' => $user->getName(),
            'surname' => $user->getSurname(),
            'iat' => time(),
            'exp' => time() + (7 * 24 * 60 * 60),
        ];

        if ($getData === true) {
            $data = $token;
        } else {
            $data = JWT::encode($token, $this->key, 'HS256');
        }

        return $data;
    }

    public function checkToken($token)
    {
        $auth = false;

        try {
            $decoded = JWT::decode($token, $this->key, ['HS256']);
        } catch (\UnexpectedValueException $e) {
            $auth = false;
        } catch (\DomainException $e) {
            $auth = false;
        }

        if (isset($decoded) && is_object($decoded) && isset($decoded->sub)) {
            $auth = true;
        }

        if ($auth === false) {
            throw new \Exception('error: Sin Autorizacion.', 403);
        }

        return $decoded;
    }
}
