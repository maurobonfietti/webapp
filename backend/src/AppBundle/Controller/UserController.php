<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

class UserController extends BaseController
{
    public function createAction(Request $request)
    {
        try {
            $this->getUserService();
            $json = $request->get('json', null);
            $user = $this->userService->create($json);

            return $this->response($user, 201);
        } catch (\Exception $e) {
            return $this->responseError($e);
        }
    }

    public function updateAction(Request $request)
    {
        try {
            $this->getUserService();
            $json = $request->get('json', null);
            $token = $request->headers->get('Authorization');
            $user = $this->userService->update($json, $token);

            return $this->response($user);
        } catch (\Exception $e) {
            return $this->responseError($e);
        }
    }
}
