<?php

namespace App\Controller;

use App\Entity\Animal;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use App\Entity\User;

#[Route('/api/animals', name: 'app_animal')]
class AnimalController extends AbstractController
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

    #[Route('/', name: 'animals', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $animals = $this->doctrine->getRepository(Animal::class)->findAll();

        $jsonAnimals = $this->serializer->serialize($animals, 'json', ['groups' => 'animal']);
        return new JsonResponse($jsonAnimals, Response::HTTP_OK, [], true);
    }

    #[Route('/{id}', name: 'detailAnimal', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $animal = $this->doctrine->getRepository(Animal::class)->find($id);

        if (!$animal) {
            return new JsonResponse(['message' => 'Animal non trouvé.'], Response::HTTP_NOT_FOUND);
        }

        $jsonAnimal = $this->serializer->serialize($animal, 'json', ['groups' => 'animal']);
        return new JsonResponse($jsonAnimal, Response::HTTP_OK, [], true);
    }

    #[Route('/', name: 'createAnimal', methods: ['POST'])]
    public function createAnimal(Request $request): JsonResponse
    {
        try {
			$animalData = $request->toArray();
            $animal = $this->serializer->deserialize($request->getContent(), Animal::class, 'json', ['groups' => 'animal']);

			if (isset($animalData['user'])) {
				$user = $this->doctrine->getRepository(User::class)->find($animalData['user']);
                $animal->setUser($user);
            } else {
                throw new \InvalidArgumentException('Aucun propriétaire n\'a été spécifié');
            }

			$this->em->persist($animal);
			$this->em->flush();

            return new JsonResponse(['message' => 'Animal créé avec succès', 'id' => $animal->getId()], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/{id}', name: 'updateAnimal', methods: ['PATCH'])]
    public function updateAnimal(int $id, Request $request): JsonResponse
    {
        try {
            $animal = $this->doctrine->getRepository(Animal::class)->find($id);

            if (!$animal) {
                return new JsonResponse(['message' => 'Animal non trouvé.'], Response::HTTP_NOT_FOUND);
            }

			if (empty($request->toArray())) {
				return new JsonResponse(['message' => 'Aucune donnée passée'], Response::HTTP_NOT_FOUND);
			}

            $animalUpdate = $this->serializer->deserialize(
				$request->getContent(), 
				Animal::class, 
				'json', 
				[AbstractNormalizer::OBJECT_TO_POPULATE => $animal, ['groups' => 'animal']]
			);

            $this->em->persist($animalUpdate);
            $this->em->flush();

            return new JsonResponse(['message' => 'Animal mis à jour']);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/{id}/uploadPhotoAnimal', name: 'uploadPhotoAnimal', methods: ['POST'])]
    public function uploadPhotoAnimal(int $id, Request $request): JsonResponse
    {
        try {
            $animal = $this->doctrine->getRepository(Animal::class)->find($id);

            if (!$animal) {
                return new JsonResponse(['message' => 'Animal non trouvé.'], Response::HTTP_NOT_FOUND);
            }

            $photo = $request->files->get('photo');

            if (!$photo) {
                return new JsonResponse(['message' => 'Aucune photo de l\'animal passée.'], Response::HTTP_NOT_FOUND);
            }

            $animal->setPhotoAnimal($photo);

            $this->em->persist($animal);
            $this->em->flush();

            return new JsonResponse(['message' => 'Photo mise à jour']);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
