<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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
     * @Route("/{year}")
     */

    public function showMonthsAction($year)
    {

        return $this->render('@App/BudgetPosition/showMonths.html.twig', ['year' => $year]);
    }

    /**
     * @Route("/{year}/{monthId}")
     */
    public function oneMonthAction($year, $monthId, Request $request)
    {
        $month = $request->query->get('month');


        return $this->render('@App/BudgetPosition/showOneMonth.html.twig',
            ['year' => $year, 'monthId' => $monthId, 'month' => $month]);
    }

}
