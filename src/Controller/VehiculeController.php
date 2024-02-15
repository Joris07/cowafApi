<?php

namespace App\Controller;

use App\Entity\ModeleVehicule;
use App\Entity\User;
use App\Entity\Vehicule;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/vehicules', name: 'app_vehicule')]
class VehiculeController extends AbstractController
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

    #[Route('/', name: 'vehicules', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $vehicules = $this->doctrine->getRepository(Vehicule::class)->findAll();

        $jsonVehicules = $this->serializer->serialize($vehicules, 'json', ['groups' => 'vehicule']);
        return new JsonResponse($jsonVehicules, Response::HTTP_OK, [], true);
    }

    #[Route('/{id}', name: 'detailVehicule', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $vehicule = $this->doctrine->getRepository(Vehicule::class)->find($id);

        if (!$vehicule) {
            return new JsonResponse(['message' => 'Vehicule non trouvé.'], Response::HTTP_NOT_FOUND);
        }

        $jsonVehicule = $this->serializer->serialize($vehicule, 'json', ['groups' => 'vehicule']);
        return new JsonResponse($jsonVehicule, Response::HTTP_OK, [], true);
    }

    #[Route('/', name: 'createVehicule', methods: ['POST'])]
    public function createVehicule(Request $request): JsonResponse
    {
        try {
            $vehiculeData = $request->toArray();
            $vehicule = $this->serializer->deserialize(
                $request->getContent(), 
                Vehicule::class, 
                'json', 
                ['groups' => 'vehicule']
            );

            $user = $this->doctrine->getRepository(User::class)->find($vehiculeData['user'] ?? -1);
            if (!is_null($user)) {
                $vehicule->setUser($user);
            } else {
                throw new \InvalidArgumentException('Aucun propriétaire n\'a été spécifié ou trouvé');
            }

            $modele = $this->doctrine->getRepository(ModeleVehicule::class)->find($vehiculeData['modele'] ?? -1);
            if (!is_null($modele)) {
                $vehicule->setModele($modele);
            } else {
                throw new \InvalidArgumentException('Aucun véhicule n\'a été spécifié ou trouvé');
            }

            $this->em->persist($vehicule);
            $this->em->flush();

            return new JsonResponse(['message' => 'Vehicule créé avec succès', 'id' => $vehicule->getId()], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/{id}', name: 'updateVehicule', methods: ['PATCH'])]
    public function updateVehicule(int $id, Request $request): JsonResponse
    {
        try {
            $vehicule = $this->doctrine->getRepository(Vehicule::class)->find($id);

            if (!$vehicule) {
                return new JsonResponse(['message' => 'Vehicule non trouvé.'], Response::HTTP_NOT_FOUND);
            }

            $vehicule = $this->serializer->deserialize(
				$request->getContent(), 
				Vehicule::class, 
				'json', 
				[AbstractNormalizer::OBJECT_TO_POPULATE => $vehicule, ['groups' => 'vehicule']]
			);

            $vehiculeData = $request->toArray();

            if (isset($vehiculeData['user'])) {
                $user = $this->doctrine->getRepository(User::class)->find($vehiculeData['user']);
                if (!is_null($user)) {
                    $vehicule->setUser($user);
                }
                else 
                    return new JsonResponse(['message' => 'Utilisateur non trouvé.'], Response::HTTP_NOT_FOUND);
            }
            
            if (isset($vehiculeData['modele'])) {
               $modele = $this->doctrine->getRepository(ModeleVehicule::class)->find($vehiculeData['modele']);
                if (!is_null($modele)) {
                    $vehicule->setModele($modele);
                }
                else 
                    return new JsonResponse(['message' => 'Modele non trouvé.'], Response::HTTP_NOT_FOUND);
            }
            
            $this->em->persist($vehicule);
            $this->em->flush();

            return new JsonResponse(['message' => 'Vehicule mis à jour']);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

}
