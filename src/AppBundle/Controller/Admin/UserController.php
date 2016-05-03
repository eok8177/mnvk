<?php

namespace AppBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use AppBundle\Entity\User;
use AppBundle\Form\UserType;

use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/admin")
 */
class UserController extends Controller
{
	private $URL_USER_AVATAR = "/uploads/avatars";

	/**
	 * @Route("/users", name="admin_users_list")
	 */
	public function indexAction(Request $request)
	{

		$order = $request->query->get('order') ? $request->query->get('order') : 'ASC';
		$column = $request->query->get('column') ? $request->query->get('column') : 'id';

		$users = $this->getDoctrine()
			->getRepository('AppBundle:User')
			->findBy(
				array(),
				array($column => $order));

		return $this->render('admin/users/index.html.twig', [
			'users' => $users,
			'current' => ['controller' => 'user', 'action' => 'list'],
			'order' => $order,
			'column' => $column,
		]);
	}

	/**
	 * @Route("/user/edit/{id}", name="admin_user_edit", defaults={"id" = 0})
	 */
	public function editAction($id, Request $request)
	{

		if ($id == 0) {
			$user = new User();
			$action = 'create';
		} else {
			$user = $this->getDoctrine()
				->getRepository('AppBundle:User')
				->find($id);

			if (!$user) {throw $this->createNotFoundException('No users found');}
			$action = 'edit';
		}

		$photo = $user->getPhoto();
		$user->setPhoto('');

		$form = $this->createForm(UserType::class, $user);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {

			$password = $form['plainPassword']->getData();

			if ($password) {
				$password = $this->get('security.password_encoder')
					->encodePassword($user, $password);
				$user->setPassword($password);
			}

			// $file stores the uploaded file
			/** @var Symfony\Component\HttpFoundation\File\UploadedFile $file */
			$file = $user->getPhoto();
			if ($file) {
				// Generate a unique name for the file before saving it
				$ext = $file->getClientOriginalExtension();
				$name = preg_replace("/$ext/", '', $file->getClientOriginalName());

				$fileName = $name.md5(uniqid()).'.'.$file->guessExtension();

				$urlDir = $this->URL_USER_AVATAR;

				$rootDir = $this->container->getParameter('kernel.root_dir').'/../web';

				$photosDir = $rootDir.$urlDir;

				$file->move($photosDir, $fileName);

				//delete old file
				if ($photo AND file_exists($rootDir.'/'.$photo)) unlink($rootDir.'/'.$photo);

				// Update the 'photo' property to store the file name instead of its contents
				$user->setPhoto($urlDir.'/'.$fileName);
			} else {
				$user->setPhoto($photo);
			}

			$em = $this->getDoctrine()->getManager();
			$em->persist($user);
			$em->flush();

			$this->addFlash(
				'notice',
				'User saved!'
			);
			return $this->redirectToRoute('admin_user_edit', [
				'id' => $user->getId(),
				]);
		}

		return $this->render('admin/users/edit.html.twig', [
			'form' => $form->createView(),
			'photo' => $photo,
			'user' => $user,
			'current' => ['controller' => 'user', 'action' => $action],
		]);
	}

	/**
	 * @Route("/user/delete/{id}", name="admin_user_delete")
	 */
	public function deleteAction($id, Request $request)
	{
		if ($this->getUser()->getId() == $id) {
			$this->addFlash(
				'warning',
				'You can`t delete self!'
			);
			return $this->redirectToRoute('admin_users_list');
		}

		$user = $this->getDoctrine()
			->getRepository('AppBundle:User')
			->find($id);

		$photo = $user->getPhoto();
		$rootDir = $this->container->getParameter('kernel.root_dir').'/../web';
		if ($photo AND file_exists($rootDir.'/'.$photo)) unlink($rootDir.'/'.$photo);
		$cacheManager = $this->get('liip_imagine.cache.manager');
		$cacheManager->remove($photo);

		$em = $this->getDoctrine()->getManager();
		$em->remove($user);
		$em->flush();

		$this->addFlash(
			'warning',
			'User deleted!'
		);

		return $this->redirectToRoute('admin_users_list');
	}

	/**
	 * @Route("/avatar/delete/{id}", name="admin_user_delete_avatar")
	 */
	public function deleteAvatarAction ($id, Request $request) 
	{
		$user = $this->getDoctrine()
		->getRepository('AppBundle:User')
		->find($id);

		if (!$user) {
			throw $this->createNotFoundException(
				'No users found for id '.$id
				);
		}

		$photo = $user->getPhoto();
		$rootDir = $this->container->getParameter('kernel.root_dir').'/../web';
		if ($photo AND file_exists($rootDir.'/'.$photo)) unlink($rootDir.'/'.$photo);
		$cacheManager = $this->get('liip_imagine.cache.manager');
		$cacheManager->remove($photo);

		$user->setPhoto('');

		$em = $this->getDoctrine()->getManager();
		$em->persist($user);
		$em->flush();

		$this->addFlash(
			'warning',
			'Foto deleted'
			);

		return $this->redirectToRoute('admin_user_edit', [
			'id' =>$id,
			]);
	}

}
