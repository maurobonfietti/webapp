<?php

namespace AppBundle\Controller;

use AppBundle\Services\JwtAuth;
use Symfony\Component\HttpFoundation\Request;

class UserController extends BaseController
{
    public function createAction(Request $request)
    {
        try {
            $this->getUserService();
            $json = $request->get('json', null);
            $user = $this->userService->create($json);

            return $this->response($user);
        } catch (\Exception $e) {
            return $this->responseError($e);
        }
    }

    public function updateAction(Request $request)
    {
        try {
            $this->getUserService();
            $jwtAuth = $this->get(JwtAuth::class);
            $token = $request->get('authorization', null);
            $json = $request->get('json', null);
            $user = $this->userService->update($json, $jwtAuth, $token);

            return $this->response($user);
        } catch (\Exception $e) {
            return $this->responseError($e);
        }
    }
}
