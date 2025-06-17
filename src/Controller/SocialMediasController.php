<?php

namespace App\Controller;

use App\Entity\SocialMedias;
use App\Entity\Images;
use App\Repository\SocialMediasRepository;
use App\Service\ImageUploadService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/social-media')]
class SocialMediasController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private SocialMediasRepository $socialMediasRepository,
        private ImageUploadService $imageUploadService,
        private SerializerInterface $serializer
    ) {}

    #[Route('', name: 'app_social_media_index', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $socialMedias = $this->socialMediasRepository->findAll();
        $data = $this->serializer->serialize($socialMedias, 'json', ['groups' => 'social_media:read']);
        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    #[Route('/{id}', name: 'app_social_media_show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $socialMedia = $this->socialMediasRepository->find($id);
        if (!$socialMedia) {
            return new JsonResponse(['message' => 'Réseau social non trouvé'], Response::HTTP_NOT_FOUND);
        }

        $data = $this->serializer->serialize($socialMedia, 'json', ['groups' => 'social_media:read']);
        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    #[Route('', name: 'app_social_media_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            
            if (!isset($data['platform']) || !isset($data['url'])) {
                return new JsonResponse([
                    'message' => 'Données manquantes. La plateforme et l\'URL sont requis.'
                ], Response::HTTP_BAD_REQUEST);
            }

            $socialMedia = new SocialMedias();
            $socialMedia->setPlatform($data['platform']);
            $socialMedia->setUrl($data['url']);
            
            $iconUrl = null;
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

                    $imagePath = $this->imageUploadService->upload($imageFile, 'social_media');
                    $iconUrl = $request->getSchemeAndHttpHost() . '/uploads/images/' . $imagePath;
                    $socialMedia->setIconUrl($iconUrl);
                }
            }

            $this->entityManager->persist($socialMedia);
            $this->entityManager->flush();

            $response = [
                'id' => $socialMedia->getId(),
                'platform' => $socialMedia->getPlatform(),
                'url' => $socialMedia->getUrl(),
                'iconUrl' => $iconUrl
            ];

            return new JsonResponse($response, Response::HTTP_CREATED);

        } catch (\Exception $e) {
            return new JsonResponse([
                'message' => 'Erreur lors de la création du réseau social',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/{id}', name: 'app_social_media_update', methods: ['POST'])]
    public function update(int $id, Request $request): JsonResponse
    {
        try {
            $socialMedia = $this->entityManager->getRepository(SocialMedias::class)->find($id);
            
            if (!$socialMedia) {
                return new JsonResponse([
                    'message' => 'Réseau social non trouvé'
                ], Response::HTTP_NOT_FOUND);
            }

            $data = json_decode($request->getContent(), true);
            
            if (isset($data['platform'])) {
                $socialMedia->setPlatform($data['platform']);
            }
            
            if (isset($data['url'])) {
                $socialMedia->setUrl($data['url']);
            }
            
            $iconUrl = null;
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

                    $imagePath = $this->imageUploadService->upload($imageFile, 'social_media');
                    $iconUrl = $request->getSchemeAndHttpHost() . '/uploads/images/' . $imagePath;
                    $socialMedia->setIconUrl($iconUrl);
                }
            }

            $this->entityManager->flush();

            $response = [
                'id' => $socialMedia->getId(),
                'platform' => $socialMedia->getPlatform(),
                'url' => $socialMedia->getUrl(),
                'iconUrl' => $iconUrl ?? $socialMedia->getIconUrl()
            ];

            return new JsonResponse($response, Response::HTTP_OK);

        } catch (\Exception $e) {
            return new JsonResponse([
                'message' => 'Erreur lors de la mise à jour du réseau social',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/{id}', name: 'app_social_media_delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        try {
            $socialMedia = $this->entityManager->getRepository(SocialMedias::class)->find($id);
            
            if (!$socialMedia) {
                return new JsonResponse([
                    'message' => 'Réseau social non trouvé'
                ], Response::HTTP_NOT_FOUND);
            }

            if ($socialMedia->getIconUrl()) {
                $imagePath = $this->getParameter('images_directory') . '/' . basename($socialMedia->getIconUrl());
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }

            $this->entityManager->remove($socialMedia);
            $this->entityManager->flush();

            return new JsonResponse([
                'message' => 'Réseau social supprimé avec succès'
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            return new JsonResponse([
                'message' => 'Erreur lors de la suppression du réseau social',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
} 