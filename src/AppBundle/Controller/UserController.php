<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;
use BackendBundle\Entity\User;
use AppBundle\Services\Helpers;

class UserController extends Controller
{
    public function newAction(Request $request)
    {
        $helpers = $this->get(Helpers::class);
        
        $json = $request->get('json', null);
        
        $params = json_decode($json);

        $data = [
            'status' => 'error',
            'code' => 400,
            'msg' => 'User Not Created.',
        ];
        
        if ($json != null) {
            $createdAt = new \DateTime("now");
            $role = 'user';
            $email = isset($params->email) ? $params->email : null;
            $name = isset($params->name) ? $params->name : null;
            $surname = isset($params->surname) ? $params->surname : null;
            $password = isset($params->password) ? $params->password : null;

            $emailConstraint = new Assert\Email();
            $validateEmail = $this->get('validator')->validate($email, $emailConstraint);
            
            if ($email != null && count($validateEmail) == 0 && $name != null && $password != null && $surname != null) {
                
                $user = new User();
                $user->setCreatedAt($createdAt);
                $user->setRole($role);
                $user->setEmail($email);
                $user->setName($name);
                $user->setPassword($password);
                
                $em = $this->getDoctrine()->getManager();
                $issetUser = $em->getRepository('BackendBundle:User')
                    ->findBy(["email" => $email]);

                if (count($issetUser) == 0) {
                    $em->persist($user);
                    $em->flush();
                    
                    $data = [
                        'status' => 'success',
                        'code' => 200,
                        'msg' => 'User Created.',
                        'user' => $user,
                    ];
                } else {
                    $data = [
                        'status' => 'error',
                        'code' => 400,
                        'msg' => 'User exists.',
                    ];
                }
            }
        }
        
        return $helpers->json($data);
    }
}
