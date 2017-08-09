<?php

namespace AppBundle\Services;

use Firebase\JWT\JWT;

class JwtAuth
{
    public $manager;
    
    public function __construct($manager) {
        $this->manager = $manager;
    }

    public function signUp($email, $password)
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
            $data = [
                'status' => 'success',
                'user' => $user,
            ];
        } else {
            $data = [
                'status' => 'error',
                'data' => 'Login Failed.',
            ];
        }
        
        return $data;
    }
}