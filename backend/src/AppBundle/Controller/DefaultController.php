<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

class DefaultController extends BaseController
{
    public function loginAction(Request $request)
    {
        try {
            $json = $request->get('json', null);
            $data = $this->login($json);

            return $this->json($data);
        } catch (\Exception $e) {
            return $this->responseError($e);
        }
    }

    private function login($json)
    {
        if ($json === null) {
            throw new \Exception('error: Datos incompletos...', 403);
        }
        $params = json_decode($json);
        $email = isset($params->email) ? $params->email : null;
        $password = isset($params->password) ? $params->password : null;
        $getHash = isset($params->getHash) ? $params->getHash : null;
        $pwd = hash('sha256', $password);
        $user = $this->getDoctrine()->getManager()->getRepository('AppBundle:Users')
            ->findOneBy(['email' => $email, 'password' => $pwd]);
        if (empty($user)) {
            throw new \Exception('error: El email o password ingresado es incorrecto...', 403);
        }
        if ($getHash === null || $getHash === false) {
            $data = $this->getJwtService()->signUp($user);
        } else {
            $data = $this->getJwtService()->signUp($user, true);
        }

        return $data;
    }
}
