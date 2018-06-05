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
     * @Route("/{year}/{month}/categoryDetails/{categoryId}")
     */
    public function categoryDetailsAction( $year, $month, $categoryId)
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

        $sum = BudgetPosition::sumPositions($budgetPositions);

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

        return $this->render('@App/category/addCategory.html.twig', ['form' => $form->createView(), 'year' => $year, 'month' => $month]);
    }

    /**
     * @Route("/{year}/{month}/modifyCategory/{id}")
     */
    public function modifyCategoryAction(Request $request, $id, $year, $month)
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

            return $this->redirectToRoute('app_category_showcategories', ['year' => $year, 'month' =>$month]);
        }

        return $this->render('@App/category/modifyCategory.html.twig', ['form' => $form->createView(), 'year' => $year, 'month' => $month, 'categoryId' => $id]);
    }

    /**
     * @Route("/{year}/{month}/deleteCategory/{id}")
     */
    public function deleteCategoryAction($id, $year, $month)
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

        return $this->redirectToRoute('app_category_showcategories', ['year' => $year, 'month' =>$month]);
    }

    /**
     * @Route("/{year}/{month}/showCategories")
     */
    public function showCategoriesAction($year, $month)
    {
        $categories = $this
            ->getDoctrine()
            ->getRepository('AppBundle:Category')
            ->findAll();

        return $this->render('@App/category/showCategories.html.twig', [
            'categories' => $categories,
            'year' => $year,
            'month' => $month]);
    }
}
