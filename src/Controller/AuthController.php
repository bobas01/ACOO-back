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
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use App\Service\EmailService;

class AuthController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private UserPasswordHasherInterface $passwordHasher;
    private SerializerInterface $serializer;
    private EmailService $emailService;

    public function __construct(
        EntityManagerInterface $entityManager, 
        UserPasswordHasherInterface $passwordHasher,
        SerializerInterface $serializer,
        EmailService $emailService
    ) {
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
        $this->serializer = $serializer;
        $this->emailService = $emailService;
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

    #[Route('/admin/forgot-password', name: 'app_admin_forgot_password', methods: ['POST'])]
    public function forgotPassword(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);
        if (!isset($data['email'])) {
            return $this->json(['message' => 'Email requis'], Response::HTTP_BAD_REQUEST);
        }
        
        $admin = $this->entityManager->getRepository(Admin::class)->findOneBy(['email' => $data['email']]);
        if (!$admin) {
            // Pour des raisons de sécurité, on ne révèle pas si l'email existe ou non
            return $this->json(['message' => 'Si cet email existe, un email de réinitialisation a été envoyé'], Response::HTTP_OK);
        }
        
        $token = bin2hex(random_bytes(32));
        $expiresAt = new \DateTime();
        $expiresAt->add(new \DateInterval('PT24H')); // 24 heures
        
        $admin->setResetToken($token);
       
        $this->entityManager->flush();
        
        // Envoi de l'email avec le service dédié
        try {
            $emailSent = $this->emailService->sendPasswordResetEmail($admin, $token, $request);
        } catch (\Exception $e) {
            // En cas d'erreur d'envoi, on supprime le token pour éviter les fuites
            $admin->setResetToken(null);
            $this->entityManager->flush();
            
            return $this->json([
                'message' => 'Erreur lors de l\'envoi de l\'email: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        
        if (!$emailSent) {
            // En cas d'erreur d'envoi, on supprime le token pour éviter les fuites
            $admin->setResetToken(null);
            $this->entityManager->flush();
            
            return $this->json(['message' => 'Erreur lors de l\'envoi de l\'email'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        
        return $this->json(['message' => 'Un email de réinitialisation a été envoyé'], Response::HTTP_OK);
    }

    #[Route('/admin/reset-password', name: 'app_admin_reset_password', methods: ['POST'])]
    public function resetPassword(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);
        if (!isset($data['token'], $data['password'])) {
            return $this->json(['message' => 'Token et nouveau mot de passe requis'], Response::HTTP_BAD_REQUEST);
        }
        $admin = $this->entityManager->getRepository(Admin::class)->findOneBy(['resetToken' => $data['token']]);
        if (!$admin) {
            return $this->json(['message' => 'Token invalide'], Response::HTTP_BAD_REQUEST);
        }
        // Vérification de la robustesse du mot de passe (mêmes règles que register)
        $password = $data['password'];
        $passwordErrors = [];
        if (strlen($password) < 12) {
            $passwordErrors[] = 'Le mot de passe doit contenir au moins 12 caractères.';
        }
        if (!preg_match('/[A-Z]/', $password)) {
            $passwordErrors[] = 'Le mot de passe doit contenir au moins une majuscule.';
        }
        if (!preg_match('/[a-z]/', $password)) {
            $passwordErrors[] = 'Le mot de passe doit contenir au moins une minuscule.';
        }
        if (!preg_match('/[0-9]/', $password)) {
            $passwordErrors[] = 'Le mot de passe doit contenir au moins un chiffre.';
        }
        if (!preg_match('/[\W_]/', $password)) {
            $passwordErrors[] = 'Le mot de passe doit contenir au moins un caractère spécial.';
        }
        if (!empty($passwordErrors)) {
            return $this->json(['errors' => $passwordErrors], Response::HTTP_BAD_REQUEST);
        }
        $hashedPassword = $this->passwordHasher->hashPassword($admin, $password);
        $admin->setPassword($hashedPassword);
        $admin->setResetToken(null);
        $this->entityManager->flush();
        return $this->json(['message' => 'Mot de passe réinitialisé avec succès'], Response::HTTP_OK);
    }

      #[Route('/admin/{id}', name: 'app_admin_update', methods: ['POST'])]
    public function update(Request $request, int $id): Response
    {
        $admin = $this->entityManager->getRepository(Admin::class)->find($id);
        if (!$admin) {
            return $this->json(['message' => 'Administrateur non trouvé'], Response::HTTP_NOT_FOUND);
        }

        // Vérification de l'identité : seul l'admin connecté peut modifier son profil
        $user = $this->getUser();
        if (!$user instanceof Admin || $user->getId() !== $admin->getId()) {
            return $this->json(['message' => 'Accès refusé'], Response::HTTP_FORBIDDEN);
        }

        $data = json_decode($request->getContent(), true);
        if (isset($data['username'])) {
            $admin->setUsername($data['username']);
        }
        if (isset($data['email'])) {
            $admin->setEmail($data['email']);
        }
        // Changement de mot de passe sécurisé
        if (isset($data['password'])) {
            if (!isset($data['oldPassword'])) {
                return $this->json(['message' => 'L\'ancien mot de passe est requis pour changer le mot de passe'], Response::HTTP_BAD_REQUEST);
            }
            if (!$this->passwordHasher->isPasswordValid($admin, $data['oldPassword'])) {
                return $this->json(['message' => 'Ancien mot de passe incorrect'], Response::HTTP_BAD_REQUEST);
            }
            $password = $data['password'];
            $passwordErrors = [];
            if (strlen($password) < 12) {
                $passwordErrors[] = 'Le mot de passe doit contenir au moins 12 caractères.';
            }
            if (!preg_match('/[A-Z]/', $password)) {
                $passwordErrors[] = 'Le mot de passe doit contenir au moins une majuscule.';
            }
            if (!preg_match('/[a-z]/', $password)) {
                $passwordErrors[] = 'Le mot de passe doit contenir au moins une minuscule.';
            }
            if (!preg_match('/[0-9]/', $password)) {
                $passwordErrors[] = 'Le mot de passe doit contenir au moins un chiffre.';
            }
            if (!preg_match('/[\W_]/', $password)) {
                $passwordErrors[] = 'Le mot de passe doit contenir au moins un caractère spécial.';
            }
            if (!empty($passwordErrors)) {
                return $this->json(['errors' => $passwordErrors], Response::HTTP_BAD_REQUEST);
            }
            $hashedPassword = $this->passwordHasher->hashPassword($admin, $password);
            $admin->setPassword($hashedPassword);
        }
        $this->entityManager->flush();
        $responseData = $this->serializer->serialize($admin, 'json', ['groups' => 'admin:read']);
        return new Response($responseData, Response::HTTP_OK, ['Content-Type' => 'application/json']);
    }
} 