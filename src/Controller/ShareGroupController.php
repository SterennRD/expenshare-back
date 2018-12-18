<?php

namespace App\Controller;

use App\Entity\ShareGroup;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ShareGroupController
 * @package App\Controller
 * @Route("/sharegroup")
 */
class ShareGroupController extends BaseController
{

    /**
     * @Route("/{slug}", name="sharegroup_get", methods="GET")
     */
    public function index(ShareGroup $shareGroup)
    {
        return $this->json($this->serialize($shareGroup));
    }

    /**
     * @Route("/", name="sharegroup_new", methods="POST")
     */
    public function new(Request $request)
    {
        $data = $request->getContent();

        $jsonData = json_decode($data, true);

        $em = $this->getDoctrine()->getManager();

        $sharegroup = new ShareGroup();
        $sharegroup->setSlug($jsonData["slug"]);
        $sharegroup->setCreatedAt(new \DateTime());
        $sharegroup->setClosed(false);

        $em->persist($sharegroup);
        $em->flush();

        return $this->json($this->serialize($sharegroup));
    }

}
