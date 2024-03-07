<?php

namespace App\Controller;

use App\Entity\Avis;
use App\Entity\User;
use App\Repository\AvisRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Utils\ValidationUtils;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/avis', name: 'avis')]
class AvisController extends AbstractController
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

	#[Route('/', name: 'listAvis', methods: ['GET'])]
	public function listAvis(): JsonResponse
	{
		$avisList = $this->doctrine->getRepository(Avis::class)->findAll();

		$jsonAvisList = $this->serializer->serialize($avisList, 'json', ['groups' => 'avis']);
		return new JsonResponse($jsonAvisList, Response::HTTP_OK, [], true);
	}

	#[Route('/{id}', name: 'detailAvis', methods: ['GET'])]
	public function detailAvis(int $id): JsonResponse
	{
		$avis = $this->doctrine->getRepository(Avis::class)->find($id);

		if (!$avis) {
			return new JsonResponse(['errors' => ValidationUtils::createValidationErrorArray("Avis", "Avis non trouvé")], Response::HTTP_NOT_FOUND);
		}

		$jsonAvis = $this->serializer->serialize($avis, 'json', ['groups' => 'avis']);
		return new JsonResponse($jsonAvis, Response::HTTP_OK, [], true);
	}

	#[Route('/', name: 'createAvis', methods: ['POST'])]
	public function createAvis(Request $request): JsonResponse
	{
		try {
			$avisData = $request->toArray();
			$avis = $this->serializer->deserialize(
				$request->getContent(),
				Avis::class,
				'json',
				['groups' => 'avis']
			);

			$errors = $this->validator->validate($avis);

			if (count($errors) > 0) {
				$formattedErrors = ValidationUtils::formatValidationErrors($errors);

				return new JsonResponse(['errors' => $formattedErrors], Response::HTTP_BAD_REQUEST);
			}

			$auteur = $this->doctrine->getRepository(User::class)->find((isset($avisData["auteur"]) ? $avisData["auteur"] : ""));
			if (!is_null($auteur)) {
				$avis->setAuteur($auteur);
			} else {
				return new JsonResponse(['errors' => ValidationUtils::createValidationErrorArray("auteur", "Auteur non trouvé")], Response::HTTP_NOT_FOUND);
			}

			$destinataire = $this->doctrine->getRepository(User::class)->find((isset($avisData["destinataire"]) ? $avisData["destinataire"] : ""));
			if (!is_null($destinataire)) {
				$avis->setDestinataire($destinataire);
			} else {
				return new JsonResponse(['errors' => ValidationUtils::createValidationErrorArray("destinataire", "Destinataire non trouvé")], Response::HTTP_NOT_FOUND);
			}

			$this->em->persist($avis);
			$this->em->flush();

			return new JsonResponse(['success' => ValidationUtils::createValidationErrorArray("avis", "Avis créé avec succès", $avis->getId())], Response::HTTP_CREATED);
		} catch (\Exception $e) {
			return new JsonResponse(['errors' => ['message' => $e->getMessage()]], Response::HTTP_BAD_REQUEST);
		}
	}

	#[Route('/{id}', name: 'updateAvis', methods: ['PATCH'])]
	public function updateAvis(int $id, Request $request): JsonResponse
	{
		try {
			$avisData = $request->toArray();
			$avis = $this->doctrine->getRepository(Avis::class)->find($id);

			if (!$avis) {
				return new JsonResponse(['errors' => ValidationUtils::createValidationErrorArray("Avis", "Avis non trouvé")], Response::HTTP_NOT_FOUND);
			}

			$avisUpdate = $this->serializer->deserialize(
				$request->getContent(),
				Avis::class,
				'json',
				[AbstractNormalizer::OBJECT_TO_POPULATE => $avis, 'groups' => 'avis']
			);

			if (isset($avisData["auteur"])) {
				$auteur = $this->doctrine->getRepository(User::class)->find($avisData["auteur"]);
				if (!is_null($auteur)) {
					$avis->setAuteur($auteur);
				} else {
					return new JsonResponse(['errors' => ValidationUtils::createValidationErrorArray("auteur", "Auteur non trouvé")], Response::HTTP_NOT_FOUND);
				}
			}

			if (isset($avisData["destinataire"])) {
				$destinataire = $this->doctrine->getRepository(User::class)->find($avisData["destinataire"]);
				if (!is_null($destinataire)) {
					$avis->setDestinataire($destinataire);
				} else {
					return new JsonResponse(['errors' => ValidationUtils::createValidationErrorArray("destinataire", "Destinataire non trouvé")], Response::HTTP_NOT_FOUND);
				}
			}

			$errors = $this->validator->validate($avis);

            if (count($errors) > 0) {
                $formattedErrors = ValidationUtils::formatValidationErrors($errors);

                return new JsonResponse(['errors' => $formattedErrors], Response::HTTP_BAD_REQUEST);
            }

			$this->em->persist($avisUpdate);
			$this->em->flush();

			return new JsonResponse(['success' => ValidationUtils::createValidationErrorArray("avis", "Avis mis à jour")]);
		} catch (\Exception $e) {
			return new JsonResponse(['errors' => ['message' => $e->getMessage()]], Response::HTTP_INTERNAL_SERVER_ERROR);
		}
	}

	#[Route('/delete/{id}', name: 'deleteAvis', methods: ['DELETE'])]
	#[IsGranted("ROLE_ADMIN")]
	public function deleteAvis(int $id, AvisRepository $avisRepository): JsonResponse
	{
		$avis = $avisRepository->find($id);

		if (!$avis) {
			return new JsonResponse(['errors' => ValidationUtils::createValidationErrorArray("Avis", "Avis non trouvé")], Response::HTTP_NOT_FOUND);
		}

		$this->em->remove($avis);
		$this->em->flush();

		return new JsonResponse(['success' => ValidationUtils::createValidationErrorArray("avis", "Avis supprimé avec succès")]);
	}
}
