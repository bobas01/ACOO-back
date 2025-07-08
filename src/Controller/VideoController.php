<?php

namespace App\Controller;

use App\Entity\Video;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/video')]
class VideoController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('', name: 'app_video_get', methods: ['GET'])]
    public function getVideo(): JsonResponse
    {
        $video = $this->entityManager->getRepository(Video::class)->findOneBy([]);
        if (!$video) {
            return new JsonResponse(['videoUrl' => null], Response::HTTP_OK);
        }
        return new JsonResponse(['videoUrl' => $video->getVideoUrl()], Response::HTTP_OK);
    }

    #[Route('', name: 'app_video_update', methods: ['POST'])]
    public function updateVideo(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (!isset($data['videoUrl']) || empty($data['videoUrl'])) {
            return new JsonResponse(['message' => 'Le champ videoUrl est requis'], Response::HTTP_BAD_REQUEST);
        }
        $video = $this->entityManager->getRepository(Video::class)->findOneBy([]);
        if (!$video) {
            $video = new Video();
        }
        $video->setVideoUrl($data['videoUrl']);
        $this->entityManager->persist($video);
        $this->entityManager->flush();
        return new JsonResponse(['videoUrl' => $video->getVideoUrl()], Response::HTTP_OK);
    }

    #[Route('', name: 'app_video_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (!isset($data['videoUrl']) || empty($data['videoUrl'])) {
            return new JsonResponse(['message' => 'Le champ videoUrl est requis'], Response::HTTP_BAD_REQUEST);
        }
        $video = $this->entityManager->getRepository(Video::class)->findOneBy([]);
        if (!$video) {
            $video = new Video();
        }
        $video->setVideoUrl($data['videoUrl']);
        $this->entityManager->persist($video);
        $this->entityManager->flush();
        return new JsonResponse(['videoUrl' => $video->getVideoUrl()], Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'app_video_update_item', methods: ['POST'])]
    public function update(Request $request, $id): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (!isset($data['videoUrl']) || empty($data['videoUrl'])) {
            return new JsonResponse(['message' => 'Le champ videoUrl est requis'], Response::HTTP_BAD_REQUEST);
        }
        $video = $this->entityManager->getRepository(Video::class)->find($id);
        if (!$video) {
            return new JsonResponse(['message' => 'Vidéo non trouvée'], Response::HTTP_NOT_FOUND);
        }
        $video->setVideoUrl($data['videoUrl']);
        $this->entityManager->flush();
        return new JsonResponse(['videoUrl' => $video->getVideoUrl()], Response::HTTP_OK);
    }

    public function getAll(): JsonResponse
    {
        $videos = $this->entityManager->getRepository(\App\Entity\Video::class)->findAll();
        $data = [];
        foreach ($videos as $video) {
            $data[] = [
                'id' => $video->getId(),
                'videoUrl' => $video->getVideoUrl()
            ];
        }
        return new JsonResponse($data, Response::HTTP_OK);
    }

    public function getOne($id): JsonResponse
    {
        $video = $this->entityManager->getRepository(\App\Entity\Video::class)->find($id);
        if (!$video) {
            return new JsonResponse(['message' => 'Vidéo non trouvée'], Response::HTTP_NOT_FOUND);
        }
        return new JsonResponse([
            'id' => $video->getId(),
            'videoUrl' => $video->getVideoUrl()
        ], Response::HTTP_OK);
    }

    public function delete($id): JsonResponse
    {
        $video = $this->entityManager->getRepository(\App\Entity\Video::class)->find($id);
        if (!$video) {
            return new JsonResponse(['message' => 'Vidéo non trouvée'], Response::HTTP_NOT_FOUND);
        }
        $this->entityManager->remove($video);
        $this->entityManager->flush();
        return new JsonResponse(['message' => 'Vidéo supprimée avec succès'], Response::HTTP_OK);
    }
} 