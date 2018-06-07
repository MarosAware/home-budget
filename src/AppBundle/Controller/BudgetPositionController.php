<?php

namespace AppBundle\Controller;

use AppBundle\Entity\BudgetPosition;
use AppBundle\Form\BudgetPositionType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use CMEN\GoogleChartsBundle\GoogleCharts\Charts;


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
        $user = $this->getUser();

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
            ->findBy(array('type' => 'przychód', 'user' => $user), array('name'=>'ASC'));

        $costCategories = $this
            ->getDoctrine()
            ->getRepository("AppBundle:Category")
            ->findBy(array('type' => 'wydatek', 'user' => $user), array('name'=>'ASC'));

        $incomeCategoriesSums = [];
        $costCategoriesSums = [];

        $chartData[] = ['Kategoria', 'Kwota'];
        $chartData2[] = ['Kategoria', 'Kwota'];


        foreach ($incomeCategories as $category) {
            $budgetPositions = $this
                ->getDoctrine()
                ->getRepository("AppBundle:BudgetPosition")
                ->findByMonthAndCategory($month, $year, $category->getId());

            $incomeCategoriesSums [] = BudgetPosition::sumPositions($budgetPositions);
            $chartData2[] = [$category->getName(), end($incomeCategoriesSums)];
        }

        foreach ($costCategories as $category) {
            $budgetPositions = $this
                ->getDoctrine()
                ->getRepository("AppBundle:BudgetPosition")
                ->findByMonthAndCategory($month, $year, $category->getId());



            $costCategoriesSums [] = BudgetPosition::sumPositions($budgetPositions);
            $chartData[] = [$category->getName(), end($costCategoriesSums)];
        }

        $totalIncome = array_sum($incomeCategoriesSums);
        $totalCost = array_sum($costCategoriesSums);


        //Charts

        //Chart for costs.
        $pieChart = new Charts\PieChart();
        $pieChart->getData()->setArrayToDataTable($chartData);
        $pieChart->getOptions()->setTitle('Wydatki:');
        $pieChart->getOptions()->setHeight(500);
        $pieChart->getOptions()->setWidth(700);
        $pieChart->getOptions()->getTitleTextStyle()->setBold(true);
        $pieChart->getOptions()->getTitleTextStyle()->setColor('#000');
        $pieChart->getOptions()->getTitleTextStyle()->setItalic(false);
        $pieChart->getOptions()->getTitleTextStyle()->setFontName('Arial');
        $pieChart->getOptions()->getTitleTextStyle()->setFontSize(30);


        //Chart for incomes.
        $pieChart2 = new Charts\PieChart();
        $pieChart2->getData()->setArrayToDataTable($chartData2);
        $pieChart2->getOptions()->setTitle('Przychody:');
        $pieChart2->getOptions()->setHeight(500);
        $pieChart2->getOptions()->setWidth(700);
        $pieChart2->getOptions()->getTitleTextStyle()->setBold(true);
        $pieChart2->getOptions()->getTitleTextStyle()->setColor('#000');
        $pieChart2->getOptions()->getTitleTextStyle()->setItalic(false);
        $pieChart2->getOptions()->getTitleTextStyle()->setFontName('Arial');
        $pieChart2->getOptions()->getTitleTextStyle()->setFontSize(30);


        //End Charts

        return $this->render('@App/BudgetPosition/showOneMonth.html.twig', [
            'year' => $year,
            'month' => $month,
            'monthTxt' => $months[$month],
            'incomeCategories' => $incomeCategories,
            'incomeCategoriesSums' => $incomeCategoriesSums,
            'costCategories' => $costCategories,
            'costCategoriesSums' => $costCategoriesSums,
            'totalIncome' => $totalIncome,
            'totalCost' =>$totalCost,
            'piechart' => $pieChart,
            'piechart2' => $pieChart2]);
    }

    /**
     * @Route("/{year}/{month}/addPosition/{categoryId}")
     */
    public function addPositionAction($year, $month, Request $request, $categoryId)
    {
        $position = new BudgetPosition();
        $user = $this->getUser();

        $form = $this->createForm(BudgetPositionType::class, $position,
            ['year' => $year, 'month' => $month]);


        if($request->isXmlHttpRequest()) {

            $em = $this->getDoctrine()->getManager();

            $title = $request->request->get('title');
            $description = $request->request->get('description');
            $price = $request->request->get('price');
            $date = $request->request->get('date');


            $category = $this
                ->getDoctrine()
                ->getRepository("AppBundle:Category")
                ->find($categoryId);

            $dateObj = new \DateTime($date);

            $position->setTitle($title);
            $position->setDescription($description);
            $position->setPrice($price);
            $position->setDate($dateObj);
            $position->setCategory($category);
            $position->setUser($user);

            $em->persist($position);
            $em->flush();


            return new JsonResponse(array('success' => $position), 200);
        }

        return $this->render('@App/BudgetPosition/addPositionForm.html.twig', ['form' => $form->createView(), 'year' => $year, 'month' => $month, 'categoryId'=> $categoryId]);

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
