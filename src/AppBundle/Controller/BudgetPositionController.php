<?php

namespace AppBundle\Controller;

use AppBundle\Entity\BudgetPosition;
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
     * @Route("/year/{year}")
     */

    public function showMonthsAction($year)
    {

        return $this->render('@App/BudgetPosition/showMonths.html.twig', ['year' => $year]);
    }

    /**
     * @Route("/year/{year}/{monthId}/{month}")
     */
    public function oneMonthAction($year, $monthId, $month)
    {

        $month = $request->query->get('month');

        $incomeCategories = $this->getDoctrine()->getRepository("AppBundle:Category")->findByType('przychód');
        $costCategories = $this->getDoctrine()->getRepository("AppBundle:Category")->findByType('wydatek');

        foreach ($incomeCategories as $category) {
            $budgetPositions = $this
                ->getDoctrine()
                ->getRepository("AppBundle:BudgetPosition")
                ->findByMonthAndCategory($monthId, $year, $category->getId());

            $allIncomeCategoriesSum [$category->getName()] = BudgetPosition::sumPositionsByMonthAndCategory($budgetPositions);
        }

        foreach ($costCategories as $category) {
            $budgetPositions = $this
                ->getDoctrine()
                ->getRepository("AppBundle:BudgetPosition")
                ->findByMonthAndCategory($monthId, $year, $category->getId());

            $allCostCategoriesSum [$category->getName()] = BudgetPosition::sumPositionsByMonthAndCategory($budgetPositions);
        }

//        $totalIncome = number_format(array_sum($incomeCategories),2);
        $totalIncome = array_sum($allIncomeCategoriesSum);
        $totalCost = array_sum($allCostCategoriesSum);

        return $this->render('@App/BudgetPosition/showOneMonth.html.twig', [
            'year' => $year,
            'monthId' => $monthId,
            'month' => $month,
            'incomeCategories' => $allIncomeCategoriesSum,
            'costCategories' => $allCostCategoriesSum,
            'totalIncome' => $totalIncome,
            'totalCost' =>$totalCost
        ]);
    }

    /**
     * @Route("/year/{year}/{monthId}/{month}/addBudgetPosition")
     */
    public function addPositionAction($year, $monthId, $month, Request $request)
    {
        $position = new BudgetPosition();

        $form = $this->createForm(BudgetPositionType::class, $position, ['year' => $year, 'monthId' => $monthId]);
        $form->handleRequest($request);

        if ($form->isValid() && $form->isSubmitted()) {
            $em = $this->getDoctrine()->getManager();

            $em->persist($position);
            $em->flush();

            return $this->redirectToRoute('app_budgetposition_onemonth', ['year' => $year, 'monthId' => $monthId, 'month' => $month]);
        }

        return $this->render('@App/BudgetPosition/addBudgetPosition.html.twig', ['form' => $form->createView()]);
    }
}
