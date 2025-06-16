<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Repository\ContactRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/contact')]
class ContactController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ContactRepository $contactRepository,
        private SerializerInterface $serializer
    ) {}

    #[Route('', name: 'app_contacts_index', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $contacts = $this->contactRepository->findAll();
        $data = $this->serializer->serialize($contacts, 'json', ['groups' => 'contact:read']);
        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    #[Route('/{id}', name: 'app_contacts_show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $contact = $this->contactRepository->find($id);
        if (!$contact) {
            return new JsonResponse(['message' => 'Contact non trouvé'], Response::HTTP_NOT_FOUND);
        }

        $data = $this->serializer->serialize($contact, 'json', ['groups' => 'contact:read']);
        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    #[Route('', name: 'app_contacts_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            
            if (!isset($data['name']) || !isset($data['mail']) || !isset($data['subject']) || !isset($data['description'])) {
                return new JsonResponse([
                    'message' => 'Données manquantes. Le nom, l\'email, le sujet et la description sont requis.'
                ], Response::HTTP_BAD_REQUEST);
            }

            $contact = new Contact();
            $contact->setName($data['name']);
            $contact->setMail($data['mail']);
            $contact->setSubject($data['subject']);
            $contact->setDescription($data['description']);

            $this->entityManager->persist($contact);
            $this->entityManager->flush();

            $responseData = $this->serializer->serialize($contact, 'json', ['groups' => 'contact:read']);
            return new JsonResponse($responseData, Response::HTTP_CREATED, [], true);

        } catch (\Exception $e) {
            return new JsonResponse([
                'message' => 'Erreur lors de la création du contact',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/{id}', name: 'app_contacts_delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        try {
            $contact = $this->contactRepository->find($id);
            if (!$contact) {
                return new JsonResponse(['message' => 'Contact non trouvé'], Response::HTTP_NOT_FOUND);
            }

            $this->entityManager->remove($contact);
            $this->entityManager->flush();

            return new JsonResponse(['message' => 'Contact supprimé avec succès'], Response::HTTP_OK);

        } catch (\Exception $e) {
            return new JsonResponse([
                'message' => 'Erreur lors de la suppression du contact',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
} 