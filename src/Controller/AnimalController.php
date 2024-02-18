<?php

namespace App\Controller;

use App\Entity\Animal;
use App\Entity\DescriptionAnimal;
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
use App\Util\ValidationUtils;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/animals', name: 'app_animal')]
class AnimalController extends AbstractController
{
    private ManagerRegistry $doctrine;
    private SerializerInterface $serializer;
    private EntityManagerInterface $em;
    private ValidatorInterface $validator; // Ajout du Validator

    public function __construct(ManagerRegistry $doctrine, SerializerInterface $serializer, EntityManagerInterface $em, ValidatorInterface $validator)
    {
        $this->doctrine = $doctrine;
        $this->serializer = $serializer;
        $this->em = $em;
        $this->validator = $validator; // Injection du Validator
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
            return new JsonResponse(['errors' => ValidationUtils::createValidationErrorArray("animal", "Animal non trouvé")], Response::HTTP_NOT_FOUND);
        }

        $jsonAnimal = $this->serializer->serialize($animal, 'json', ['groups' => 'animal']);
        return new JsonResponse($jsonAnimal, Response::HTTP_OK, [], true);
    }

    #[Route('/', name: 'createAnimal', methods: ['POST'])]
    public function createAnimal(Request $request): JsonResponse
    {
        try {
			$animalData = $request->toArray();
            $animal = $this->serializer->deserialize(
                $request->getContent(), 
                Animal::class, 
                'json', 
                ['groups' => 'animal']
            );

			if (isset($animalData['user'])) {
				$user = $this->doctrine->getRepository(User::class)->find($animalData['user']);
                if (!is_null($user)) {
                    $animal->setUser($user);
                }
                else {
                    return new JsonResponse(['errors' => ValidationUtils::createValidationErrorArray("propriétaire", "Aucun propriétaire trouvé")], Response::HTTP_NOT_FOUND);
                }
            } else {
                return new JsonResponse(['errors' => ValidationUtils::createValidationErrorArray("propriétaire", "Aucun propriétaire spécifié")], Response::HTTP_NOT_FOUND);
            }

            $errors = $this->validator->validate($animal);

            if (count($errors) > 0) {
                $formattedErrors = ValidationUtils::formatValidationErrors($errors);

                return new JsonResponse(['errors' => $formattedErrors], Response::HTTP_BAD_REQUEST);
            }

            if (isset($animalData['descriptions'])) {
                foreach ($animalData['descriptions'] as $idDescription) {
                    $descriptionAnimal = $this->doctrine->getRepository(DescriptionAnimal::class)->find($idDescription);
                    if (!is_null($descriptionAnimal)) {
                        $animal->addDescription($descriptionAnimal);
                    }
                    else {
                        return new JsonResponse(['errors' => ValidationUtils::createValidationErrorArray("description", "Aucune description trouvée")], Response::HTTP_NOT_FOUND);
                    }
                }
            }

			$this->em->persist($animal);
			$this->em->flush();

            return new JsonResponse(['success' => ValidationUtils::createValidationErrorArray("animal", "Animal créé avec succès", $animal->getId())], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return new JsonResponse(['errors' => [$e->getMessage()]], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/{id}', name: 'updateAnimal', methods: ['PATCH'])]
    public function updateAnimal(int $id, Request $request): JsonResponse
    {
        try {
            $animalData = $request->toArray();
            $animal = $this->doctrine->getRepository(Animal::class)->find($id);

            if (!$animal) {
                return new JsonResponse(['errors' => ValidationUtils::createValidationErrorArray("animal", "Animal non trouvé")], Response::HTTP_NOT_FOUND);
            }

            if (isset($animalData['descriptions'])) {
                foreach ($animalData['descriptions'] as $idDescription) {
                    $descriptionAnimal = $this->doctrine->getRepository(DescriptionAnimal::class)->find($idDescription);
                    if (!is_null($descriptionAnimal)) {
                        $animal->addDescription($descriptionAnimal);
                    }
                    else {
                        return new JsonResponse(['errors' => ValidationUtils::createValidationErrorArray("description", "Aucune description de l\'animal n\'a été spécifié")], Response::HTTP_NOT_FOUND);
                    }
                }
            }

            $animalUpdate = $this->serializer->deserialize(
				$request->getContent(), 
				Animal::class, 
				'json', 
				[AbstractNormalizer::OBJECT_TO_POPULATE => $animal, ['groups' => 'animal']]
			);

            $errors = $this->validator->validate($animalUpdate);

            if (count($errors) > 0) {
                $formattedErrors = ValidationUtils::formatValidationErrors($errors);

                return new JsonResponse(['errors' => $formattedErrors], Response::HTTP_BAD_REQUEST);
            }

            $this->em->persist($animalUpdate);
            $this->em->flush();

            return new JsonResponse(['success' => ValidationUtils::createValidationErrorArray("animal", "Animal mis à jour")]);
        } catch (\Exception $e) {
            return new JsonResponse(['errors' => ["message" => $e->getMessage()]], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/{id}/uploadPhotoAnimal', name: 'uploadPhotoAnimal', methods: ['POST'])]
    public function uploadPhotoAnimal(int $id, Request $request): JsonResponse
    {
        try {
            $animal = $this->doctrine->getRepository(Animal::class)->find($id);

            if (!$animal) {
                return new JsonResponse(['errors' => ValidationUtils::createValidationErrorArray("animal", "Animal non trouvé")], Response::HTTP_NOT_FOUND);
            }

            $photo = $request->files->get('photo');

            if (!$photo) {
                return new JsonResponse(['errors' => ValidationUtils::createValidationErrorArray("photo", "Aucune photo passée")], Response::HTTP_NOT_FOUND);
            }

            $animal->setPhotoAnimal($photo);

            $this->em->persist($animal);
            $this->em->flush();

            return new JsonResponse(['success' => ValidationUtils::createValidationErrorArray("photo", "Photo de l'animal mise à jour")]);
        } catch (\Exception $e) {
            return new JsonResponse(['errors' => ["message" => $e->getMessage()]], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/{id}/remove-description', name: 'remove_description', methods: ['DELETE'])]
    public function removeDescription(int $id, Request $request): JsonResponse
    {
        try {
            $animal = $this->doctrine->getRepository(Animal::class)->find($id);

            if (!$animal) {
                return new JsonResponse(['errors' => ValidationUtils::createValidationErrorArray("animal", "Animal non trouvé")], Response::HTTP_NOT_FOUND);
            }

            $content = $request->toArray();
            $descriptionIdsToRemove = $content['descriptions'] ?? [];

            foreach ($descriptionIdsToRemove as $descriptionId) {
                $description = $this->doctrine->getRepository(DescriptionAnimal::class)->find($descriptionId);

                if ($description) {
                    $animal->removeDescription($description);
                }
            }

            $this->em->flush();

            return new JsonResponse(['success' => ValidationUtils::createValidationErrorArray("description", "Descriptions supprimées de l\'animal")]);
        } catch (\Exception $e) {
            return new JsonResponse(['errors' => ["message" => $e->getMessage()]], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
