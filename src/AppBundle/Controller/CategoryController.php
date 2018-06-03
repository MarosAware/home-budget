<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Category;
use AppBundle\Form\CategoryType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class CategoryController extends Controller
{
    /**
     * @Route("/{month}/{year}/details/{category}")
     */
    public function showDetailsAction($month, $category, $year)
    {
        $budgetPositions = $this
            ->getDoctrine()
            ->getRepository('AppBundle:BudgetPosition')
            ->findByMonthAndCategory($month, $year, $category);

        return $this->render('@App/details.html.twig', ['budgetPositions' => $budgetPositions]);
    }

    /**
     * @Route("/addCategory")
     */
    public function addCategoryAction(Request $request)
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $em = $this->getDoctrine()->getManager();

            $em->persist($category);
            $em->flush();

            return $this->render('@App/category/add.html.twig', ['form' => $form->createView()]);

//            return $this->redirectToRoute('app_budgetposition_showmonth');
        }

        return $this->render('@App/category/add.html.twig', ['form' => $form->createView()]);
    }

}
