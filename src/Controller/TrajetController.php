<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Trajet;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[Route('/api/trajets', name: 'app_trajet')]
class TrajetController extends AbstractController
{
	private $doctrine;

	public function __construct(ManagerRegistry $doctrine)
	{
		$this->doctrine = $doctrine;
	}

	#[Route('/', name: 'list', methods: ['GET'])]
	public function index(): JsonResponse
	{
		$repository = $this->doctrine->getRepository(Trajet::class);
		$trajets = $repository->findAll();

		return $this->json($trajets);
	}

	#[Route('/{id}', name: 'show', methods: ['GET'])]
	public function show(int $id): JsonResponse
	{
		$trajet = $this->doctrine->getRepository(Trajet::class)->find($id);

		if (!$trajet) {
			return new JsonResponse(['message' => 'Trajet non trouvé.'], Response::HTTP_NOT_FOUND);
		}

		return $this->json($trajet);
	}

	#[Route('/', name: 'create', methods: ['POST'])]
	public function create(Request $request): JsonResponse
	{
		$data = json_decode($request->getContent(), true);
		// Valider et traiter les données, puis enregistrer dans la base de données
		// ...

		return new JsonResponse(['message' => 'Trajet créé.'], Response::HTTP_CREATED);
	}

	#[Route('/{id}', name: 'update', methods: ['PUT'])]
	public function update(int $id, Request $request): JsonResponse
	{
		$trajet = $this->doctrine->getRepository(Trajet::class)->find($id);

		if (!$trajet) {
			return new JsonResponse(['message' => 'Trajet non trouvé.'], Response::HTTP_NOT_FOUND);
		}

		$data = json_decode($request->getContent(), true);
		// Valider et mettre à jour les données, puis enregistrer dans la base de données
		// ...

		return new JsonResponse(['message' => 'Trajet mis à jour.']);
	}

	#[Route('/{id}', name: 'delete', methods: ['DELETE'])]
	public function delete(int $id): JsonResponse
	{
		$trajet = $this->doctrine->getRepository(Trajet::class)->find($id);

		if (!$trajet) {
			return new JsonResponse(['message' => 'Trajet non trouvé.'], Response::HTTP_NOT_FOUND);
		}

		$entityManager = $this->doctrine->getManager();
		$entityManager->remove($trajet);
		$entityManager->flush();

		return new JsonResponse(['message' => 'Trajet supprimé.']);
	}
}
