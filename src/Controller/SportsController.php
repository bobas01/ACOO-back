<?php

namespace App\Controller;

use App\Entity\Sports;
use App\Entity\Images;
use App\Service\ImageUploadService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SportsController extends AbstractController
{
    #[Route('/sports', name: 'create_sport', methods: ['POST'])]
    public function createSport(
        Request $request,
        EntityManagerInterface $entityManager,
        ImageUploadService $imageUploadService
    ): Response {
        try {
            $data = json_decode($request->getContent(), true);
            
            if (!$data) {
                return $this->json([
                    'error' => 'Invalid JSON data'
                ], Response::HTTP_BAD_REQUEST);
            }

            $name = $data['name'] ?? null;
            $description = $data['description'] ?? null;
            $contact = $data['contact'] ?? null;
            $images = $data['images'] ?? [];

            if (!$name || !$description) {
                return $this->json([
                    'error' => 'Name and description are required'
                ], Response::HTTP_BAD_REQUEST);
            }

            $sport = new Sports();
            $sport->setName($name);
            $sport->setDescription($description);
            if ($contact) {
                $sport->setContact($contact);
            }

            $entityManager->persist($sport);

            $imageUrls = [];
            foreach ($images as $base64Image) {
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

                    $imagePath = $imageUploadService->upload($imageFile, 'sports');

                    $image = new Images();
                    $image->setImage($imagePath);
                    $image->setSports($sport);

                    $entityManager->persist($image);

                    $imageUrls[] = $request->getSchemeAndHttpHost() . '/uploads/images/' . $imagePath;
                }
            }

            $entityManager->flush();

            return $this->json([
                'message' => 'Sport created successfully',
                'sport' => [
                    'id' => $sport->getId(),
                    'name' => $sport->getName(),
                    'description' => $sport->getDescription(),
                    'contact' => $sport->getContact(),
                    'images' => $imageUrls
                ]
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/sports/{id}', name: 'update_sport', methods: ['POST'])]
    public function updateSport(
        int $id,
        Request $request,
        EntityManagerInterface $entityManager,
        ImageUploadService $imageUploadService
    ): Response {
        try {
            $sport = $entityManager->getRepository(Sports::class)->find($id);

            if (!$sport) {
                return $this->json([
                    'error' => 'Sport not found'
                ], Response::HTTP_NOT_FOUND);
            }

            $data = json_decode($request->getContent(), true);
            
            if (!$data) {
                return $this->json([
                    'error' => 'Invalid JSON data'
                ], Response::HTTP_BAD_REQUEST);
            }

            $name = $data['name'] ?? null;
            $description = $data['description'] ?? null;
            $contact = $data['contact'] ?? null;
            $images = $data['images'] ?? [];

            if ($name) {
                $sport->setName($name);
            }
            if ($description) {
                $sport->setDescription($description);
            }
            if ($contact !== null) {
                $sport->setContact($contact);
            }

            // Supprimer les anciennes images
            $oldImages = $sport->getImages();
            foreach ($oldImages as $oldImage) {
                $oldPath = $this->getParameter('images_directory') . '/' . $oldImage->getImage();
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
                $entityManager->remove($oldImage);
            }

            // Traiter les nouvelles images
            $imageUrls = [];
            foreach ($images as $base64Image) {
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

                    $imagePath = $imageUploadService->upload($imageFile, 'sports');

                    $image = new Images();
                    $image->setImage($imagePath);
                    $image->setSports($sport);

                    $entityManager->persist($image);

                    $imageUrls[] = $request->getSchemeAndHttpHost() . '/uploads/images/' . $imagePath;
                }
            }

            $entityManager->flush();

            return $this->json([
                'message' => 'Sport updated successfully',
                'sport' => [
                    'id' => $sport->getId(),
                    'name' => $sport->getName(),
                    'description' => $sport->getDescription(),
                    'contact' => $sport->getContact(),
                    'images' => $imageUrls
                ]
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/sports/{id}', name: 'delete_sport', methods: ['DELETE'])]
    public function deleteSport(
        int $id,
        EntityManagerInterface $entityManager
    ): Response {
        try {
            $sport = $entityManager->getRepository(Sports::class)->find($id);

            if (!$sport) {
                return $this->json([
                    'error' => 'Sport not found'
                ], Response::HTTP_NOT_FOUND);
            }

            // Supprimer les images associées
            foreach ($sport->getImages() as $image) {
                $imagePath = $this->getParameter('images_directory') . '/' . $image->getImage();
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
                $entityManager->remove($image);
            }

            $entityManager->remove($sport);
            $entityManager->flush();

            return $this->json([
                'message' => 'Sport deleted successfully'
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/sports/{id}', name: 'get_sport', methods: ['GET'])]
    public function getSport(
        int $id,
        EntityManagerInterface $entityManager,
        Request $request
    ): Response {
        try {
            $sport = $entityManager->getRepository(Sports::class)->find($id);

            if (!$sport) {
                return $this->json([
                    'error' => 'Sport not found'
                ], Response::HTTP_NOT_FOUND);
            }

            $imageUrls = [];
            foreach ($sport->getImages() as $image) {
                $imageUrls[] = $request->getSchemeAndHttpHost() . '/uploads/images/' . $image->getImage();
            }

            return $this->json([
                'id' => $sport->getId(),
                'name' => $sport->getName(),
                'description' => $sport->getDescription(),
                'contact' => $sport->getContact(),
                'images' => $imageUrls
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/sports', name: 'get_all_sports', methods: ['GET'])]
    public function getAllSports(
        EntityManagerInterface $entityManager,
        Request $request
    ): Response {
        try {
            $sports = $entityManager->getRepository(Sports::class)->findAll();
            $sportsData = [];

            foreach ($sports as $sport) {
                $imageUrls = [];
                foreach ($sport->getImages() as $image) {
                    $imageUrls[] = $request->getSchemeAndHttpHost() . '/uploads/images/' . $image->getImage();
                }

                $sportsData[] = [
                    'id' => $sport->getId(),
                    'name' => $sport->getName(),
                    'description' => $sport->getDescription(),
                    'contact' => $sport->getContact(),
                    'images' => $imageUrls
                ];
            }

            return $this->json($sportsData, Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
} 