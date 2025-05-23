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
    #[Route('/api/introduction', name: 'create_introduction', methods: ['POST'])]
    public function createIntroduction(
        Request $request,
        EntityManagerInterface $entityManager,
        ImageUploadService $imageUploadService
    ): Response {
        try {
            // Récupérer les données du formulaire
            $title = $request->request->get('title');
            $description = $request->request->get('description');
            $imageFile = $request->files->get('image');

            if (!$title || !$description) {
                return $this->json([
                    'error' => 'Title and description are required'
                ], Response::HTTP_BAD_REQUEST);
            }


            $introduction = new Introduction();
            $introduction->setTitle($title);
            $introduction->setDescription($description);

            $entityManager->persist($introduction);

            $imageUrl = null;
            if ($imageFile) {
                $imagePath = $imageUploadService->upload($imageFile, 'introduction');

                $image = new Images();
                $image->setUrl($imagePath);
                $image->setIntroduction($introduction);

                $entityManager->persist($image);

                $imageUrl = $request->getSchemeAndHttpHost() . '/uploads/images/' . $imagePath;
            }

            $entityManager->flush();

            return $this->json([
                'message' => 'Introduction created successfully',
                'introduction' => [
                    'id' => $introduction->getId(),
                    'title' => $introduction->getTitle(),
                    'description' => $introduction->getDescription(),
                    'image' => $imageUrl
                ]
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/api/introduction/{id}', name: 'update_introduction', methods: ['POST'])]
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

            // Mise à jour du titre et de la description s'ils sont fournis
            $title = $request->request->get('title');
            $description = $request->request->get('description');

            if ($title) {
                $introduction->setTitle($title);
            }
            if ($description) {
                $introduction->setDescription($description);
            }

            // Gestion de la nouvelle image si elle est fournie
            $imageFile = $request->files->get('image');
            $imageUrl = null;

            if ($imageFile) {
                // Supprimer l'ancienne image si elle existe
                $oldImages = $introduction->getImage();
                foreach ($oldImages as $oldImage) {
                    $oldPath = $this->getParameter('images_directory') . '/' . $oldImage->getUrl();
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                    $entityManager->remove($oldImage);
                }

                // Upload de la nouvelle image
                $imagePath = $imageUploadService->upload($imageFile, 'introduction');

                $image = new Images();
                $image->setUrl($imagePath);
                $image->setIntroduction($introduction);

                $entityManager->persist($image);
                $imageUrl = $request->getSchemeAndHttpHost() . '/uploads/images/' . $imagePath;
            } else {
                // Récupérer l'URL de l'image existante si elle existe
                $existingImage = $introduction->getImage()->first();
                if ($existingImage) {
                    $imageUrl = $request->getSchemeAndHttpHost() . '/uploads/images/' . $existingImage->getUrl();
                }
            }

            $entityManager->flush();

            return $this->json([
                'message' => 'Introduction updated successfully',
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

    #[Route('/api/introduction/{id}', name: 'delete_introduction', methods: ['DELETE'])]
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

            // Supprimer les images associées
            foreach ($introduction->getImage() as $image) {
                // Supprimer le fichier physique
                $imagePath = $this->getParameter('images_directory') . '/' . $image->getUrl();
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
                $entityManager->remove($image);
            }

            // Supprimer l'introduction
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

    #[Route('/api/introduction/{id}', name: 'get_introduction', methods: ['GET'])]
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

            // Récupérer l'URL de l'image si elle existe
            $imageUrl = null;
            $image = $introduction->getImage()->first();
            if ($image) {
                $imageUrl = $request->getSchemeAndHttpHost() . '/uploads/images/' . $image->getUrl();
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

    #[Route('/api/introduction', name: 'get_all_introductions', methods: ['GET'])]
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
                    $imageUrl = $request->getSchemeAndHttpHost() . '/uploads/images/' . $image->getUrl();
                }

                $data[] = [
                    'id' => $introduction->getId(),
                    'title' => $introduction->getTitle(),
                    'description' => $introduction->getDescription(),
                    'image' => $imageUrl
                ];
            }

            return $this->json([
                'introductions' => $data
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
