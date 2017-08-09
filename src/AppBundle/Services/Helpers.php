<?php

namespace AppBundle\Services;

use \Symfony\Component\Serializer;

class Helpers
{
    public $manager;

    public function __construct($manager)
    {
        $this->manager = $manager;
    }

    public function json($data)
    {
        $normalizers = [ new Serializer\Normalizer\GetSetMethodNormalizer()];
        $encoders = ['json' => new Serializer\Encoder\JsonEncoder()];

        $serializer = new Serializer\Serializer($normalizers, $encoders);
        $json = $serializer->serialize($data, 'json');

        $response = new \Symfony\Component\HttpFoundation\Response();
        $response->setContent($json);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}