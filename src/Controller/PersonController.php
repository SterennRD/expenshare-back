<?php

namespace App\Controller;

use App\Entity\Person;
use App\Entity\ShareGroup;
use App\Form\PersonType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/person")
 */
class PersonController extends AbstractController
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


        if ($request->isXmlHttpRequest()) {
            return $this->json($people);
        } else {

        }
    }

    /**
     * @Route("/new", name="person_new", methods="GET|POST")
     */
    public function new(Request $request): Response
    {
        if ($request->isXmlHttpRequest()) {
            $person = new Person();
            $em = $this->getDoctrine()->getManager();
            $person->setFirstname($request->get('firstname'));
            $person->setShareGroup($request->get('sharegroup'));
            $person->setLastname($request->get('firstname'));
            $em->persist($person);
            $em->flush();
            return $this->json($person);
        }
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
     * @Route("/{id}", name="person_delete", methods="DELETE")
     */
    public function delete(Request $request, Person $person): Response
    {
        if ($this->isCsrfTokenValid('delete'.$person->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($person);
            $em->flush();
        }

        return $this->redirectToRoute('person_index');
    }
}
