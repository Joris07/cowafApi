<?php

namespace App\Controller;

use App\Entity\Facture;
use App\Entity\Trajet;
use App\Entity\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use App\Util\ValidationUtils;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/factures', name: 'factures')]
class FactureController extends AbstractController
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

    #[Route('/', name: 'listFactures', methods: ['GET'])]
    public function listFactures(): JsonResponse
    {
        $facturesList = $this->doctrine->getRepository(Facture::class)->findAll();

        $jsonFacturesList = $this->serializer->serialize($facturesList, 'json', ['groups' => 'facture']);
        return new JsonResponse($jsonFacturesList, Response::HTTP_OK, [], true);
    }

    #[Route('/{id}', name: 'detailFacture', methods: ['GET'])]
    public function detailFacture(int $id): JsonResponse
    {
        $facture = $this->doctrine->getRepository(Facture::class)->find($id);

        if (!$facture) {
            return new JsonResponse(['errors' => ValidationUtils::createValidationErrorArray("Facture", "Facture non trouvée")], Response::HTTP_NOT_FOUND);
        }

        $jsonFacture = $this->serializer->serialize($facture, 'json', ['groups' => 'facture']);
        return new JsonResponse($jsonFacture, Response::HTTP_OK, [], true);
    }

    #[Route('/', name: 'createFacture', methods: ['POST'])]
    public function createFacture(Request $request): JsonResponse
    {
        try {
            $factureData = $request->toArray();
            $facture = $this->serializer->deserialize(
                $request->getContent(),
                Facture::class,
                'json',
                ['groups' => 'facture']
            );

            $errors = $this->validator->validate($facture);

            if (count($errors) > 0) {
                $formattedErrors = ValidationUtils::formatValidationErrors($errors);

                return new JsonResponse(['errors' => $formattedErrors], Response::HTTP_BAD_REQUEST);
            }

            $user = $this->doctrine->getRepository(User::class)->find((isset($factureData["user"]) ? $factureData["user"] : ""));
            if (!is_null($user)) {
                $facture->setUser($user);
            } else {
                return new JsonResponse(['errors' => ValidationUtils::createValidationErrorArray("user", "User non trouvé")], Response::HTTP_NOT_FOUND);
            }

            $trajet = $this->doctrine->getRepository(Trajet::class)->find((isset($factureData["trajet"]) ? $factureData["trajet"] : ""));
            if (!is_null($trajet)) {
                $facture->setTrajet($trajet);
            } else {
                return new JsonResponse(['errors' => ValidationUtils::createValidationErrorArray("trajet", "Trajet non trouvé")], Response::HTTP_NOT_FOUND);
            }

            $this->em->persist($facture);
            $this->em->flush();

            return new JsonResponse(['success' => ValidationUtils::createValidationErrorArray("avis", "Facture créée avec succès", $facture->getId())], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return new JsonResponse(['errors' => ['message' => $e->getMessage()]], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/{id}', name: 'updateFacture', methods: ['PATCH'])]
    public function updateFacture(int $id, Request $request): JsonResponse
    {
        try {
            $factureData = $request->toArray();
            $facture = $this->doctrine->getRepository(Facture::class)->find($id);

            if (!$facture) {
                return new JsonResponse(['errors' => ValidationUtils::createValidationErrorArray("Facture", "Facture non trouvée")], Response::HTTP_NOT_FOUND);
            }

            $factureUpdate = $this->serializer->deserialize(
                $request->getContent(),
                Facture::class,
                'json',
                [AbstractNormalizer::OBJECT_TO_POPULATE => $facture, 'groups' => 'facture']
            );

            if (isset($factureData["user"])) {
                $user = $this->doctrine->getRepository(User::class)->find($factureData["user"]);
                if (!is_null($user)) {
                    $facture->setUser($user);
                } else {
                    return new JsonResponse(['errors' => ValidationUtils::createValidationErrorArray("user", "User non trouvé")], Response::HTTP_NOT_FOUND);
                }
            }

            if (isset($factureData["trajet"])) {
                $trajet = $this->doctrine->getRepository(Trajet::class)->find($factureData["trajet"]);
                if (!is_null($trajet)) {
                    $facture->setTrajet($trajet);
                } else {
                    return new JsonResponse(['errors' => ValidationUtils::createValidationErrorArray("trajet", "Trajet non trouvé")], Response::HTTP_NOT_FOUND);
                }
            }

            $errors = $this->validator->validate($facture);

            if (count($errors) > 0) {
                $formattedErrors = ValidationUtils::formatValidationErrors($errors);

                return new JsonResponse(['errors' => $formattedErrors], Response::HTTP_BAD_REQUEST);
            }

            $this->em->persist($factureUpdate);
            $this->em->flush();

            return new JsonResponse(['success' => ValidationUtils::createValidationErrorArray("facture", "Facture mise à jour")]);
        } catch (\Exception $e) {
            return new JsonResponse(['errors' => ['message' => $e->getMessage()]], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}