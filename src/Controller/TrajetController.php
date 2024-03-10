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
use App\Repository\TrajetRepository;
use App\Utils\ValidationUtils;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/trajets', name: 'app_trajet')]
class TrajetController extends AbstractController
{
    private ManagerRegistry $doctrine;
    private SerializerInterface $serializer;
    private EntityManagerInterface $em;
    private ValidatorInterface $validator;

    public function __construct(ManagerRegistry $doctrine, SerializerInterface $serializer, EntityManagerInterface $em, ValidatorInterface $validator)
    {
        $this->doctrine = $doctrine;
        $this->serializer = $serializer;
        $this->em = $em;
        $this->validator = $validator;
    }

    #[Route('/', name: 'trajets', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $repository = $this->doctrine->getRepository(Trajet::class);
        $trajets = $repository->findAll();

        $jsonTrajets = $this->serializer->serialize($trajets, 'json', ['groups' => 'trajet']);
        return new JsonResponse($jsonTrajets, Response::HTTP_OK, [], true);
    }


    #[Route('/participate', name: 'trajets_by_user', methods: ['GET'])]
    public function getTrajetsByUser(TrajetRepository $trajetRepository, SerializerInterface $serializer)
    {
        $user = $this->getUser();

        if (!$user) {
            return new JsonResponse(['error' => 'User not authenticated'], Response::HTTP_UNAUTHORIZED);
        }

        $trajets = $trajetRepository->findTrajetsByUserAndAnimals($user);
        $jsonTrajets = $serializer->serialize($trajets, 'json', ['groups' => 'trajet']);

        return new JsonResponse($jsonTrajets, Response::HTTP_OK, [], true);
    }

    #[Route('/{id}', name: 'detailTrajet', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $trajet = $this->doctrine->getRepository(Trajet::class)->find($id);

        if (!$trajet) {
            return new JsonResponse(['errors' => ValidationUtils::createValidationErrorArray("trajet", "Trajet non trouvé")], Response::HTTP_NOT_FOUND);
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
                return new JsonResponse(['errors' => ValidationUtils::createValidationErrorArray("user", "User non trouvé")], Response::HTTP_NOT_FOUND);
            }

            $trajet = $this->serializer->deserialize($request->getContent(), Trajet::class, 'json', ['groups' => 'trajet']);
            $trajet->setUser($user);

            $animauxIds = $content["animaux"] ?? [];
            $animaux = $this->doctrine->getRepository(Animal::class)->findBy(['id' => $animauxIds]);

            if (count($animaux) !== count($animauxIds)) {
				return new JsonResponse(['errors' => ValidationUtils::createValidationErrorArray("animaux", 'Un ou plusieurs animaux non trouvés')], Response::HTTP_NOT_FOUND);
            }

            foreach ($animaux as $animal) {
                $trajet->addAnimauxQuiVoyage($animal);
            }

			$errors = $this->validator->validate($trajet);

            if (count($errors) > 0) {
                $formattedErrors = ValidationUtils::formatValidationErrors($errors);

                return new JsonResponse(['errors' => $formattedErrors], Response::HTTP_BAD_REQUEST);
            }

            $this->em->persist($trajet);
            $this->em->flush();

            return new JsonResponse(['success' => ValidationUtils::createValidationErrorArray("trajet", "Trajet créé avec succès", $trajet->getId())], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return new JsonResponse(['errors' => ['message' => $e->getMessage()]], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/{id}', name: 'update', methods: ['PATCH'])]
    public function update(int $id, Request $request): JsonResponse
    {
        try {
            $content = $request->toArray();
            $trajet = $this->doctrine->getRepository(Trajet::class)->find($id);

            if (!$trajet) {
                return new JsonResponse(['errors' => ValidationUtils::createValidationErrorArray("trajet", "Trajet non trouvé")], Response::HTTP_NOT_FOUND);
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
                return new JsonResponse(['errors' => ValidationUtils::createValidationErrorArray("trajet", "Le nombre d'animaux ne peut pas dépasser le nombre de places disponibles")], Response::HTTP_BAD_REQUEST);
            }

            $animauxIds = $content["animaux"] ?? [];
            $animaux = $this->doctrine->getRepository(Animal::class)->findBy(['id' => $animauxIds]);

            if (count($animaux) !== count($animauxIds)) {
                return new JsonResponse(['errors' => ValidationUtils::createValidationErrorArray("animaux", 'Un ou plusieurs animaux non trouvés')], Response::HTTP_NOT_FOUND);
            }

            foreach ($animaux as $animal) {
                $trajetUpdate->addAnimauxQuiVoyage($animal);
            }

            $errors = $this->validator->validate($trajetUpdate);

            if (count($errors) > 0) {
                $formattedErrors = ValidationUtils::formatValidationErrors($errors);

                return new JsonResponse(['errors' => $formattedErrors], Response::HTTP_BAD_REQUEST);
            }

            $this->em->persist($trajetUpdate);
            $this->em->flush();

            return new JsonResponse(['success' => ValidationUtils::createValidationErrorArray("trajet", "Trajet mis à jour")]);
        } catch (\Exception $e) {
            return new JsonResponse(['errors' => ['message' => $e->getMessage()]], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        try {
            $trajet = $this->doctrine->getRepository(Trajet::class)->find($id);

            if (!$trajet) {
                return new JsonResponse(['errors' => ValidationUtils::createValidationErrorArray("trajet", "Trajet non trouvé")], Response::HTTP_NOT_FOUND);
            }

            $trajet->setDateSuppression(new \DateTime());
            $this->em->flush();

            return new JsonResponse(['success' => ValidationUtils::createValidationErrorArray("trajet", "Trajet supprimé avec succès")]);
        } catch (\Exception $e) {
            return new JsonResponse(['errors' => ['message' => $e->getMessage()]], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/{id}/remove-animals', name: 'remove_animals', methods: ['DELETE'])]
    public function removeAnimals(int $id, Request $request): JsonResponse
    {
        try {
            $trajet = $this->doctrine->getRepository(Trajet::class)->find($id);

            if (!$trajet) {
                return new JsonResponse(['errors' => ValidationUtils::createValidationErrorArray("Trajet", "Trajet non trouvé")], Response::HTTP_NOT_FOUND);
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

            return new JsonResponse(['success' => ValidationUtils::createValidationErrorArray("trajet", "Animaux supprimés du trajet")]);
        } catch (\Exception $e) {
            return new JsonResponse(['errors' => ['message' => $e->getMessage()]], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
