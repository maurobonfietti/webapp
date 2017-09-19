<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Services\JwtAuth;

class DefaultController extends BaseController
{
    public function loginAction(Request $request)
    {
        try {
            $json = $request->get('json', null);
            if ($json == null) {
                throw new \Exception('error: Sending data...', 403);
            }
            $params = json_decode($json);
            $data = $this->login($params);

            return $this->json($data);
        } catch (\Exception $e) {
            return $this->responseError($e);
        }
    }

    private function login($params)
    {
        $email = isset($params->email) ? $params->email : null;
        $password = isset($params->password) ? $params->password : null;
        $getHash = isset($params->getHash) ? $params->getHash : null;
        $emailConstraint = new Assert\Email();
        $validateEmail = $this->get('validator')->validate($email, $emailConstraint);
        $pwd = hash('sha256', $password);
        if ($email == null || count($validateEmail) > 0 || $password == null) {
            throw new \Exception('error: El email o el password es incorrecto...', 403);
        }
        $jwtAuth = $this->get(JwtAuth::class);
        if ($getHash == null || $getHash == false) {
            $data = $jwtAuth->signUp($email, $pwd);
        } else {
            $data = $jwtAuth->signUp($email, $pwd, true);
        }

        return $data;
    }
}
