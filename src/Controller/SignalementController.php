<?php

namespace App\Controller;

use App\Entity\Signalement;
use App\Entity\User;
use App\Repository\SignalementRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use App\Utils\ValidationUtils;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/signalements', name: 'signalements')]
class SignalementController extends AbstractController
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

    #[Route('/', name: 'listSignalements', methods: ['GET'])]
    public function listSignalements(): JsonResponse
    {
        $signalementsList = $this->doctrine->getRepository(Signalement::class)->findAll();

        $jsonSignalementsList = $this->serializer->serialize($signalementsList, 'json', ['groups' => 'signalement']);
        return new JsonResponse($jsonSignalementsList, Response::HTTP_OK, [], true);
    }

    #[Route('/{id}', name: 'detailSignalement', methods: ['GET'])]
    public function detailSignalement(int $id): JsonResponse
    {
        $signalement = $this->doctrine->getRepository(Signalement::class)->find($id);

        if (!$signalement) {
            return new JsonResponse(['errors' => ValidationUtils::createValidationErrorArray("signalement", "Signalement non trouvé")], Response::HTTP_NOT_FOUND);
        }

        $jsonSignalement = $this->serializer->serialize($signalement, 'json', ['groups' => 'signalement']);
        return new JsonResponse($jsonSignalement, Response::HTTP_OK, [], true);
    }

    #[Route('/', name: 'createSignalement', methods: ['POST'])]
    public function createSignalement(Request $request): JsonResponse
    {
        try {
            $signalementData = $request->toArray();
            $signalement = $this->serializer->deserialize(
                $request->getContent(),
                Signalement::class,
                'json',
                ['groups' => 'signalement']
            );

            $errors = $this->validator->validate($signalement);

            if (count($errors) > 0) {
                $formattedErrors = ValidationUtils::formatValidationErrors($errors);

                return new JsonResponse(['errors' => $formattedErrors], Response::HTTP_BAD_REQUEST);
            }

            $destinataire = $this->doctrine->getRepository(User::class)->find((isset($signalementData["destinataire"]) ? $signalementData["destinataire"] : ""));
            if (!is_null($destinataire)) {
                $signalement->setDestinataire($destinataire);
            } else {
                return new JsonResponse(['errors' => ValidationUtils::createValidationErrorArray("destinataire", "Destinataire non trouvé")], Response::HTTP_NOT_FOUND);
            }

            $auteur = $this->doctrine->getRepository(User::class)->find((isset($signalementData["auteur"]) ? $signalementData["auteur"] : ""));
            if (!is_null($auteur)) {
                $signalement->setAuteur($auteur);
            } else {
                return new JsonResponse(['errors' => ValidationUtils::createValidationErrorArray("auteur", "Auteur non trouvé")], Response::HTTP_NOT_FOUND);
            }

            $this->em->persist($signalement);
            $this->em->flush();

            return new JsonResponse(['success' => ValidationUtils::createValidationErrorArray("signalement", "Signalement créé avec succès", $signalement->getId())], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return new JsonResponse(['errors' => ['message' => $e->getMessage()]], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/{id}', name: 'updateSignalement', methods: ['PATCH'])]
    public function updateSignalement(int $id, Request $request): JsonResponse
    {
        try {
            $signalementData = $request->toArray();
            $signalement = $this->doctrine->getRepository(Signalement::class)->find($id);

            if (!$signalement) {
                return new JsonResponse(['errors' => ValidationUtils::createValidationErrorArray("signalement", "Signalement non trouvé")], Response::HTTP_NOT_FOUND);
            }

            $signalementUpdate = $this->serializer->deserialize(
                $request->getContent(),
                Signalement::class,
                'json',
                [AbstractNormalizer::OBJECT_TO_POPULATE => $signalement, 'groups' => 'signalement']
            );

            if (isset($signalementData["destinataire"])) {
                $destinataire = $this->doctrine->getRepository(User::class)->find($signalementData["destinataire"]);
                if (!is_null($destinataire)) {
                    $signalement->setDestinataire($destinataire);
                } else {
                    return new JsonResponse(['errors' => ValidationUtils::createValidationErrorArray("destinataire", "Destinataire non trouvé")], Response::HTTP_NOT_FOUND);
                }
            }

            if (isset($signalementData["auteur"])) {
                $auteur = $this->doctrine->getRepository(User::class)->find($signalementData["auteur"]);
                if (!is_null($auteur)) {
                    $signalement->setAuteur($auteur);
                } else {
                    return new JsonResponse(['errors' => ValidationUtils::createValidationErrorArray("auteur", "Auteur non trouvé")], Response::HTTP_NOT_FOUND);
                }
            }

            $errors = $this->validator->validate($signalement);

            if (count($errors) > 0) {
                $formattedErrors = ValidationUtils::formatValidationErrors($errors);

                return new JsonResponse(['errors' => $formattedErrors], Response::HTTP_BAD_REQUEST);
            }

            $this->em->persist($signalementUpdate);
            $this->em->flush();

            return new JsonResponse(['success' => ValidationUtils::createValidationErrorArray("signalement", "Signalement mis à jour")]);
        } catch (\Exception $e) {
            return new JsonResponse(['errors' => ['message' => $e->getMessage()]], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}