<?php

namespace AppBundle\Controller;

use AppBundle\Entity\BudgetPosition;
use AppBundle\Entity\Category;
use AppBundle\Form\CategoryType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
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
            'month' => $month,
            'categoryId' => $categoryId
            ]);
    }

    //AJAX

    /**
     * @Route("/{year}/{month}/addCategory/")
     * @Method("POST")
     */
    public function addCategoryAction(Request $request)
    {
        $user = $this->getUser();

        $category = new Category();
        $category->setUser($user);

        if($request->isXmlHttpRequest()) {

            $em = $this->getDoctrine()->getManager();

            $name = $request->request->get('name');
            $type = $request->request->get('type');

            $category->setName($name);
            $category->setType($type);

            $em->persist($category);
            $em->flush();


            return new JsonResponse(array('success' => $category), 200);
        }
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

        return $this->render('@App/category/modify.html.twig', ['form' => $form->createView(), 'year' => $year, 'month' => $month, 'categoryId' => $id]);
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

        $user = $this->getUser();


        $category = new Category();
        $category->setUser($user);

        $form = $this->createForm(CategoryType::class, $category);


        $categories = $this
            ->getDoctrine()
            ->getRepository('AppBundle:Category')
            ->findBy(array('user' => $user), array('name'=>'ASC'));

        return $this->render('@App/category/showAll.html.twig', [
            'form' => $form->createView(),
            'categories' => $categories,
            'year' => $year,
            'month' => $month]);
    }
}
