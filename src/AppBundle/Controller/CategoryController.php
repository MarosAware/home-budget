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
     * @Route("/{month}/{year}/details/{categoryId}")
     */
    public function showDetailsAction($month, $categoryId, $year)
    {
        $category = $this->getDoctrine()->getRepository("AppBundle:Category")->find($categoryId);
        $categoryName = $category->getName();

        $budgetPositions = $this
            ->getDoctrine()
            ->getRepository('AppBundle:BudgetPosition')
            ->findByMonthAndCategory($month, $year, $categoryId);

        $sum = 0;

        foreach ($budgetPositions as $budgetPosition){

            $category = $budgetPosition->getCategory();
            if ($category->getType() === "przychód"){
                $sum += $budgetPosition->getPrice();

            }elseif ($category->getType() === "wydatek"){
                $sum -= $budgetPosition->getPrice();
            }
        }

        return $this->render('@App/details.html.twig', ['budgetPositions' => $budgetPositions, 'categoryName' => $categoryName, 'sum' => $sum]);
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

    /**
     * @Route("/modifyCategory/{id}")
     */
    public function modifyCategoryAction(Request $request, $id)
    {
        $category = $this
            ->getDoctrine()
            ->getRepository('AppBundle:Category')
            ->find($id);

        if (!$category) {
            throw $this->createNotFoundException('Category not found');
        }

        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();

//            return $this->redirectToRoute('');
        }

        return $this->render('@App/category/modify.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/deleteCategory/{id}")
     */
    public function deleteCategoryAction(Request $request, $id)
    {
        $category = $this
            ->getDoctrine()
            ->getRepository('AppBundle:Category')
            ->find($id);

        if (!$category) {
            throw $this->createNotFoundException('Category not found');
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($category);
        $em->flush();

        return $this->redirectToRoute('');
    }
}
