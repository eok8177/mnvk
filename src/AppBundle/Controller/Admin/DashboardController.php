<?php
namespace AppBundle\Controller\Admin;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Symfony\Component\HttpFoundation\Request;

class DashboardController extends Controller
{
	/**
	 * @Route("/admin", name="admin_dashboard")
	 */
	public function indexAction()
	{
		return $this->render('admin/dashboard.html.twig', [
			'current' => ['controller' => 'dashboard', 'action' => 'index'],
			]);
	}
}