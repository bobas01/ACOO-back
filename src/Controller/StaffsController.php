<?php

namespace App\Controller;

use App\Entity\Staffs;
use App\Entity\Images;
use App\Service\ImageUploadService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/staffs')]
class StaffsController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private ImageUploadService $imageUploadService;

    public function __construct(
        EntityManagerInterface $entityManager,
        ImageUploadService $imageUploadService
    ) {
        $this->entityManager = $entityManager;
        $this->imageUploadService = $imageUploadService;
    }

    #[Route('', name: 'app_staffs_index', methods: ['GET'])]
    public function index(Request $request): JsonResponse
    {
        try {
            $staffs = $this->entityManager->getRepository(Staffs::class)->findAll();
            $data = [];

            foreach ($staffs as $staff) {
                $imageUrl = null;
                if ($staff->getImage()) {
                    $imageUrl = $request->getSchemeAndHttpHost() . '/uploads/images/' . $staff->getImage()->getImage();
                }

                $teams = [];
                foreach ($staff->getTeam() as $team) {
                    $teams[] = [
                        'id' => $team->getId(),
                        'name' => $team->getName()
                    ];
                }

                $data[] = [
                    'id' => $staff->getId(),
                    'name' => $staff->getName(),
                    'role' => $staff->getRole(),
                    'phoneNumber' => $staff->getPhoneNumber(),
                    'mail' => $staff->getMail(),
                    'image' => $imageUrl,
                    'teams' => $teams
                ];
            }

            return new JsonResponse($data, Response::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse([
                'message' => 'Erreur lors de la récupération des membres du staff',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/{id}', name: 'app_staffs_show', methods: ['GET'])]
    public function show(int $id, Request $request): JsonResponse
    {
        try {
            $staff = $this->entityManager->getRepository(Staffs::class)->find($id);

            if (!$staff) {
                return new JsonResponse([
                    'message' => 'Membre du staff non trouvé'
                ], Response::HTTP_NOT_FOUND);
            }

            $imageUrl = null;
            if ($staff->getImage()) {
                $imageUrl = $request->getSchemeAndHttpHost() . '/uploads/images/' . $staff->getImage()->getImage();
            }

            $teams = [];
            foreach ($staff->getTeam() as $team) {
                $teams[] = [
                    'id' => $team->getId(),
                    'name' => $team->getName()
                ];
            }

            $data = [
                'id' => $staff->getId(),
                'name' => $staff->getName(),
                'role' => $staff->getRole(),
                'phoneNumber' => $staff->getPhoneNumber(),
                'mail' => $staff->getMail(),
                'image' => $imageUrl,
                'teams' => $teams
            ];

            return new JsonResponse($data, Response::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse([
                'message' => 'Erreur lors de la récupération du membre du staff',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('', name: 'app_staffs_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);

            if (!isset($data['name'])) {
                return new JsonResponse([
                    'message' => 'Le nom est requis'
                ], Response::HTTP_BAD_REQUEST);
            }

            $staff = new Staffs();
            $staff->setName($data['name']);
            $staff->setRole($data['role'] ?? null);
            $staff->setPhoneNumber($data['phoneNumber'] ?? null);
            $staff->setMail($data['mail'] ?? null);

            if (isset($data['teams']) && is_array($data['teams'])) {
                foreach ($data['teams'] as $teamId) {
                    $team = $this->entityManager->getRepository('App\Entity\Teams')->find($teamId);
                    if ($team) {
                        $staff->addTeam($team);
                    }
                }
            }

            if (isset($data['image']) && is_array($data['image']) && !empty($data['image'])) {
                $base64Image = $data['image'][0];
                if (strpos($base64Image, 'data:image') === 0) {
                    $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64Image));
                    $tempFile = tempnam(sys_get_temp_dir(), 'img_');
                    file_put_contents($tempFile, $imageData);
                    
                    $mimeType = mime_content_type($tempFile);
                    $extension = explode('/', $mimeType)[1];
                    
                    $imageFile = new \Symfony\Component\HttpFoundation\File\UploadedFile(
                        $tempFile,
                        'image.' . $extension,
                        $mimeType,
                        null,
                        true
                    );

                    $imagePath = $this->imageUploadService->upload($imageFile, 'staffs');
                    
                    $image = new Images();
                    $image->setImage($imagePath);
                    $staff->setImage($image);
                }
            }

            $this->entityManager->persist($staff);
            $this->entityManager->flush();

            $imageUrl = null;
            if ($staff->getImage()) {
                $imageUrl = $request->getSchemeAndHttpHost() . '/uploads/images/' . $staff->getImage()->getImage();
            }

            $teams = [];
            foreach ($staff->getTeam() as $team) {
                $teams[] = [
                    'id' => $team->getId(),
                    'name' => $team->getName()
                ];
            }

            $response = [
                'id' => $staff->getId(),
                'name' => $staff->getName(),
                'role' => $staff->getRole(),
                'phoneNumber' => $staff->getPhoneNumber(),
                'mail' => $staff->getMail(),
                'image' => $imageUrl,
                'teams' => $teams
            ];

            return new JsonResponse($response, Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return new JsonResponse([
                'message' => 'Erreur lors de la création du membre du staff',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/{id}', name: 'app_staffs_update', methods: ['POST'])]
    public function update(int $id, Request $request): JsonResponse
    {
        try {
            $staff = $this->entityManager->getRepository(Staffs::class)->find($id);

            if (!$staff) {
                return new JsonResponse([
                    'message' => 'Membre du staff non trouvé'
                ], Response::HTTP_NOT_FOUND);
            }

            $data = json_decode($request->getContent(), true);

            if (isset($data['name'])) {
                $staff->setName($data['name']);
            }
            if (isset($data['role'])) {
                $staff->setRole($data['role']);
            }
            if (isset($data['phoneNumber'])) {
                $staff->setPhoneNumber($data['phoneNumber']);
            }
            if (isset($data['mail'])) {
                $staff->setMail($data['mail']);
            }

            if (isset($data['teams']) && is_array($data['teams'])) {
                $staff->getTeam()->clear();
                foreach ($data['teams'] as $teamId) {
                    $team = $this->entityManager->getRepository('App\Entity\Teams')->find($teamId);
                    if ($team) {
                        $staff->addTeam($team);
                    }
                }
            }

            if (isset($data['image']) && is_array($data['image']) && !empty($data['image'])) {
                $oldImage = $staff->getImage();
                if ($oldImage) {
                    $oldImagePath = $this->getParameter('images_directory') . '/' . $oldImage->getImage();
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                    $this->entityManager->remove($oldImage);
                }

                $base64Image = $data['image'][0];
                if (strpos($base64Image, 'data:image') === 0) {
                    $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64Image));
                    $tempFile = tempnam(sys_get_temp_dir(), 'img_');
                    file_put_contents($tempFile, $imageData);
                    
                    $mimeType = mime_content_type($tempFile);
                    $extension = explode('/', $mimeType)[1];
                    
                    $imageFile = new \Symfony\Component\HttpFoundation\File\UploadedFile(
                        $tempFile,
                        'image.' . $extension,
                        $mimeType,
                        null,
                        true
                    );

                    $imagePath = $this->imageUploadService->upload($imageFile, 'staffs');
                    
                    $image = new Images();
                    $image->setImage($imagePath);
                    $staff->setImage($image);
                    $this->entityManager->persist($image);
                }
            }

            $this->entityManager->persist($staff);
            $this->entityManager->flush();

            $imageUrl = null;
            if ($staff->getImage()) {
                $imageUrl = $request->getSchemeAndHttpHost() . '/uploads/images/' . $staff->getImage()->getImage();
            }

            $teams = [];
            foreach ($staff->getTeam() as $team) {
                $teams[] = [
                    'id' => $team->getId(),
                    'name' => $team->getName()
                ];
            }

            $response = [
                'id' => $staff->getId(),
                'name' => $staff->getName(),
                'role' => $staff->getRole(),
                'phoneNumber' => $staff->getPhoneNumber(),
                'mail' => $staff->getMail(),
                'image' => $imageUrl,
                'teams' => $teams
            ];

            return new JsonResponse($response, Response::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse([
                'message' => 'Erreur lors de la mise à jour du membre du staff',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/{id}', name: 'app_staffs_delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        try {
            $staff = $this->entityManager->getRepository(Staffs::class)->find($id);

            if (!$staff) {
                return new JsonResponse([
                    'message' => 'Membre du staff non trouvé'
                ], Response::HTTP_NOT_FOUND);
            }

            if ($staff->getImage()) {
                $imagePath = $this->getParameter('images_directory') . '/' . $staff->getImage()->getImage();
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }

            $this->entityManager->remove($staff);
            $this->entityManager->flush();

            return new JsonResponse([
                'message' => 'Membre du staff supprimé avec succès'
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse([
                'message' => 'Erreur lors de la suppression du membre du staff',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
} 