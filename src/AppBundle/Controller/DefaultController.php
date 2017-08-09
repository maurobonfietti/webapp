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
            $email = $params->email ? : null;
            $password = $params->password ? : null;
//            var_dump($email);
//            exit;
            $emailConstraint = new Assert\Email();
            $emailConstraint->message = "Email invalid...";
            $validateEmail = $this->get('validator')->validate($email, $emailConstraint);
//            var_dump($validateEmail);
//            exit;
            if ($email != null && count($validateEmail) == 0 && $password != null) {
                
                $jwtAuth = $this->get(JwtAuth::class);
                $signUp = $jwtAuth->signUp($email, $password);
                $data = [
                    'status' => 'success',
                    'data' => 'Email ok...',
                    'signUp' => $signUp,
                ]; 
            } else {
                $data = [
                    'status' => 'success',
                    'data' => 'Email or password incorrecto...',
                ]; 
            }

        }

        return $helpers->json($data);
    }

    /**
     * @Route("/pruebas", name="pruebas")
     */
    public function pruebasAction()
    {
        $em = $this->getDoctrine()->getManager();
        $userRepo = $em->getRepository('BackendBundle:User');
        $users = $userRepo->findAll();
        $response = [
            'status' => 'success',
            'users' => $users,
        ];

        $helpers = $this->get(Helpers::class);

        return $helpers->json($response);
    }
}
