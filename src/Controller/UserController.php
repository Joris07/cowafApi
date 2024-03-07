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
use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Utils\ValidationUtils;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/users', name: 'app_user')]
class UserController extends AbstractController
{
    private ManagerRegistry $doctrine;
    private SerializerInterface $serializer;
    private EntityManagerInterface $em;
    private UserPasswordHasherInterface $userPasswordHasher;
    private ValidatorInterface $validator;

    public function __construct(ManagerRegistry $doctrine, SerializerInterface $serializer, EntityManagerInterface $em, UserPasswordHasherInterface $userPasswordHasher, ValidatorInterface $validator)
    {
        $this->doctrine = $doctrine;
        $this->serializer = $serializer;
        $this->em = $em;
        $this->userPasswordHasher = $userPasswordHasher;
        $this->validator = $validator;
    }

    #[Route('/', name: 'users', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $repository = $this->doctrine->getRepository(User::class);
        $users = $repository->findAll();

        $jsonUsers = $this->serializer->serialize($users, 'json', ['groups' => 'user']);
        return new JsonResponse($jsonUsers, Response::HTTP_OK, [], true);
    }

    #[Route('/{id}', name: 'detailUser', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $user = $this->doctrine->getRepository(User::class)->find($id);

        if (!$user) {
            return new JsonResponse(['errors' => ValidationUtils::createValidationErrorArray("User", "utilisateur non trouvé")], Response::HTTP_NOT_FOUND);
        }

        $jsonUser = $this->serializer->serialize($user, 'json', ['groups' => 'user']);
        return new JsonResponse($jsonUser, Response::HTTP_OK, [], true);
    }

    #[Route('/register', name: 'createUser', methods: ['POST'])]
    public function createUser(Request $request): JsonResponse
    {
        try {
            $userData = $request->toArray();
            $user = $this->serializer->deserialize($request->getContent(), User::class, 'json', ['groups' => 'user']);
            
            $errors = $this->validator->validate($user);
            
            if (count($errors) > 0) {
                $formattedErrors = ValidationUtils::formatValidationErrors($errors);

                return new JsonResponse(['errors' => $formattedErrors], Response::HTTP_BAD_REQUEST);
            }
    
            if (isset($userData['password'])) {
                $user->setPassword($this->userPasswordHasher->hashPassword($user, $userData['password']));
            } else {
                return new JsonResponse(['errors' => ValidationUtils::createValidationErrorArray("password", "Aucun mot de passe passé")], Response::HTTP_NOT_FOUND);
            }
            
            $this->em->persist($user);
            $this->em->flush();
    
            return new JsonResponse(['success' => ValidationUtils::createValidationErrorArray("user", "Utilisateur créé avec succès", $user->getId())], Response::HTTP_CREATED);

        } catch (\Exception $e) {
            return new JsonResponse(['errors' => ["message" => $e->getMessage()]], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/{id}', name: 'updateUser', methods: ['PATCH'])]
    public function updateUser(int $id, Request $request): JsonResponse
    {
        try {
            $user = $this->doctrine->getRepository(User::class)->find($id);

            if (!$user) {
                return new JsonResponse(['errors' => ValidationUtils::createValidationErrorArray("user", "Utilisateur non trouvé")], Response::HTTP_NOT_FOUND);
            }

            $userUpdate = $this->serializer->deserialize(
                $request->getContent(), 
                User::class, 
                'json', 
                [AbstractNormalizer::OBJECT_TO_POPULATE => $user, ['groups' => 'user']]
            );

            $errors = $this->validator->validate($user);

            if (count($errors) > 0) {
                $formattedErrors = ValidationUtils::formatValidationErrors($errors);

                return new JsonResponse(['errors' => $formattedErrors], Response::HTTP_BAD_REQUEST);
            }

            $this->em->persist($userUpdate);
            $this->em->flush();

            return new JsonResponse(['success' => ValidationUtils::createValidationErrorArray("user", "Utilisateur mis à jour")]);
        } catch (\Exception $e) {
            return new JsonResponse(['errors' => ["message" => $e->getMessage()]], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/{id}/uploadPhotoProfil', name: 'uploadPhotoUser', methods: ['POST'])]
    public function uploadPhotoProfil(int $id, Request $request): JsonResponse
    {
        try {
            $user = $this->doctrine->getRepository(User::class)->find($id);
            
            if (!$user) {
                return new JsonResponse(['errors' => ValidationUtils::createValidationErrorArray("user", "Utilisateur non trouvé")], Response::HTTP_NOT_FOUND);
            }

            $photo = $request->files->get('photo');

            if (!$photo) {
                return new JsonResponse(['errors' => ValidationUtils::createValidationErrorArray("photo", 'Aucune photo de profil passée')], Response::HTTP_NOT_FOUND);
            }

            $user->setPhotoProfil($photo);

            $this->em->persist($user);
            $this->em->flush();

            return new JsonResponse(['success' => ValidationUtils::createValidationErrorArray("photo", "Photo mise à jour")]);
        } catch (\Exception $e) {
            return new JsonResponse(['errors' => ["message" => $e->getMessage()]], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
