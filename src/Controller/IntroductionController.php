<?php

namespace App\Controller;

use App\Entity\Introduction;
use App\Entity\Images;
use App\Service\ImageUploadService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IntroductionController extends AbstractController
{
    #[Route('/introduction', name: 'create_introduction', methods: ['POST'])]
    public function createIntroduction(
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

            $title = $data['title'] ?? null;
            $description = $data['description'] ?? null;
            $images = $data['images'] ?? [];

            if (!$title || !$description) {
                return $this->json([
                    'error' => 'Title and description are required'
                ], Response::HTTP_BAD_REQUEST);
            }

            $introduction = new Introduction();
            $introduction->setTitle($title);
            $introduction->setDescription($description);

            $entityManager->persist($introduction);

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

                    $imagePath = $imageUploadService->upload($imageFile, 'introduction');

                    $image = new Images();
                    $image->setImage($imagePath);
                    $image->setIntroduction($introduction);

                    $entityManager->persist($image);

                    $imageUrls[] = $request->getSchemeAndHttpHost() . '/uploads/images/' . $imagePath;
                }
            }

            $entityManager->flush();

            return $this->json([
                'message' => 'Introduction created successfully',
                'introduction' => [
                    'id' => $introduction->getId(),
                    'title' => $introduction->getTitle(),
                    'description' => $introduction->getDescription(),
                    'image' => $imageUrls[0]
                ]
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/introduction/{id}', name: 'update_introduction', methods: ['POST'])]
    public function updateIntroduction(
        int $id,
        Request $request,
        EntityManagerInterface $entityManager,
        ImageUploadService $imageUploadService
    ): Response {
        try {
            $introduction = $entityManager->getRepository(Introduction::class)->find($id);

            if (!$introduction) {
                return $this->json([
                    'error' => 'Introduction not found'
                ], Response::HTTP_NOT_FOUND);
            }

            $data = json_decode($request->getContent(), true);
            
            if (!$data) {
                return $this->json([
                    'error' => 'Invalid JSON data'
                ], Response::HTTP_BAD_REQUEST);
            }

            $title = $data['title'] ?? null;
            $description = $data['description'] ?? null;
            $images = $data['images'] ?? [];

            if ($title) {
                $introduction->setTitle($title);
            }
            if ($description) {
                $introduction->setDescription($description);
            }

            $oldImages = $introduction->getImage();
            foreach ($oldImages as $oldImage) {
                $oldPath = $this->getParameter('images_directory') . '/' . $oldImage->getImage();
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
                $entityManager->remove($oldImage);
            }

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

                    $imagePath = $imageUploadService->upload($imageFile, 'introduction');

                    $image = new Images();
                    $image->setImage($imagePath);
                    $image->setIntroduction($introduction);

                    $entityManager->persist($image);

                    $imageUrls[] = $request->getSchemeAndHttpHost() . '/uploads/images/' . $imagePath;
                }
            }

            $entityManager->flush();

            return $this->json([
                'message' => 'Introduction updated successfully',
                'introduction' => [
                    'id' => $introduction->getId(),
                    'title' => $introduction->getTitle(),
                    'description' => $introduction->getDescription(),
                    'image' => $imageUrls[0]
                ]
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/introduction/{id}', name: 'delete_introduction', methods: ['DELETE'])]
    public function deleteIntroduction(
        int $id,
        EntityManagerInterface $entityManager
    ): Response {
        try {
            $introduction = $entityManager->getRepository(Introduction::class)->find($id);

            if (!$introduction) {
                return $this->json([
                    'error' => 'Introduction not found'
                ], Response::HTTP_NOT_FOUND);
            }

            foreach ($introduction->getImage() as $image) {
                $imagePath = $this->getParameter('images_directory') . '/' . $image->getImage();
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
                $entityManager->remove($image);
            }

            $entityManager->remove($introduction);
            $entityManager->flush();

            return $this->json([
                'message' => 'Introduction deleted successfully'
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/introduction/{id}', name: 'get_introduction', methods: ['GET'])]
    public function getIntroduction(
        int $id,
        EntityManagerInterface $entityManager,
        Request $request
    ): Response {
        try {
            $introduction = $entityManager->getRepository(Introduction::class)->find($id);

            if (!$introduction) {
                return $this->json([
                    'error' => 'Introduction not found'
                ], Response::HTTP_NOT_FOUND);
            }

            $imageUrl = null;
            $image = $introduction->getImage()->first();
            if ($image) {
                $imageUrl = $request->getSchemeAndHttpHost() . '/uploads/images/' . $image->getImage();
            }

            return $this->json([
                'introduction' => [
                    'id' => $introduction->getId(),
                    'title' => $introduction->getTitle(),
                    'description' => $introduction->getDescription(),
                    'image' => $imageUrl
                ]
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/introduction', name: 'get_all_introductions', methods: ['GET'])]
    public function getAllIntroductions(
        EntityManagerInterface $entityManager,
        Request $request
    ): Response {
        try {
            $introductions = $entityManager->getRepository(Introduction::class)->findAll();
            $data = [];

            foreach ($introductions as $introduction) {
                $imageUrl = null;
                $image = $introduction->getImage()->first();
                if ($image) {
                    $imageUrl = $request->getSchemeAndHttpHost() . '/uploads/images/' . $image->getImage();
                }

                $data[] = [
                    'id' => $introduction->getId(),
                    'title' => $introduction->getTitle(),
                    'description' => $introduction->getDescription(),
                    'image' => $imageUrl
                ];
            }

            return $this->json($data, Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
