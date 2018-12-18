<?php

namespace App\Controller;

use App\Entity\Expense;
use App\Entity\Person;
use App\Entity\ShareGroup;
use App\Form\ExpenseType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
            ->select('p', 'e', 's', 'c', 'SUM(e.amount) AS somme')
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
     * @Route("/new", name="expense_new", methods="GET|POST")
     */
    public function new(Request $request): Response
    {
        $expense = new Expense();
        $form = $this->createForm(ExpenseType::class, $expense);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($expense);
            $em->flush();

            return $this->redirectToRoute('expense_index');
        }

        return $this->render('expense/new.html.twig', [
            'expense' => $expense,
            'form' => $form->createView(),
        ]);
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
        $form = $this->createForm(ExpenseType::class, $expense);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('expense_index', ['id' => $expense->getId()]);
        }

        return $this->render('expense/edit.html.twig', [
            'expense' => $expense,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="expense_delete", methods="DELETE")
     */
    public function delete(Request $request, Expense $expense): Response
    {
        if ($this->isCsrfTokenValid('delete'.$expense->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($expense);
            $em->flush();
        }

        return $this->redirectToRoute('expense_index');
    }
}
