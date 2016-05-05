<?php
namespace AppBundle\Controller\Admin;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Symfony\Component\HttpFoundation\Request;

use AppBundle\Entity\Page;
use AppBundle\Form\PageType;

/**
 * @Route("/admin/page")
 */
class PageController extends Controller
{
	private $URL_PAGE_IMAGE = "/uploads/pages";

	/**
	 * @Route("/list", name="admin_page_list")
	 */
	public function listAction(Request $request)
	{
		$pages = $this->getDoctrine()
			->getRepository('AppBundle:Page')
			->getList();

		return $this->render('admin/page/list.html.twig', [
			'pages' => $pages,
			'current' => ['controller' => 'page', 'action' => 'list'],
		]);
	}

	/**
	 * @Route("/edit/{id}", name="admin_page_edit", defaults={"id" = 0})
	 */
	public function EditAction($id, Request $request)
	{
		if ($id == 0) {
			$page = new Page();
			$action = 'create';
		} else {
			$page = $this->getDoctrine()
				->getRepository('AppBundle:Page')
				->find($id);

			if (!$page) {throw $this->createNotFoundException('No pages found');}
			$action = 'edit';
		}

		$image = $page->getFimage();
		$page->setFimage('');

		$form = $this->createForm(PageType::class, $page);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {

			$file = $page->getFimage();
			if ($file) {
				$ext = $file->getClientOriginalExtension();
				$name = preg_replace("/$ext/", '', $file->getClientOriginalName());

				$fileName = $name.md5(uniqid()).'.'.$file->guessExtension();

				$urlDir = $this->URL_PAGE_IMAGE;

				$rootDir = $this->container->getParameter('kernel.root_dir').'/../web';

				$imagesDir = $rootDir.$urlDir;

				$file->move($imagesDir, $fileName);

				if ($image AND file_exists($rootDir.'/'.$image)) unlink($rootDir.'/'.$image);

				$page->setFimage($urlDir.'/'.$fileName);
			} else {
				$page->setFimage($image);
			}

			$status = $this->getDoctrine()
				->getRepository('AppBundle:Page')
				->savePage($page);

			$this->addFlash('notice','Saved!');

			return $this->redirectToRoute('admin_page_edit', ['id' => $page->getId()]);
		}

		return $this->render('admin/page/edit.html.twig', [
			'form' => $form->createView(),
			'current' => ['controller' => 'page', 'action' => $action],
			'image' => $image,
			'page' => $page,
		]);
	}

	/**
	 * @Route("/delete/{id}", name="admin_page_delete")
	 */
	public function DeleteAction($id, Request $request)
	{
		$page = $this->getDoctrine()
			->getRepository('AppBundle:Page')
			->find($id);

		$image = $page->getFimage();
		$rootDir = $this->container->getParameter('kernel.root_dir').'/../web';
		if ($image AND file_exists($rootDir.'/'.$image)) unlink($rootDir.'/'.$image);
		$cacheManager = $this->get('liip_imagine.cache.manager');
		$cacheManager->remove($image);

		$em = $this->getDoctrine()->getManager();
		$em->remove($page);
		$em->flush();

		$this->addFlash(
			'warning',
			'Page deleted!'
		);

		return $this->redirectToRoute('admin_page_list');
	}

	/**
	 * @Route("/image/delete/{id}", name="admin_page_delete_image")
	 */
	public function deleteImageAction ($id, Request $request) 
	{
		$page = $this->getDoctrine()
		->getRepository('AppBundle:Page')
		->find($id);

		if (!$page) {
			throw $this->createNotFoundException(
				'No pages found for id '.$id
				);
		}

		$image = $page->getFimage();
		$rootDir = $this->container->getParameter('kernel.root_dir').'/../web';
		if ($image AND file_exists($rootDir.'/'.$image)) unlink($rootDir.'/'.$image);
		$cacheManager = $this->get('liip_imagine.cache.manager');
		$cacheManager->remove($image);

		$page->setFimage('');

		$em = $this->getDoctrine()->getManager();
		$em->persist($page);
		$em->flush();

		$this->addFlash(
			'warning',
			'Image deleted'
			);

		return $this->redirectToRoute('admin_page_edit', [
			'id' =>$id,
			]);
	}
}