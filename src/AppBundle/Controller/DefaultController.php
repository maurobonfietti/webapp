<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

class DefaultController extends BaseController
{
    const API_NAME = 'todo-list-back';

    const API_VERSION = '1.0.0';

    public function statusAction()
    {
        $data = [
            'api' => self::API_NAME,
            'env' => getenv('ENV'),
            'version' => self::API_VERSION,
            'status' => 'OK',
        ];

        return $this->json($data);
    }

    public function testAction()
    {
        $users = $this->getDoctrine()->getManager()
                ->getRepository('AppBundle:Users')
                ->findAll();

        $tasks = $this->getDoctrine()->getManager()
                ->getRepository('AppBundle:Tasks')
                ->findAll();

        $data = [
            'api' => self::API_NAME,
            'env' => getenv('ENV'),
            'version' => self::API_VERSION,
            'status' => 'OK',
            'database' => 'OK',
            'users' => count($users),
            'tasks' => count($tasks),
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
