<?php

namespace AppBundle\Controller;

use AppBundle\Form\UserType;
use AppBundle\Form\UserRegistrationType;
use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class SecurityController extends Controller
{
	/**
	 * @Route("/login", name="login")
	 */
	public function loginAction(Request $request)
	{
		$authenticationUtils = $this->get('security.authentication_utils');

		// get the login error if there is one
		$error = $authenticationUtils->getLastAuthenticationError();

		// last username entered by the user
		$lastUsername = $authenticationUtils->getLastUsername();

		return $this->render(
			'security/login.html.twig',
			array(
				// last username entered by the user
				'last_username' => $lastUsername,
				'error'         => $error,
			)
		);
	}

	/**
	 * @Route("/redirectafterlogin", name="redirect_after_login")
	 */
	public function redirectafterloginAction(Request $request)
	{
		if ($this->get('security.authorization_checker')->isGranted("ROLE_ADMIN")) return $this->redirectToRoute('admin_dashboard');
		if ($this->get('security.authorization_checker')->isGranted("ROLE_USER")) return $this->redirectToRoute('user_dashboard');
	}

# http://symfony.com/doc/current/cookbook/doctrine/registration_form.html
	/**
	 * @Route("/register", name="user_registration")
	 */
	public function registerAction(Request $request)
	{
		// 1) build the form
		$user = new User();
		$form = $this->createForm(UserRegistrationType::class, $user);

		// 2) handle the submit (will only happen on POST)
		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()) {

			// 3) Encode the password (you could also do this via Doctrine listener)
			$password = $this->get('security.password_encoder')
				->encodePassword($user, $user->getPlainPassword());
			$user->setPassword($password);

			$user->setRole('ROLE_ADMIN');
			$user->setIsActive(1);

			// 4) save the User!
			$em = $this->getDoctrine()->getManager();
			$em->persist($user);
			$em->flush();

			// ... do any other work - like sending them an email, etc
			// maybe set a "flash" success message for the user

			return $this->redirectToRoute('homepage');
		}

		return $this->render(
			'security/register.html.twig',
			array('form' => $form->createView())
		);
	}
}