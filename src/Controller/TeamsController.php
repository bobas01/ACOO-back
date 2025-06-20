<?php

namespace App\Controller;

use App\Entity\Teams;
use App\Entity\Sports;
use App\Entity\Images;
use App\Repository\TeamsRepository;
use App\Repository\SportsRepository;
use App\Service\ImageUploadService;
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
public function index(Request $request): JsonResponse
{
    $teams = $this->teamsRepository->findAll();
    $data = [];
foreach ($teams as $team) {
    // Récupérer toutes les images de l'équipe
    $imagesUrls = [];
    foreach ($team->getImages() as $image) {
        $imagesUrls[] = $request->getSchemeAndHttpHost() . '/uploads/images/' . $image->getImage();
    }

    $data[] = [
        'id' => $team->getId(),
        'name' => $team->getName(),
        'role' => $team->getRole(),
        'sport' => $team->getSport() ? $team->getSport()->getId() : null,
        'images' => $imagesUrls,
        'events' => $team->getEvents()->map(fn($event) => $event->getId())->toArray(),
        'recurringSchedules' => $team->getRecurringSchedules()->map(fn($rs) => $rs->getId())->toArray(),
    ];
}

    return new JsonResponse($data, Response::HTTP_OK);
}
#[Route('/{id}', name: 'app_teams_show', methods: ['GET'])]
public function show(int $id, Request $request): JsonResponse
{
    $team = $this->teamsRepository->find($id);
    if (!$team) {
        return new JsonResponse(['message' => 'Équipe non trouvée'], Response::HTTP_NOT_FOUND);
    }

    $imagesUrls = [];
    foreach ($team->getImages() as $image) {
        $imagesUrls[] = $request->getSchemeAndHttpHost() . '/uploads/images/' . $image->getImage();
    }

    $data = [
        'id' => $team->getId(),
        'name' => $team->getName(),
        'role' => $team->getRole(),
        'sport' => $team->getSport() ? $team->getSport()->getId() : null,
        'images' => $imagesUrls,
        'events' => $team->getEvents()->map(fn($event) => $event->getId())->toArray(),
        'recurringSchedules' => $team->getRecurringSchedules()->map(fn($rs) => $rs->getId())->toArray(),
    ];

    return new JsonResponse($data, Response::HTTP_OK);
}

#[Route('', name: 'app_teams_create', methods: ['POST'])]
public function create(Request $request, ImageUploadService $imageUploadService): JsonResponse
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

 
if (isset($data['images']) && is_array($data['images']) && !empty($data['images'])) {
    foreach ($data['images'] as $imageData) {
        try {
            if (strpos($imageData, 'data:image') === 0) {
                // Décodage base64
                list($type, $imageDataPart) = explode(';', $imageData);
                list(, $imageDataPart) = explode(',', $imageDataPart);
                $imageDataPart = base64_decode($imageDataPart);

                // Création d'un fichier temporaire
                $tempFile = tempnam(sys_get_temp_dir(), 'team_image_');
                file_put_contents($tempFile, $imageDataPart);

                $mimeType = mime_content_type($tempFile);
                $extension = str_replace('image/', '', $mimeType);

                $imageFile = new \Symfony\Component\HttpFoundation\File\UploadedFile(
                    $tempFile,
                    'image.' . $extension,
                    $mimeType,
                    null,
                    true
                );

              
                $imagePath = $imageUploadService->upload($imageFile, 'teams');

                $image = new Images();
                $image->setImage($imagePath);
                $image->setTeams($team);

                $this->entityManager->persist($image);

                // Nettoyage du fichier temporaire
                unlink($tempFile);
            }
        } catch (\Exception $e) {
            error_log('Erreur lors du traitement de l\'image : ' . $e->getMessage());
        }
    }
}

        $this->entityManager->persist($team);
        $this->entityManager->flush();
        $this->entityManager->refresh($team);

        // Construction de la réponse customisée
        $imagesUrls = [];
        foreach ($team->getImages() as $image) {
            $imagesUrls[] = $request->getSchemeAndHttpHost() . '/uploads/images/' . $image->getImage();
        }

        $responseData = [
            'id' => $team->getId(),
            'name' => $team->getName(),
            'role' => $team->getRole(),
            'sport' => $team->getSport() ? $team->getSport()->getId() : null,
            'images' => $imagesUrls,
            'events' => $team->getEvents()->map(fn($event) => $event->getId())->toArray(),
            'recurringSchedules' => $team->getRecurringSchedules()->map(fn($rs) => $rs->getId())->toArray(),
        ];

        return new JsonResponse($responseData, Response::HTTP_CREATED);

    } catch (\Exception $e) {
        return new JsonResponse([
            'message' => 'Erreur lors de la création de l\'équipe',
            'error' => $e->getMessage()
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}

#[Route('/{id}', name: 'app_teams_update', methods: ['POST'])]
public function update(Request $request, int $id, ImageUploadService $imageUploadService): JsonResponse
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

        // Gestion des images
     
if (isset($data['images']) && is_array($data['images'])) {
    // Supprimer les anciennes images
    foreach ($team->getImages() as $oldImage) {
        $this->entityManager->remove($oldImage);
    }

    // Ajouter les nouvelles images
    foreach ($data['images'] as $imageData) {
        try {
            if (strpos($imageData, 'data:image') === 0) {
                // Décodage base64
                list($type, $imageDataPart) = explode(';', $imageData);
                list(, $imageDataPart) = explode(',', $imageDataPart);
                $imageDataPart = base64_decode($imageDataPart);

                // Création d'un fichier temporaire
                $tempFile = tempnam(sys_get_temp_dir(), 'team_image_');
                file_put_contents($tempFile, $imageDataPart);

                $mimeType = mime_content_type($tempFile);
                $extension = str_replace('image/', '', $mimeType);

                $imageFile = new \Symfony\Component\HttpFoundation\File\UploadedFile(
                    $tempFile,
                    'image.' . $extension,
                    $mimeType,
                    null,
                    true
                );

                // Upload via ton service (à injecter dans le contrôleur)
                $imagePath = $imageUploadService->upload($imageFile, 'teams');

                $image = new Images();
                $image->setImage($imagePath);
                $image->setTeams($team);

                $this->entityManager->persist($image);

                // Nettoyage du fichier temporaire
                unlink($tempFile);
            }
        } catch (\Exception $e) {
            error_log('Erreur lors du traitement de l\'image : ' . $e->getMessage());
        }
    }
}
        $this->entityManager->flush();
        $this->entityManager->refresh($team);

        // Construction de la réponse customisée
        $imagesUrls = [];
        foreach ($team->getImages() as $image) {
            $imagesUrls[] = $request->getSchemeAndHttpHost() . '/uploads/images/' . $image->getImage();
        }

        $responseData = [
            'id' => $team->getId(),
            'name' => $team->getName(),
            'role' => $team->getRole(),
            'sport' => $team->getSport() ? $team->getSport()->getId() : null,
            'images' => $imagesUrls,
            'events' => $team->getEvents()->map(fn($event) => $event->getId())->toArray(),
            'recurringSchedules' => $team->getRecurringSchedules()->map(fn($rs) => $rs->getId())->toArray(),
        ];

        return new JsonResponse($responseData, Response::HTTP_OK);

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

        // Supprimer les images associées
        foreach ($team->getImages() as $image) {
            $this->entityManager->remove($image);
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
?>