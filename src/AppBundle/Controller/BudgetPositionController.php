<?php

namespace AppBundle\Controller;

use AppBundle\Entity\BudgetPosition;
use AppBundle\Entity\Category;
use AppBundle\Form\BudgetPositionType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class BudgetPositionController extends Controller
{

    /**
     * @Route("/")
     * @Method("GET")
     */
    public function selectYearGetAction()
    {
        $currentDate = new \DateTime();
        $currentDate = $currentDate->format('Y');
        return $this->render('@App/BudgetPosition/selectYearForm.html.twig', ['year' => $currentDate]);
    }

    /**
     * @Route("/")
     * @Method("POST")
     */
    public function selectYearPostAction(Request $request)
    {
        $year = $request->request->get('year');
        return $this->redirectToRoute('app_budgetposition_showmonths', ['year' => $year]);
    }

    /**
     * @Route("/{year}/", requirements={"year"="\d+"})
     */

    public function showMonthsAction($year)
    {

        return $this->render('@App/BudgetPosition/showMonths.html.twig', ['year' => $year]);
    }

    /**
     * @Route("/{year}/{month}", requirements={"year"="\d+", "month"="\d+"} )
     */
    public function oneMonthAction($year, $month)
    {
        $months = [
            '01' =>'Styczeń',
            '02' => 'Luty',
            '03' => 'Marzec',
            '04' => 'Kwiecień',
            '05' => 'Maj',
            '06' => 'Czerwiec',
            '07' => 'Lipiec',
            '08' => 'Sierpień',
            '09' => 'Wrzesień',
            '10' => 'Październik',
            '11' => 'Listopad',
            '12' => 'Grudzień'];

        $incomeCategories = $this
            ->getDoctrine()
            ->getRepository("AppBundle:Category")
            ->findBy(array('type' => 'przychód'), array('name'=>'ASC'));

        $costCategories = $this
            ->getDoctrine()
            ->getRepository("AppBundle:Category")
            ->findBy(array('type' => 'wydatek'), array('name'=>'ASC'));

        $incomeCategoriesSums = [];
        $costCategoriesSums = [];

        foreach ($incomeCategories as $category) {
            $budgetPositions = $this
                ->getDoctrine()
                ->getRepository("AppBundle:BudgetPosition")
                ->findByMonthAndCategory($month, $year, $category->getId());

            $incomeCategoriesSums [] = BudgetPosition::sumPositions($budgetPositions);
        }

        foreach ($costCategories as $category) {
            $budgetPositions = $this
                ->getDoctrine()
                ->getRepository("AppBundle:BudgetPosition")
                ->findByMonthAndCategory($month, $year, $category->getId());

            $costCategoriesSums [] = BudgetPosition::sumPositions($budgetPositions);
        }
        
        $totalIncome = array_sum($incomeCategoriesSums);
        $totalCost = array_sum($costCategoriesSums);

        return $this->render('@App/BudgetPosition/showOneMonth.html.twig', [
            'year' => $year,
            'month' => $month,
            'monthTxt' => $months[$month],
            'incomeCategories' => $incomeCategories,
            'incomeCategoriesSums' => $incomeCategoriesSums,
            'costCategories' => $costCategories,
            'costCategoriesSums' => $costCategoriesSums,
            'totalIncome' => $totalIncome,
            'totalCost' =>$totalCost]);
    }

    /**
     * @Route("/{year}/{month}/addPosition")
     */
    public function addPositionAction($year, $month, Request $request)
    {

//TODO: Make selected category name when visit this route from category details
//        $categoryId = $request->query->get('categoryId');
//
//        if (isset($categoryId)) {
//            $category = $this->getDoctrine()->getRepository('AppBundle:Category')->findOneById($categoryId);
//
//            if (!$category) {
//                return $this->createNotFoundException('Category not found.');
//            }
//
//        }
//
//        $em = $this->getDoctrine()->getManager();


        //'categoryObj' => isset($category) ? $category : null

        $position = new BudgetPosition();

        $form = $this->createForm(BudgetPositionType::class, $position,
            ['year' => $year, 'month' => $month]);



        $form->handleRequest($request);

        if ($form->isValid() && $form->isSubmitted()) {
            $em = $this->getDoctrine()->getManager();

            $em->persist($position);
            $em->flush();

            $categoryId = $position->getCategory()->getId();
            return $this->redirectToRoute('app_category_categorydetails', ['year' => $year, 'month' => $month, 'categoryId' => $categoryId]);
        }

        return $this->render('@App/BudgetPosition/add.html.twig', ['form' => $form->createView(), 'year' => $year, 'month' => $month]);
    }


    /**
     * @Route("/{year}/{month}/editPosition/{id}")
     */
    public function editAction($year, $month, $id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $position = $em->getRepository('AppBundle:BudgetPosition')->findOneById($id);

        if (!$position) {
            return $this->createNotFoundException('Budget position not found.');
        }
        $categoryId = $position->getCategory()->getId();

        $form = $this->createForm(BudgetPositionType::class, $position, ['year' => $year, 'month' => $month]);
        $form->handleRequest($request);

        if ($form->isValid() && $form->isSubmitted()) {
            $em->flush();

            return $this->redirectToRoute('app_category_categorydetails', ['year' => $year, 'month' => $month, 'categoryId' => $categoryId]);
        }

        return $this->render('@App/BudgetPosition/edit.html.twig', ['form' => $form->createView(), 'year' => $year, 'month' => $month, 'categoryId' => $categoryId]);
    }

    /**
     * @Route("/{year}/{month}/deletePosition/{id}")
     */
    public function deleteAction($year, $month, $id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $position = $em->getRepository('AppBundle:BudgetPosition')->findOneById($id);

        if (!$position) {
            return $this->createNotFoundException('Budget position not found.');
        }

        $categoryId = $position->getCategory()->getId();

        $em->remove($position);
        $em->flush();

        return $this->redirectToRoute('app_category_categorydetails', ['year' => $year, 'month' => $month, 'categoryId' => $categoryId]);
    }
}
