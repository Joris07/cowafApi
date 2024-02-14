<?php

namespace App\Controller;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Trajet;
use App\Entity\User;
use App\Entity\Animal;

#[Route('/api/trajets', name: 'app_trajet')]
class TrajetController extends AbstractController
{
	private ManagerRegistry $doctrine;
	private SerializerInterface $serializer;
	private EntityManagerInterface $em;

	public function __construct(ManagerRegistry $doctrine, SerializerInterface $serializer, EntityManagerInterface $em)
	{
		$this->doctrine = $doctrine;
		$this->serializer = $serializer;
		$this->em = $em;
	}

	#[Route('/', name: 'trajets', methods: ['GET'])]
	public function index(): JsonResponse
	{
		$repository = $this->doctrine->getRepository(Trajet::class);
		$trajets = $repository->findAll();

		$jsonTrajets = $this->serializer->serialize($trajets, 'json', ['groups' => 'trajet']);
		return new JsonResponse($jsonTrajets, Response::HTTP_OK, [], true);
	}

	#[Route('/{id}', name: 'detailTrajet', methods: ['GET'])]
	public function show(int $id): JsonResponse
	{
		$trajet = $this->doctrine->getRepository(Trajet::class)->find($id);

		if (!$trajet) {
			return new JsonResponse(['message' => 'Trajet non trouvé.'], Response::HTTP_NOT_FOUND);
		}

		$jsonTrajet = $this->serializer->serialize($trajet, 'json', ['groups' => 'trajet']);
		return new JsonResponse($jsonTrajet, Response::HTTP_OK, [], true);
	}

	#[Route('/', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        try {
            $content = $request->toArray();

            $idUser = $content['user'] ?? null;
            $user = $this->doctrine->getRepository(User::class)->find($idUser);

            if (!$user) {
                throw new \InvalidArgumentException('Utilisateur inexistant');
            }

            $trajet = $this->serializer->deserialize($request->getContent(), Trajet::class, 'json', ['groups' => 'trajet']);
            $trajet->setUser($user);
			
            $animauxIds = $content["animaux"] ?? [];
            $animaux = $this->doctrine->getRepository(Animal::class)->findBy(['id' => $animauxIds]);

            if (count($animaux) !== count($animauxIds)) {
                throw new \InvalidArgumentException('Un ou plusieurs animaux non trouvés');
            }			

            foreach ($animaux as $animal) {
                $trajet->addAnimauxQuiVoyage($animal);
            }

            $this->em->persist($trajet);
            $this->em->flush();

            return new JsonResponse(['message' => 'Trajet créé avec succès', 'id' => $trajet->getId()], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

	#[Route('/{id}', name: 'update', methods: ['PATCH'])]
	public function update(int $id, Request $request): JsonResponse
	{
		try {
			$content = $request->toArray();
			$trajet = $this->doctrine->getRepository(Trajet::class)->find($id);

			if (!$trajet) {
				return new JsonResponse(['message' => 'Trajet non trouvé.'], Response::HTTP_NOT_FOUND);
			}

			if (empty($request->toArray())) {
				return new JsonResponse(['message' => 'Aucune donnée passée'], Response::HTTP_NOT_FOUND);
			}

			$trajetUpdate = $this->serializer->deserialize(
				$request->getContent(), 
				Trajet::class, 
				'json', 
				[AbstractNormalizer::OBJECT_TO_POPULATE => $trajet, 'groups' => 'trajet']
			);

			$placesDisponible = $trajetUpdate->getPlacesDisponible();
			$nombreAnimaux = count($trajetUpdate->getAnimaux());

			if ($nombreAnimaux > $placesDisponible) {
				return new JsonResponse(['message' => 'Le nombre d\'animaux ne peut pas dépasser le nombre de places disponibles.'], Response::HTTP_BAD_REQUEST);
			}

			$animauxIds = $content["animaux"] ?? [];
            $animaux = $this->doctrine->getRepository(Animal::class)->findBy(['id' => $animauxIds]);
			
            if (count($animaux) !== count($animauxIds)) {
                throw new \InvalidArgumentException('Un ou plusieurs animaux non trouvés');
            }
			
            foreach ($animaux as $animal) {
                $trajetUpdate->addAnimauxQuiVoyage($animal);
            }

			$this->em->persist($trajetUpdate);
			$this->em->flush();

			return new JsonResponse(['message' => 'Trajet mis à jour']);
		} catch (\Exception $e) {
			return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
		}
	}

	#[Route('/{id}', name: 'delete', methods: ['DELETE'])]
	public function delete(int $id): JsonResponse
	{
		try {
			$trajet = $this->doctrine->getRepository(Trajet::class)->find($id);

			if (!$trajet) {
				return new JsonResponse(['message' => 'Trajet non trouvé.'], Response::HTTP_NOT_FOUND);
			}

			$trajet->setDateSuppression(new \DateTime());
			$this->em->flush();

			return new JsonResponse(['message' => 'Trajet supprimé']);
		} catch (\Exception $e) {
			return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
		}
	}

	#[Route('/{id}/remove-animals', name: 'remove_animals', methods: ['DELETE'])]
	public function removeAnimals(int $id, Request $request): JsonResponse
	{
		try {
			$trajet = $this->doctrine->getRepository(Trajet::class)->find($id);

			if (!$trajet) {
				return new JsonResponse(['message' => 'Trajet non trouvé.'], Response::HTTP_NOT_FOUND);
			}

			$content = $request->toArray();
			$animalIdsToRemove = $content['animaux'] ?? [];

			foreach ($animalIdsToRemove as $animalId) {
				$animal = $this->doctrine->getRepository(Animal::class)->find($animalId);

				if ($animal) {
					$trajet->removeAnimauxQuiVoyage($animal);
				}
			}

			$this->em->flush();

			return new JsonResponse(['message' => 'Animaux supprimés du trajet']);
		} catch (\Exception $e) {
			return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
		}
	}
}
