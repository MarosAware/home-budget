<?php

namespace AppBundle\Controller;

use AppBundle\Entity\BudgetPosition;
use AppBundle\Form\BudgetPositionType;
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
     * @Route("/year/{year}")
     */

    public function showMonthsAction($year)
    {

        return $this->render('@App/BudgetPosition/showMonths.html.twig', ['year' => $year]);
    }

    /**
     * @Route("/year/{year}/{monthId}")
     */
    public function oneMonthAction($year, $monthId, Request $request)
    {
        $month = $request->query->get('month');


        return $this->render('@App/BudgetPosition/showOneMonth.html.twig',
            ['year' => $year, 'monthId' => $monthId, 'month' => $month]);
    }


    /**
     * @Route("/year/{year}/{monthId}/addBudgetPosition")
     */
    public function addPositionAction($year, $monthId, Request $request)
    {
        $position = new BudgetPosition();

        $form = $this->createForm(BudgetPositionType::class, $position, ['year' => $year, 'monthId' => $monthId]);
        $form->handleRequest($request);

        if ($form->isValid() && $form->isSubmitted()) {
            $em = $this->getDoctrine()->getManager();

            $em->persist($position);
            $em->flush();

            return $this->redirectToRoute('app_budgetposition_onemonth', ['year' => $year, 'monthId' => $monthId]);
        }

        return $this->render('@App/BudgetPosition/addBudgetPosition.html.twig', ['form' => $form->createView()]);
    }
}
