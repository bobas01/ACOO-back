<?php

namespace App\Controller;

use App\Entity\Pictures;
use App\Entity\Gallery;
use App\Entity\Images;
use App\Service\ImageUploadService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/pictures')]
class PicturesController extends AbstractController
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

    #[Route('', name: 'app_pictures_index', methods: ['GET'])]
    public function index(Request $request): JsonResponse
    {
        try {
            $pictures = $this->entityManager->getRepository(Pictures::class)->findAll();
            $data = [];

            foreach ($pictures as $picture) {
                $imageUrl = null;
                $image = $picture->getImages()->first();
                if ($image) {
                    $imageUrl = $request->getSchemeAndHttpHost() . '/uploads/images/' . $image->getImage();
                }

                $data[] = [
                    'id' => $picture->getId(),
                    'description' => $picture->getDescription(),
                    'gallery' => $picture->getIdGallery() ? [
                        'id' => $picture->getIdGallery()->getId(),
                        'theme' => $picture->getIdGallery()->getTheme()
                    ] : null,
                    'images' => $imageUrl ? [$imageUrl] : [],
                    'created_at' => $picture->getCreatedAt() ? $picture->getCreatedAt()->format('d/m/Y H:i') : null,
                    'updated_at' => $picture->getUpdatedAt() ? $picture->getUpdatedAt()->format('d/m/Y H:i') : null
                ];
            }

            return new JsonResponse($data, Response::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse([
                'message' => 'Erreur lors de la récupération des images',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/{id}', name: 'app_pictures_show', methods: ['GET'])]
    public function show(int $id, Request $request): JsonResponse
    {
        try {
            $picture = $this->entityManager->getRepository(Pictures::class)->find($id);

            if (!$picture) {
                return new JsonResponse([
                    'message' => 'Image non trouvée'
                ], Response::HTTP_NOT_FOUND);
            }

            $imageUrl = null;
            $image = $picture->getImages()->first();
            if ($image) {
                $imageUrl = $request->getSchemeAndHttpHost() . '/uploads/images/' . $image->getImage();
            }

            $data = [
                'id' => $picture->getId(),
                'description' => $picture->getDescription(),
                'gallery' => $picture->getIdGallery() ? [
                    'id' => $picture->getIdGallery()->getId(),
                    'theme' => $picture->getIdGallery()->getTheme()
                ] : null,
                'images' => $imageUrl ? [$imageUrl] : [],
                'created_at' => $picture->getCreatedAt() ? $picture->getCreatedAt()->format('d/m/Y H:i') : null,
                'updated_at' => $picture->getUpdatedAt() ? $picture->getUpdatedAt()->format('d/m/Y H:i') : null
            ];

            return new JsonResponse($data, Response::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse([
                'message' => 'Erreur lors de la récupération de l\'image',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('', name: 'app_pictures_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);

            if (!isset($data['description']) || !isset($data['gallery_id'])) {
                return new JsonResponse([
                    'message' => 'La description et l\'ID de la galerie sont requis'
                ], Response::HTTP_BAD_REQUEST);
            }

            $gallery = $this->entityManager->getRepository(Gallery::class)->find($data['gallery_id']);
            if (!$gallery) {
                return new JsonResponse([
                    'message' => 'Galerie non trouvée'
                ], Response::HTTP_NOT_FOUND);
            }

            $picture = new Pictures();
            $picture->setDescription($data['description']);
            $picture->setIdGallery($gallery);
            $picture->setCreatedAt(new \DateTimeImmutable());

            $imageUrl = null;
            if (isset($data['images']) && is_array($data['images']) && !empty($data['images'])) {
                $base64Image = $data['images'][0];
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

                    $imagePath = $this->imageUploadService->upload($imageFile, 'pictures');
                    $imageUrl = $request->getSchemeAndHttpHost() . '/uploads/images/' . $imagePath;
                    
                    $image = new Images();
                    $image->setImage($imagePath);
                    $picture->addImage($image);
                }
            }

            $this->entityManager->persist($picture);
            $this->entityManager->flush();

            $response = [
                'id' => $picture->getId(),
                'description' => $picture->getDescription(),
                'gallery' => [
                    'id' => $gallery->getId(),
                    'theme' => $gallery->getTheme()
                ],
                'images' => $imageUrl ? [$imageUrl] : [],
                'created_at' => $picture->getCreatedAt()->format('d/m/Y H:i')
            ];

            return new JsonResponse($response, Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return new JsonResponse([
                'message' => 'Erreur lors de la création de l\'image',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/{id}', name: 'app_pictures_update', methods: ['POST'])]
    public function update(int $id, Request $request): JsonResponse
    {
        try {
            $picture = $this->entityManager->getRepository(Pictures::class)->find($id);

            if (!$picture) {
                return new JsonResponse([
                    'message' => 'Image non trouvée'
                ], Response::HTTP_NOT_FOUND);
            }

            $data = json_decode($request->getContent(), true);

            if (isset($data['description'])) {
                $picture->setDescription($data['description']);
            }

            if (isset($data['gallery_id'])) {
                $gallery = $this->entityManager->getRepository(Gallery::class)->find($data['gallery_id']);
                if (!$gallery) {
                    return new JsonResponse([
                        'message' => 'Galerie non trouvée'
                    ], Response::HTTP_NOT_FOUND);
                }
                $picture->setIdGallery($gallery);
            }

            $imageUrl = null;
            if (isset($data['images']) && is_array($data['images']) && !empty($data['images'])) {
                foreach ($picture->getImages() as $oldImage) {
                    $oldImagePath = $this->getParameter('images_directory') . '/' . $oldImage->getImage();
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                    $picture->removeImage($oldImage);
                }

                $base64Image = $data['images'][0];
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

                    $imagePath = $this->imageUploadService->upload($imageFile, 'pictures');
                    $imageUrl = $request->getSchemeAndHttpHost() . '/uploads/images/' . $imagePath;
                    
                    $image = new Images();
                    $image->setImage($imagePath);
                    $picture->addImage($image);
                }
            }

            $picture->setUpdatedAt(new \DateTimeImmutable());
            $this->entityManager->flush();

            $response = [
                'id' => $picture->getId(),
                'description' => $picture->getDescription(),
                'gallery' => $picture->getIdGallery() ? [
                    'id' => $picture->getIdGallery()->getId(),
                    'theme' => $picture->getIdGallery()->getTheme()
                ] : null,
                'images' => $imageUrl ? [$imageUrl] : [],
                'created_at' => $picture->getCreatedAt()->format('d/m/Y H:i'),
                'updated_at' => $picture->getUpdatedAt()->format('d/m/Y H:i')
            ];

            return new JsonResponse($response, Response::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse([
                'message' => 'Erreur lors de la mise à jour de l\'image',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/{id}', name: 'app_pictures_delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        try {
            $picture = $this->entityManager->getRepository(Pictures::class)->find($id);

            if (!$picture) {
                return new JsonResponse([
                    'message' => 'Image non trouvée'
                ], Response::HTTP_NOT_FOUND);
            }

            foreach ($picture->getImages() as $image) {
                $imagePath = $this->getParameter('images_directory') . '/' . $image->getImage();
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
                $picture->removeImage($image);
            }

            $this->entityManager->remove($picture);
            $this->entityManager->flush();

            return new JsonResponse([
                'message' => 'Image supprimée avec succès'
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse([
                'message' => 'Erreur lors de la suppression de l\'image',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
} 