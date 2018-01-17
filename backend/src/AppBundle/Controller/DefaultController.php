<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

class DefaultController extends BaseController
{
    const API_VERSION = '0.9.0';

    public function statusAction()
    {
        $data = [
            'api' => 'webapp',
            'status' => 'OK',
            'version' => self::API_VERSION,
        ];

        return $this->json($data);
    }

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
        $getData = isset($params->getData) ? $params->getData : null;
        $pwd = hash('sha256', $password);

        $user = $this->getDoctrine()->getManager()->getRepository('AppBundle:Users')
            ->findOneBy(['email' => $email, 'password' => $pwd]);

        if (empty($user)) {
            throw new \Exception('error: El email o password ingresado es incorrecto...', 403);
        }

        if ($getData === true) {
            $data = $this->getJwtService()->signUp($user, true);
        } else {
            $data = $this->getJwtService()->signUp($user, false);
        }

        return $data;
    }
}
