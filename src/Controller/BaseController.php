<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class BaseController extends AbstractController
{

    protected function serialize($object) {
        $encoders = array(new JsonEncoder());

        $getSetNormalizer = new GetSetMethodNormalizer();
        $callback = function ($innerObject) {
            return $innerObject instanceof \DateTime ? $innerObject->format(\DateTime::ISO8601) : '';
        };
        $getSetNormalizer->setCallbacks(['createdAt' => $callback]);

        $normalizers = array($getSetNormalizer);

        $serializer = new Serializer($normalizers, $encoders);

        return $serializer->serialize($object, 'json');
    }

}