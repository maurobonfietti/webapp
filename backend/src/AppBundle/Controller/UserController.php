<?php

namespace AppBundle\Controller;

use AppBundle\Services\Helpers;
use AppBundle\Services\JwtAuth;
use Symfony\Component\HttpFoundation\Request;

class UserController extends BaseController
{
    public function createAction(Request $request)
    {
        try {
            $this->getUserService();
            $json = $request->get('json', null);
            $data = $this->userService->create($json);

            return $this->get(Helpers::class)->json($data);
        } catch (\Exception $e) {
            return $this->get(Helpers::class)->json($e->getMessage(), $e->getCode());
        }
    }

    public function updateAction(Request $request)
    {
        try {
            $this->getUserService();
            $jwtAuth = $this->get(JwtAuth::class);
            $token = $request->get('authorization', null);
            $json = $request->get('json', null);
            $data = $this->userService->update($json, $jwtAuth, $token);

            return $this->get(Helpers::class)->json($data);
        } catch (\Exception $e) {
            return $this->get(Helpers::class)->json($e->getMessage(), $e->getCode());
        }
    }
}
