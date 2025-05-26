<?php

namespace App\Controller;

use App\Entity\Admin;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher, ValidatorInterface $validator): Response
    {
        $data = json_decode($request->getContent(), true);

        if (!$data || !isset($data['username'], $data['email'], $data['password'])) {
            return $this->json(['error' => 'Données invalides. Should be username, email and password.'], Response::HTTP_BAD_REQUEST);
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

        $existingAdminByUsername = $entityManager->getRepository(Admin::class)->findOneBy(['username' => $data['username']]);
        $existingAdminByEmail = $entityManager->getRepository(Admin::class)->findOneBy(['email' => $data['email']]);

        if ($existingAdminByUsername) {
            return $this->json(['error' => 'Le nom d\'utilisateur est déjà pris.'], Response::HTTP_BAD_REQUEST);
        }

        if ($existingAdminByEmail) {
            return $this->json(['error' => 'L\'email est déjà utilisé.'], Response::HTTP_BAD_REQUEST);
        }

        $admin = new Admin();
        $admin->setUsername($data['username']);
        $admin->setEmail($data['email']);
        
        $hashedPassword = $passwordHasher->hashPassword($admin, $password);
        $admin->setPassword($hashedPassword);

        $errors = $validator->validate($admin);
        if (count($errors) > 0) {
            return $this->json(['errors' => (string) $errors], Response::HTTP_BAD_REQUEST);
        }

        $entityManager->persist($admin);
        $entityManager->flush();

        return new Response('Utilisateur enregistré avec succès', Response::HTTP_CREATED);
    }
} 