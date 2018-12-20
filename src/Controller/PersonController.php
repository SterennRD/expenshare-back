<?php

namespace App\Controller;

use App\Entity\Person;
use App\Entity\ShareGroup;
use App\Form\PersonType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/person")
 */
class PersonController extends BaseController
{
    /**
     * @Route("/{slug}", name="person_index", methods="GET")
     */
    public function index(ShareGroup $group, Request $request): Response
    {
        $people = $this->getDoctrine()
            ->getRepository(Person::class)
            ->createQueryBuilder('p')
            ->select('p', 's')
            ->join('p.shareGroup', 's')
            ->where('s.id = :group')
            ->setParameter('group', $group)
            ->getQuery()
            ->getArrayResult();

            return $this->json($people);
    }
    /**
     * @Route("/group/{slug}", name="person", methods="GET")
     */
    public function index2(ShareGroup $shareGroup)
    {
        $persons = $this->getDoctrine()->getRepository(Person::class)
            ->createQueryBuilder('p')
            ->select('p', 'e')
            ->leftJoin('p.expenses', 'e')
            ->where('p.shareGroup = :group')
            ->orderBy('e.amount', 'DESC')
            ->setParameter(':group', $shareGroup)
            ->getQuery()
            ->getArrayResult()
        ;
        return $this->json($persons);
    }

    /**
     * @Route("/", name="person_new", methods="POST")
     */
    public function new(Request $request)
    {
        $data = $request->getContent();

        $jsonData = json_decode($data, true);
        $groupe = $this->getDoctrine()->getRepository(ShareGroup::class)->find($jsonData["sharegroup"]);

        $em = $this->getDoctrine()->getManager();

        $person = new Person();
        $person->setFirstname($jsonData["firstname"]);
        $person->setLastname($jsonData["lastname"]);
        $person->setShareGroup($groupe);

        $em->persist($person);
        $em->flush();



        return $this->json($this->serialize($person));
    }

    /**
     * @Route("/{id}", name="person_show", methods="GET")
     */
    public function show(Person $person): Response
    {
        return $this->render('person/show.html.twig', ['person' => $person]);
    }

    /**
     * @Route("/{id}/edit", name="person_edit", methods="GET|POST")
     */
    public function edit(Request $request, Person $person): Response
    {
        $form = $this->createForm(PersonType::class, $person);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('person_index', ['id' => $person->getId()]);
        }

        return $this->render('person/edit.html.twig', [
            'person' => $person,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/", name="person_delete", methods="DELETE")
     */
    public function delete(Request $request): Response
    {
        $data = $request->getContent();

        $jsonData = json_decode($data, true);
        $person = $this->getDoctrine()->getRepository(Person::class)->find($jsonData["id"]);

        $em = $this->getDoctrine()->getManager();
        $em->remove($person);
        $em->flush();



        return $this->json($this->serialize($person));
    }
}
