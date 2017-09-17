<?php

namespace AppBundle\Controller;

use AppBundle\Services\Helpers;
use AppBundle\Services\JwtAuth;
use AppBundle\Services\UserService;
use Symfony\Component\HttpFoundation\Request;

class UserController extends BaseController
{
    public function createAction(Request $request)
    {
        try {
            $em = $this->getDoctrine()->getManager();
            $json = $request->get('json', null);
            $validator = $this->get('validator');
            $userService = new UserService();
            $data = $userService->create($json, $validator, $em);

            return $this->get(Helpers::class)->json($data);
        } catch (\Exception $e) {
            return $this->get(Helpers::class)->json($e->getMessage(), $e->getCode());
        }
    }

    public function updateAction(Request $request)
    {
        try {
            $em = $this->getDoctrine()->getManager();
            $jwtAuth = $this->get(JwtAuth::class);
            $token = $request->get('authorization', null);
            $json = $request->get('json', null);
            $validator = $this->get('validator');
            $userService = new UserService();
            $data = $userService->update($json, $validator, $em, $jwtAuth, $token);

            return $this->get(Helpers::class)->json($data);
        } catch (\Exception $e) {
            return $this->get(Helpers::class)->json($e->getMessage(), $e->getCode());
        }
    }
}
