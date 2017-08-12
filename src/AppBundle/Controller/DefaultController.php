<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Services\Helpers;
use AppBundle\Services\JwtAuth;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
        ]);
    }

    public function loginAction(Request $request)
    {
        $helpers = $this->get(Helpers::class);
        $json = $request->get('json', null);

        $data = [
            'status' => 'error',
            'data' => 'Sending data...',
        ];

        if ($json != null) {
            $params = json_decode($json);
            $email = isset($params->email) ? $params->email : null;
            $password = isset($params->password) ? $params->password : null;
            $getHash = isset($params->getHash) ? $params->getHash : null;

            $emailConstraint = new Assert\Email();
            $emailConstraint->message = "Email invalid...";
            $validateEmail = $this->get('validator')->validate($email, $emailConstraint);

            $pwd = hash('sha256', $password);

            if ($email != null && count($validateEmail) == 0 && $password != null) {
                $jwtAuth = $this->get(JwtAuth::class);
                
                if ($getHash == null || $getHash ==false) {
                    $signUp = $jwtAuth->signUp($email, $pwd);
                } else {
                    $signUp = $jwtAuth->signUp($email, $pwd, true);
                }
                
                return $this->json($signUp);
            } else {
                $data = [
                    'status' => 'success',
                    'data' => 'Email or password incorrecto...',
                ];
            }
        }

        return $helpers->json($data);
    }

    public function pruebasAction(Request $request)
    {
        $helpers = $this->get(Helpers::class);
        $jwtAuth = $this->get(JwtAuth::class);
        
        $token = $request->get('authorization', null);

        if ($token && $jwtAuth->checkToken($token) == true) {
            $em = $this->getDoctrine()->getManager();
            $userRepo = $em->getRepository('BackendBundle:User');
            $users = $userRepo->findAll();
            $response = [
                'status' => 'success',
                'users' => $users,
            ];
        } else {
            $response = [
                'status' => 'error',
                'data' => 'Authorization Invalid.',
                'code' =>400,
            ];
        }

        

        return $helpers->json($response);
    }
}
