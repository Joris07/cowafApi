<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

#[Route('/api', name: 'login')]
class SecurityController extends AbstractController
{
	#[Route('/login', name:"app_login", methods:["POST"])]
	public function login()
	{
		
	}

	#[Route('/validate', name:"validate_token", methods:["GET"])]
	public function validateToken()
    {
		$user = $this->getUser();

		if ($user instanceof UserInterface) {
			$userId = $user->getId();
			return new JsonResponse(['userId' => $userId]);
		}

		// L'utilisateur n'est pas connecté
		return new JsonResponse(['valid' => false]);
    }
}
