<?php
namespace AppBundle\Controller\Admin;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Symfony\Component\HttpFoundation\Request;

use AppBundle\Entity\Category;
use AppBundle\Form\CategoryType;

/**
 * @Route("/admin/category")
 */
class CategoryController extends Controller
{
	/**
	 * @Route("/list", name="admin_category_list")
	 */
	public function listAction(Request $request)
	{
		$categories = $this->getDoctrine()
			->getRepository('AppBundle:Category')
			->getList();

		return $this->render('admin/category/list.html.twig', [
			'categories' => $categories,
			'current' => ['controller' => 'category', 'action' => 'list'],
		]);
	}

	/**
	 * @Route("/edit/{id}", name="admin_category_edit", defaults={"id" = 0})
	 */
	public function EditAction($id, Request $request)
	{
		if ($id == 0) {
			$category = new Category();
			$action = 'create';
		} else {
			$category = $this->getDoctrine()
				->getRepository('AppBundle:Category')
				->find($id);

			if (!$category) {throw $this->createNotFoundException('No categories found');}
			$action = 'edit';
		}

		$form = $this->createForm(CategoryType::class, $category);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$em = $this->getDoctrine()->getManager();
			$em->persist($category);

			try{
				//ordering
				if ($form['parent']->getData()) {
					$parent = $em
						->getRepository('AppBundle:Category')
						->find($form['parent']->getData());

					if ($form['order']->getData() == 'before') {
						$em->getRepository('AppBundle:Category')->persistAsPrevSiblingOf($category, $parent);
					} elseif ($form['order']->getData() == 'after') {
						$em->getRepository('AppBundle:Category')->persistAsNextSiblingOf($category, $parent);
					}
				}

				$em->flush();

			} catch(\Doctrine\ORM\ORMException $e){
				$this->addFlash('warning', $e->getMessage());
				return $this->redirectToRoute('admin_category_edit', ['id' => $category->getId()]);
			} catch(\Exception $e){
				$this->addFlash('warning', $e->getMessage());
				return $this->redirectToRoute('admin_category_edit', ['id' => $category->getId()]);
			}


			$this->addFlash('notice','Saved!');

			if ($form->get('saveAndExit')->isClicked()) return $this->redirectToRoute('admin_category_list');
			return $this->redirectToRoute('admin_category_edit', ['id' => $category->getId()]);
		}

		return $this->render('admin/category/edit.html.twig', [
			'form' => $form->createView(),
			'current' => ['controller' => 'category', 'action' => $action],
			'category' => $category,
		]);
	}

	/**
	 * @Route("/delete/{id}", name="admin_category_delete")
	 */
	public function DeleteAction($id, Request $request)
	{
		$category = $this->getDoctrine()
			->getRepository('AppBundle:Category')
			->find($id);

		$em = $this->getDoctrine()->getManager();
		$em->remove($category);
		$em->flush();

		$this->addFlash(
			'warning',
			'Category deleted!'
		);

		return $this->redirectToRoute('admin_category_list');
	}
}