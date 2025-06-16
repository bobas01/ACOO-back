<?php

namespace App\Controller;

use App\Entity\Teams;
use App\Entity\Sports;
use App\Repository\TeamsRepository;
use App\Repository\SportsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/teams')]
class TeamsController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private TeamsRepository $teamsRepository,
        private SportsRepository $sportsRepository,
        private SerializerInterface $serializer
    ) {}

    #[Route('', name: 'app_teams_index', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $teams = $this->teamsRepository->findAll();
        $data = $this->serializer->serialize($teams, 'json', ['groups' => 'teams:read']);
        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    #[Route('/{id}', name: 'app_teams_show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $team = $this->teamsRepository->find($id);
        if (!$team) {
            return new JsonResponse(['message' => 'Équipe non trouvée'], Response::HTTP_NOT_FOUND);
        }

        $data = $this->serializer->serialize($team, 'json', ['groups' => 'teams:read']);
        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    #[Route('', name: 'app_teams_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            
            if (!isset($data['name']) || !isset($data['role']) || !isset($data['sport'])) {
                return new JsonResponse([
                    'message' => 'Données manquantes. Le nom, le rôle et le sport sont requis.'
                ], Response::HTTP_BAD_REQUEST);
            }

            $team = new Teams();
            $team->setName($data['name']);
            $team->setRole($data['role']);

            $sport = $this->sportsRepository->find($data['sport']);
            if (!$sport) {
                return new JsonResponse([
                    'message' => 'Sport non trouvé'
                ], Response::HTTP_NOT_FOUND);
            }
            $team->setSport($sport);

            $this->entityManager->persist($team);
            $this->entityManager->flush();

            $responseData = $this->serializer->serialize($team, 'json', ['groups' => 'teams:read']);
            return new JsonResponse($responseData, Response::HTTP_CREATED, [], true);

        } catch (\Exception $e) {
            return new JsonResponse([
                'message' => 'Erreur lors de la création de l\'équipe',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/{id}', name: 'app_teams_update', methods: ['POST'])]
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $team = $this->teamsRepository->find($id);
            if (!$team) {
                return new JsonResponse(['message' => 'Équipe non trouvée'], Response::HTTP_NOT_FOUND);
            }

            $data = json_decode($request->getContent(), true);
            
            if (isset($data['name'])) {
                $team->setName($data['name']);
            }
            if (isset($data['role'])) {
                $team->setRole($data['role']);
            }
            if (isset($data['sport'])) {
                $sport = $this->sportsRepository->find($data['sport']);
                if (!$sport) {
                    return new JsonResponse([
                        'message' => 'Sport non trouvé'
                    ], Response::HTTP_NOT_FOUND);
                }
                $team->setSport($sport);
            }

            $this->entityManager->flush();

            $responseData = $this->serializer->serialize($team, 'json', ['groups' => 'teams:read']);
            return new JsonResponse($responseData, Response::HTTP_OK, [], true);

        } catch (\Exception $e) {
            return new JsonResponse([
                'message' => 'Erreur lors de la mise à jour de l\'équipe',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/{id}', name: 'app_teams_delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        try {
            $team = $this->teamsRepository->find($id);
            if (!$team) {
                return new JsonResponse(['message' => 'Équipe non trouvée'], Response::HTTP_NOT_FOUND);
            }

            $this->entityManager->remove($team);
            $this->entityManager->flush();

            return new JsonResponse(['message' => 'Équipe supprimée avec succès'], Response::HTTP_OK);

        } catch (\Exception $e) {
            return new JsonResponse([
                'message' => 'Erreur lors de la suppression de l\'équipe',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
} 