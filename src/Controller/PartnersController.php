<?php

namespace App\Controller;

use App\Entity\Partners;
use App\Entity\Images;
use App\Service\ImageUploadService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/partners')]
class PartnersController extends AbstractController
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

    #[Route('', name: 'app_partners_index', methods: ['GET'])]
    public function index(Request $request): JsonResponse
    {
        try {
            $partners = $this->entityManager->getRepository(Partners::class)->findAll();
            $data = [];

            foreach ($partners as $partner) {
                $imageUrl = null;
                $image = $partner->getImage()->first();
                if ($image) {
                    $imageUrl = $request->getSchemeAndHttpHost() . '/uploads/images/' . $image->getUrl();
                }

                $data[] = [
                    'id' => $partner->getId(),
                    'name' => $partner->getName(),
                    'description' => $partner->getDescription(),
                    'sponsor' => $partner->isSponsor(),
                    'image' => $imageUrl,
                    'created_at' => $partner->getCreatedAt() ? $partner->getCreatedAt()->format('d/m/Y H:i') : null,
                    'updated_at' => $partner->getUpdatedAt() ? $partner->getUpdatedAt()->format('d/m/Y H:i') : null
                ];
            }

            return new JsonResponse($data, Response::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse([
                'message' => 'Erreur lors de la récupération des partenaires',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/{id}', name: 'app_partners_show', methods: ['GET'])]
    public function show(int $id, Request $request): JsonResponse
    {
        try {
            $partner = $this->entityManager->getRepository(Partners::class)->find($id);

            if (!$partner) {
                return new JsonResponse([
                    'message' => 'Partenaire non trouvé'
                ], Response::HTTP_NOT_FOUND);
            }

            $imageUrl = null;
            $image = $partner->getImage()->first();
            if ($image) {
                $imageUrl = $request->getSchemeAndHttpHost() . '/uploads/images/' . $image->getUrl();
            }

            $data = [
                'id' => $partner->getId(),
                'name' => $partner->getName(),
                'description' => $partner->getDescription(),
                'sponsor' => $partner->isSponsor(),
                'image' => $imageUrl,
                'created_at' => $partner->getCreatedAt() ? $partner->getCreatedAt()->format('d/m/Y H:i') : null,
                'updated_at' => $partner->getUpdatedAt() ? $partner->getUpdatedAt()->format('d/m/Y H:i') : null
            ];

            return new JsonResponse($data, Response::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse([
                'message' => 'Erreur lors de la récupération du partenaire',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('', name: 'app_partners_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);

            if (!isset($data['name']) || !isset($data['description'])) {
                return new JsonResponse([
                    'message' => 'Données manquantes. Le nom et la description sont requis.'
                ], Response::HTTP_BAD_REQUEST);
            }

            $partner = new Partners();
            $partner->setName($data['name']);
            $partner->setDescription($data['description']);
            $partner->setSponsor($data['sponsor'] ?? false);
            $partner->setCreatedAt(new \DateTimeImmutable());

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

                    $imagePath = $this->imageUploadService->upload($imageFile, 'partners');
                    $imageUrl = $request->getSchemeAndHttpHost() . '/uploads/images/' . $imagePath;
                    
                    $image = new Images();
                    $image->setUrl($imagePath);
                    $partner->addImage($image);
                }
            }

            $this->entityManager->persist($partner);
            $this->entityManager->flush();

            $response = [
                'id' => $partner->getId(),
                'name' => $partner->getName(),
                'description' => $partner->getDescription(),
                'sponsor' => $partner->isSponsor(),
                'image' => $imageUrl,
                'created_at' => $partner->getCreatedAt()->format('d/m/Y H:i')
            ];

            return new JsonResponse($response, Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return new JsonResponse([
                'message' => 'Erreur lors de la création du partenaire',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/{id}', name: 'app_partners_update', methods: ['POST'])]
    public function update(int $id, Request $request): JsonResponse
    {
        try {
            $partner = $this->entityManager->getRepository(Partners::class)->find($id);

            if (!$partner) {
                return new JsonResponse([
                    'message' => 'Partenaire non trouvé'
                ], Response::HTTP_NOT_FOUND);
            }

            $data = json_decode($request->getContent(), true);

            if (isset($data['name'])) {
                $partner->setName($data['name']);
            }
            if (isset($data['description'])) {
                $partner->setDescription($data['description']);
            }
            if (isset($data['sponsor'])) {
                $partner->setSponsor($data['sponsor']);
            }

            $imageUrl = null;
            if (isset($data['images']) && is_array($data['images']) && !empty($data['images'])) {
                foreach ($partner->getImage() as $oldImage) {
                    $oldImagePath = $this->getParameter('images_directory') . '/' . $oldImage->getUrl();
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                    $partner->removeImage($oldImage);
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

                    $imagePath = $this->imageUploadService->upload($imageFile, 'partners');
                    $imageUrl = $request->getSchemeAndHttpHost() . '/uploads/images/' . $imagePath;
                    
                    $image = new Images();
                    $image->setUrl($imagePath);
                    $partner->addImage($image);
                }
            }

            $partner->setUpdatedAt(new \DateTimeImmutable());
            $this->entityManager->flush();

            $response = [
                'id' => $partner->getId(),
                'name' => $partner->getName(),
                'description' => $partner->getDescription(),
                'sponsor' => $partner->isSponsor(),
                'image' => $imageUrl ?? ($partner->getImage()->first() ? $request->getSchemeAndHttpHost() . '/uploads/images/' . $partner->getImage()->first()->getUrl() : null),
                'created_at' => $partner->getCreatedAt()->format('d/m/Y H:i'),
                'updated_at' => $partner->getUpdatedAt()->format('d/m/Y H:i')
            ];

            return new JsonResponse($response, Response::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse([
                'message' => 'Erreur lors de la mise à jour du partenaire',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/{id}', name: 'app_partners_delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        try {
            $partner = $this->entityManager->getRepository(Partners::class)->find($id);

            if (!$partner) {
                return new JsonResponse([
                    'message' => 'Partenaire non trouvé'
                ], Response::HTTP_NOT_FOUND);
            }

            foreach ($partner->getImage() as $image) {
                $imagePath = $this->getParameter('images_directory') . '/' . $image->getUrl();
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
                $partner->removeImage($image);
            }

            $this->entityManager->remove($partner);
            $this->entityManager->flush();

            return new JsonResponse([
                'message' => 'Partenaire supprimé avec succès'
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse([
                'message' => 'Erreur lors de la suppression du partenaire',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
} 