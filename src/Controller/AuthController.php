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

class AuthController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher)
    {
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
    }

    #[Route('/login', name: 'app_login', methods: ['POST'])]
    public function login(Request $request, JWTTokenManagerInterface $JWTManager): Response
    {
        $data = json_decode($request->getContent(), true);
        $username = $data['username'];
        $password = $data['password'];

        $admin = $this->entityManager->getRepository(Admin::class)->findOneBy(['username' => $username]);

        if (!$admin || !$this->passwordHasher->isPasswordValid($admin, $password)) {
            throw new AuthenticationException('Invalid credentials.');
        }

        $token = $JWTManager->create($admin);

        return $this->json(['token' => $token,
            'username' => $admin->getUsername(),
            'email' => $admin->getEmail(),
        ]);
    }
} 