<?php

namespace App\Controller;

use App\Entity\Admin;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\SerializerInterface;

class AuthController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private UserPasswordHasherInterface $passwordHasher;
    private SerializerInterface $serializer;

    public function __construct(
        EntityManagerInterface $entityManager, 
        UserPasswordHasherInterface $passwordHasher,
        SerializerInterface $serializer
    ) {
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
        $this->serializer = $serializer;
    }

    #[Route('/api/login', name: 'app_login', methods: ['POST'])]
    public function login(Request $request, JWTTokenManagerInterface $JWTManager): Response
    {
        $data = json_decode($request->getContent(), true);
        $username = $data['username'];
        $password = $data['password'];

        $admin = $this->entityManager->getRepository(Admin::class)->findOneBy(['username' => $username]);

        if (!$admin || !$this->passwordHasher->isPasswordValid($admin, $password)) {
            return $this->json(['message' => 'Identifiants invalides.'], Response::HTTP_UNAUTHORIZED);
        }

        $token = $JWTManager->create($admin);
        $tokenTtl = $this->getParameter('lexik_jwt_authentication.token_ttl') ?? 3600;
        $expiresAt = (new \DateTimeImmutable())->modify("+{$tokenTtl} seconds")->getTimestamp();

        return $this->json([
            'id' => $admin->getId(),
            'username' => $admin->getUsername(),
            'email' => $admin->getEmail(),
            'tokenData' => [
                'expires_at' => $expiresAt,
                'token' => $token,
            ]
        ]);
    }

    #[Route('/admin/{id}', name: 'app_admin_show', methods: ['GET'])]
    public function show(int $id): Response
    {
        $admin = $this->entityManager->getRepository(Admin::class)->find($id);
        if (!$admin) {
            return $this->json(['message' => 'Administrateur non trouvé'], Response::HTTP_NOT_FOUND);
        }

        $data = $this->serializer->serialize($admin, 'json', ['groups' => 'admin:read']);
        return new Response($data, Response::HTTP_OK, ['Content-Type' => 'application/json']);
    }

    #[Route('/admin', name: 'app_admin_index', methods: ['GET'])]
    public function index(): Response
    {
        $admins = $this->entityManager->getRepository(Admin::class)->findAll();
        $data = $this->serializer->serialize($admins, 'json', ['groups' => 'admin:read']);
        return new Response($data, Response::HTTP_OK, ['Content-Type' => 'application/json']);
    }

    #[Route('/admin/{id}', name: 'app_admin_update', methods: ['POST'])]
    public function update(Request $request, int $id): Response
    {
        $admin = $this->entityManager->getRepository(Admin::class)->find($id);
        if (!$admin) {
            return $this->json(['message' => 'Administrateur non trouvé'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);
        
        if (isset($data['username'])) {
            $admin->setUsername($data['username']);
        }
        if (isset($data['email'])) {
            $admin->setEmail($data['email']);
        }
        if (isset($data['password'])) {
            $hashedPassword = $this->passwordHasher->hashPassword($admin, $data['password']);
            $admin->setPassword($hashedPassword);
        }

        $this->entityManager->flush();

        $responseData = $this->serializer->serialize($admin, 'json', ['groups' => 'admin:read']);
        return new Response($responseData, Response::HTTP_OK, ['Content-Type' => 'application/json']);
    }

    #[Route('/admin/{id}', name: 'app_admin_delete', methods: ['DELETE'])]
    public function delete(int $id): Response
    {
        $admin = $this->entityManager->getRepository(Admin::class)->find($id);
        if (!$admin) {
            return $this->json(['message' => 'Administrateur non trouvé'], Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($admin);
        $this->entityManager->flush();

        return $this->json(['message' => 'Administrateur supprimé avec succès'], Response::HTTP_OK);
    }
} 