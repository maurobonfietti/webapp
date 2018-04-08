<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

class UserController extends BaseController
{
    public function createAction(Request $request)
    {
        try {
            $json = $request->get('json', null);
            $user = $this->getUserService()->create($json);

            return $this->response($user, 201);
        } catch (\Exception $e) {
            return $this->responseError($e);
        }
    }

    public function updateAction(Request $request)
    {
        try {
            $json = $request->get('json', null);
            $token = $request->headers->get('Authorization');
            $user = $this->getUserService()->update($token, $json);

            return $this->response($user);
        } catch (\Exception $e) {
            return $this->responseError($e);
        }
    }

    public function getAllAction(Request $request)
    {
        try {
            $token = $request->headers->get('Authorization');
            $users = $this->getUserService()->getAll($token);

            return $this->response($users);
        } catch (\Exception $e) {
            return $this->responseError($e);
        }
    }
}
