<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class SecurityController extends AbstractController
{
	#[Route('/api/login', name:"app_login", methods:["POST"])]
	public function login(Request $request, UserPasswordHasherInterface $passwordHasher, JWTTokenManagerInterface $JWTManager, ManagerRegistry $doctrine)
	{
		$credentials = json_decode($request->getContent(), true);

		if (empty($credentials['email']) || empty($credentials['password'])) {
			throw new BadCredentialsException('Invalid credentials');
		}

		$user = $doctrine->getRepository(User::class)->findOneBy(['email' => $credentials['email']]);

		if (!$user || !$passwordHasher->isPasswordValid($user, $credentials['password'])) {
			throw new BadCredentialsException('Invalid credentials');
		}

		$token = $JWTManager->create($user);

		return new JsonResponse(['token' => $token]);
	}

	#[Route('/', name:"app")]
	public function app()
	{
		return new JsonResponse(['token' => "test"]);
	}
}
