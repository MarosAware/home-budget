<?php

namespace AppBundle\Controller;

use AppBundle\Entity\BudgetPosition;
use AppBundle\Entity\Category;
use AppBundle\Form\CategoryType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class CategoryController extends Controller
{
    /**
     * @Route("/{year}/{month}/details/{categoryId}")
     */
    public function showDetailsAction( $year, $month, $categoryId)
    {
        $category = $this->getDoctrine()->getRepository("AppBundle:Category")->find($categoryId);
        if (!$category){
            throw $this->createNotFoundException('Category not found');
        }
        $categoryName = $category->getName();

        $budgetPositions = $this
            ->getDoctrine()
            ->getRepository('AppBundle:BudgetPosition')
            ->findByMonthAndCategory($month, $year, $categoryId);

        $sum = BudgetPosition::sumPositionsByMonthAndCategory($budgetPositions);

        return $this->render('@App/category/details.html.twig', [
            'budgetPositions' => $budgetPositions,
            'categoryName' => $categoryName,
            'sum' => $sum,
            'year' => $year,
            'month' => $month,]);
    }

    /**
     * @Route("/{year}/{month}/addCategory/")
     */
    public function addCategoryAction(Request $request, $year, $month)
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $em = $this->getDoctrine()->getManager();

            $em->persist($category);
            $em->flush();

            return $this->redirectToRoute('app_budgetposition_onemonth', ['year' => $year, 'month' => $month]);
        }

        return $this->render('@App/category/addCategory.html.twig', ['form' => $form->createView()]);
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

        return $this->render('@App/category/modifyCategory.html.twig', ['form' => $form->createView()]);
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

//        return $this->redirectToRoute('');
    }
}
