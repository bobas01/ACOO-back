<?php

namespace App\Controller;

use App\Entity\Gallery;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/gallery')]
class GalleryController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('', name: 'app_gallery_index', methods: ['GET'])]
    public function index(Request $request): JsonResponse
    {
        try {
            $galleries = $this->entityManager->getRepository(Gallery::class)->findAll();
            $data = [];

            foreach ($galleries as $gallery) {
                $data[] = [
                    'id' => $gallery->getId(),
                    'theme' => $gallery->getTheme(),
                    'pictures' => $gallery->getPictures()->map(function($picture) use ($request) {
                        $imageUrl = null;
                        $image = $picture->getImage()->first();
                        if ($image) {
                            $imageUrl = $request->getSchemeAndHttpHost() . '/uploads/images/' . $image->getUrl();
                        }
                        return [
                            'id' => $picture->getId(),
                            'description' => $picture->getDescription(),
                            'image' => $imageUrl
                        ];
                    })->toArray(),
                    'created_at' => $gallery->getCreatedAt() ? $gallery->getCreatedAt()->format('d/m/Y H:i') : null,
                    'updated_at' => $gallery->getUpdatedAt() ? $gallery->getUpdatedAt()->format('d/m/Y H:i') : null
                ];
            }

            return new JsonResponse($data, Response::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse([
                'message' => 'Erreur lors de la récupération des galeries',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/{id}', name: 'app_gallery_show', methods: ['GET'])]
    public function show(int $id, Request $request): JsonResponse
    {
        try {
            $gallery = $this->entityManager->getRepository(Gallery::class)->find($id);

            if (!$gallery) {
                return new JsonResponse([
                    'message' => 'Galerie non trouvée'
                ], Response::HTTP_NOT_FOUND);
            }

            $data = [
                'id' => $gallery->getId(),
                'theme' => $gallery->getTheme(),
                'pictures' => $gallery->getPictures()->map(function($picture) use ($request) {
                    $imageUrl = null;
                    $image = $picture->getImage()->first();
                    if ($image) {
                        $imageUrl = $request->getSchemeAndHttpHost() . '/uploads/images/' . $image->getUrl();
                    }
                    return [
                        'id' => $picture->getId(),
                        'description' => $picture->getDescription(),
                        'image' => $imageUrl
                    ];
                })->toArray(),
                'created_at' => $gallery->getCreatedAt() ? $gallery->getCreatedAt()->format('d/m/Y H:i') : null,
                'updated_at' => $gallery->getUpdatedAt() ? $gallery->getUpdatedAt()->format('d/m/Y H:i') : null
            ];

            return new JsonResponse($data, Response::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse([
                'message' => 'Erreur lors de la récupération de la galerie',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('', name: 'app_gallery_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);

            if (!isset($data['theme'])) {
                return new JsonResponse([
                    'message' => 'Le thème est requis'
                ], Response::HTTP_BAD_REQUEST);
            }

            $gallery = new Gallery();
            $gallery->setTheme($data['theme']);
            $gallery->setCreatedAt(new \DateTimeImmutable());

            $this->entityManager->persist($gallery);
            $this->entityManager->flush();

            $response = [
                'id' => $gallery->getId(),
                'theme' => $gallery->getTheme(),
                'created_at' => $gallery->getCreatedAt()->format('d/m/Y H:i')
            ];

            return new JsonResponse($response, Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return new JsonResponse([
                'message' => 'Erreur lors de la création de la galerie',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/{id}', name: 'app_gallery_update', methods: ['POST'])]
    public function update(int $id, Request $request): JsonResponse
    {
        try {
            $gallery = $this->entityManager->getRepository(Gallery::class)->find($id);

            if (!$gallery) {
                return new JsonResponse([
                    'message' => 'Galerie non trouvée'
                ], Response::HTTP_NOT_FOUND);
            }

            $data = json_decode($request->getContent(), true);

            if (isset($data['theme'])) {
                $gallery->setTheme($data['theme']);
            }

            $gallery->setUpdatedAt(new \DateTimeImmutable());
            $this->entityManager->flush();

            $response = [
                'id' => $gallery->getId(),
                'theme' => $gallery->getTheme(),
                'created_at' => $gallery->getCreatedAt()->format('d/m/Y H:i'),
                'updated_at' => $gallery->getUpdatedAt()->format('d/m/Y H:i')
            ];

            return new JsonResponse($response, Response::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse([
                'message' => 'Erreur lors de la mise à jour de la galerie',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/{id}', name: 'app_gallery_delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        try {
            $gallery = $this->entityManager->getRepository(Gallery::class)->find($id);

            if (!$gallery) {
                return new JsonResponse([
                    'message' => 'Galerie non trouvée'
                ], Response::HTTP_NOT_FOUND);
            }

            $this->entityManager->remove($gallery);
            $this->entityManager->flush();

            return new JsonResponse([
                'message' => 'Galerie supprimée avec succès'
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse([
                'message' => 'Erreur lors de la suppression de la galerie',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
} 