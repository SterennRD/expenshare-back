<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Expense;
use App\Entity\Person;
use App\Entity\ShareGroup;
use App\Form\ExpenseType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * @Route("/expense")
 */
class ExpenseController extends BaseController
{
    /**
     * @Route("/{slug}", name="expense_index", methods="GET")
     */
    public function index(ShareGroup $group, Request $request): Response
    {
        $expenses = $this->getDoctrine()
            ->getRepository(Expense::class)
            ->createQueryBuilder('e')
            ->select('p', 'e', 's', 'c')
            ->leftJoin('e.person', 'p')
            ->leftJoin('e.category', 'c')
            ->join('p.shareGroup', 's')
            ->where('s.id = :group')
            ->setParameter('group', $group)
            ->getQuery()
            ->getArrayResult();


        if ($request->isXmlHttpRequest()) {
            return $this->json($expenses);
        } else {

        }
    }

    /**
     * @Route("/liste/{slug}", name="expense_liste", methods="GET")
     */
    public function liste(ShareGroup $group, Request $request): Response
    {
        $expenses = $this->getDoctrine()
            ->getRepository(Expense::class)
            ->createQueryBuilder('e')
            ->select('p', 'e', 's', 'c', 'SUM(e.amount) AS somme', 'COUNT(p.id) as nb_paie')
            ->leftJoin('e.person', 'p')
            ->leftJoin('e.category', 'c')
            ->join('p.shareGroup', 's')
            ->where('s.id = :group')
            ->groupBy('p.id')
            ->setParameter('group', $group)
            ->getQuery()
            ->getArrayResult();


        if ($request->isXmlHttpRequest()) {
            return $this->json($expenses);
        } else {

        }
    }


    /**
     * @Route("/", name="expense_new", methods="POST")
     */
    public function new(Request $request)
    {
        $data = $request->getContent();

        $jsonData = json_decode($data, true);
        $cat = $this->getDoctrine()->getRepository(Category::class)->find($jsonData["category"]);
        $person = $this->getDoctrine()->getRepository(Person::class)->find($jsonData["person"]);

        $em = $this->getDoctrine()->getManager();

        $expense = new Expense();
        $expense->setCreatedAt(new \DateTime());
        $expense->setCategory($cat);
        $expense->setTitle($jsonData["title"]);
        $expense->setAmount($jsonData["amount"]);
        $expense->setPerson($person);

        $em->persist($expense);
        $em->flush();

        return $this->json($this->serialize($expense));
    }

    /**
     * @Route("/{id}", name="expense_show", methods="GET")
     */
    public function show(Expense $expense): Response
    {
        return $this->render('expense/show.html.twig', ['expense' => $expense]);
    }

    /**
     * @Route("/{id}/edit", name="expense_edit", methods="GET|POST")
     */
    public function edit(Request $request, Expense $expense): Response
    {
        $data = $request->getContent();

        $jsonData = json_decode($data, true);
        $expense = $this->getDoctrine()->getRepository(Expense::class)->find($expense);
        $cat = $this->getDoctrine()->getRepository(Category::class)->find($jsonData["category"]);
        $person = $this->getDoctrine()->getRepository(Person::class)->find($jsonData["person"]);

        $em = $this->getDoctrine()->getManager();

        $expense->setCategory($cat);
        $expense->setTitle($jsonData["title"]);
        $expense->setAmount($jsonData["amount"]);
        $expense->setPerson($person);

        $em->persist($expense);
        $em->flush();

        $expenses = $this->getDoctrine()
            ->getRepository(Expense::class)->findAll();

        return $this->json($this->serialize($expenses));
    }

    /**
     * @Route("/", name="expense_delete", methods="DELETE")
     */
    public function delete(Request $request): Response
    {

        $data = $request->getContent();

        $jsonData = json_decode($data, true);
        $expense = $this->getDoctrine()->getRepository(Expense::class)->find($jsonData["id"]);

            $em = $this->getDoctrine()->getManager();
            $em->remove($expense);
            $em->flush();


        return $this->json($this->serialize($expense));
    }
}
