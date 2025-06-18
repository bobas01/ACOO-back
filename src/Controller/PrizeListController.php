<?php

namespace App\Controller;

use App\Entity\PrizeList;
use App\Entity\Images;
use App\Service\ImageUploadService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/prize-list')]
class PrizeListController extends AbstractController
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

    #[Route('', name: 'app_prize_list_index', methods: ['GET'])]
    public function index(Request $request): JsonResponse
    {
        try {
            $prizeLists = $this->entityManager->getRepository(PrizeList::class)->findAll();
            $data = [];

            foreach ($prizeLists as $prizeList) {
                $imageUrl = null;
                $image = $prizeList->getImage()->first();
                if ($image) {
                    $imageUrl = $request->getSchemeAndHttpHost() . '/uploads/images/' . $image->getImage();
                }

                $data[] = [
                    'id' => $prizeList->getId(),
                    'athleteName' => $prizeList->getAthleteName(),
                    'competition' => $prizeList->getCompetition(),
                    'category' => $prizeList->getCategory(),
                    'sport' => $prizeList->getSport(),
                    'gender' => $prizeList->getGender(),
                    'result' => $prizeList->getResult(),
                    'year' => $prizeList->getYear(),
                    'images' => $imageUrl ? [$imageUrl] : [],
                    'created_at' => $prizeList->getCreatedAt() ? $prizeList->getCreatedAt()->format('d/m/Y H:i') : null,
                    'updated_at' => $prizeList->getUpdatedAt() ? $prizeList->getUpdatedAt()->format('d/m/Y H:i') : null
                ];
            }

            return new JsonResponse($data, Response::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse([
                'message' => 'Erreur lors de la récupération des listes de prix',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/{id}', name: 'app_prize_list_show', methods: ['GET'])]
    public function show(int $id, Request $request): JsonResponse
    {
        try {
            $prizeList = $this->entityManager->getRepository(PrizeList::class)->find($id);

            if (!$prizeList) {
                return new JsonResponse([
                    'message' => 'Liste de prix non trouvée'
                ], Response::HTTP_NOT_FOUND);
            }

            $imageUrl = null;
            $image = $prizeList->getImage()->first();
            if ($image) {
                $imageUrl = $request->getSchemeAndHttpHost() . '/uploads/images/' . $image->getImage();
            }

            $data = [
                'id' => $prizeList->getId(),
                'athleteName' => $prizeList->getAthleteName(),
                'competition' => $prizeList->getCompetition(),
                'category' => $prizeList->getCategory(),
                'sport' => $prizeList->getSport(),
                'gender' => $prizeList->getGender(),
                'result' => $prizeList->getResult(),
                'year' => $prizeList->getYear(),
                'images' => $imageUrl ? [$imageUrl] : [],
                'created_at' => $prizeList->getCreatedAt() ? $prizeList->getCreatedAt()->format('d/m/Y H:i') : null,
                'updated_at' => $prizeList->getUpdatedAt() ? $prizeList->getUpdatedAt()->format('d/m/Y H:i') : null
            ];

            return new JsonResponse($data, Response::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse([
                'message' => 'Erreur lors de la récupération de la liste de prix',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('', name: 'app_prize_list_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);

            if (!isset($data['athleteName']) || !isset($data['competition']) || !isset($data['category']) || 
                !isset($data['sport']) || !isset($data['gender']) || !isset($data['result']) || !isset($data['year'])) {
                return new JsonResponse([
                    'message' => 'Données manquantes. Tous les champs sont requis sauf l\'image.'
                ], Response::HTTP_BAD_REQUEST);
            }

            $prizeList = new PrizeList();
            $prizeList->setAthleteName($data['athleteName']);
            $prizeList->setCompetition($data['competition']);
            $prizeList->setCategory($data['category']);
            $prizeList->setSport($data['sport']);
            $prizeList->setGender($data['gender']);
            $prizeList->setResult($data['result']);
            $prizeList->setYear($data['year']);
            $prizeList->setCreatedAt(new \DateTimeImmutable());

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

                    $imagePath = $this->imageUploadService->upload($imageFile, 'prize_list');
                    $imageUrl = $request->getSchemeAndHttpHost() . '/uploads/images/' . $imagePath;
                    
                    $image = new Images();
                    $image->setImage($imagePath);
                    $prizeList->addImage($image);
                }
            }

            $this->entityManager->persist($prizeList);
            $this->entityManager->flush();

            $response = [
                'id' => $prizeList->getId(),
                'athleteName' => $prizeList->getAthleteName(),
                'competition' => $prizeList->getCompetition(),
                'category' => $prizeList->getCategory(),
                'sport' => $prizeList->getSport(),
                'gender' => $prizeList->getGender(),
                'result' => $prizeList->getResult(),
                'year' => $prizeList->getYear(),
                'images' => $imageUrl ? [$imageUrl] : [],
                'created_at' => $prizeList->getCreatedAt()->format('d/m/Y H:i')
            ];

            return new JsonResponse($response, Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return new JsonResponse([
                'message' => 'Erreur lors de la création de la liste de prix',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/{id}', name: 'app_prize_list_update', methods: ['POST'])]
    public function update(int $id, Request $request): JsonResponse
    {
        try {
            $prizeList = $this->entityManager->getRepository(PrizeList::class)->find($id);

            if (!$prizeList) {
                return new JsonResponse([
                    'message' => 'Liste de prix non trouvée'
                ], Response::HTTP_NOT_FOUND);
            }

            $data = json_decode($request->getContent(), true);

            if (isset($data['athleteName'])) {
                $prizeList->setAthleteName($data['athleteName']);
            }
            if (isset($data['competition'])) {
                $prizeList->setCompetition($data['competition']);
            }
            if (isset($data['category'])) {
                $prizeList->setCategory($data['category']);
            }
            if (isset($data['sport'])) {
                $prizeList->setSport($data['sport']);
            }
            if (isset($data['gender'])) {
                $prizeList->setGender($data['gender']);
            }
            if (isset($data['result'])) {
                $prizeList->setResult($data['result']);
            }
            if (isset($data['year'])) {
                $prizeList->setYear($data['year']);
            }

            $imageUrl = null;
            if (isset($data['images']) && is_array($data['images']) && !empty($data['images'])) {
                foreach ($prizeList->getImage() as $oldImage) {
                    if ($oldImage->getImage()) {
                        $oldImagePath = $this->getParameter('images_directory') . '/' . $oldImage->getImage();
                        if (file_exists($oldImagePath) && is_file($oldImagePath)) {
                            unlink($oldImagePath);
                        }
                    }
                    $prizeList->removeImage($oldImage);
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

                    $imagePath = $this->imageUploadService->upload($imageFile, 'prize_list');
                    $imageUrl = $request->getSchemeAndHttpHost() . '/uploads/images/' . $imagePath;
                    
                    $image = new Images();
                    $image->setImage($imagePath);
                    $prizeList->addImage($image);
                }
            }

            $prizeList->setUpdatedAt(new \DateTimeImmutable());
            $this->entityManager->flush();

            $response = [
                'id' => $prizeList->getId(),
                'athleteName' => $prizeList->getAthleteName(),
                'competition' => $prizeList->getCompetition(),
                'category' => $prizeList->getCategory(),
                'sport' => $prizeList->getSport(),
                'gender' => $prizeList->getGender(),
                'result' => $prizeList->getResult(),
                'year' => $prizeList->getYear(),
                'images' => $imageUrl ? [$imageUrl] : ($prizeList->getImage()->first() ? [$request->getSchemeAndHttpHost() . '/uploads/images/' . $prizeList->getImage()->first()->getImage()] : []),
                'created_at' => $prizeList->getCreatedAt()->format('d/m/Y H:i'),
                'updated_at' => $prizeList->getUpdatedAt()->format('d/m/Y H:i')
            ];

            return new JsonResponse($response, Response::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse([
                'message' => 'Erreur lors de la mise à jour de la liste de prix',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/{id}', name: 'app_prize_list_delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        try {
            $prizeList = $this->entityManager->getRepository(PrizeList::class)->find($id);

            if (!$prizeList) {
                return new JsonResponse([
                    'message' => 'Liste de prix non trouvée'
                ], Response::HTTP_NOT_FOUND);
            }

            foreach ($prizeList->getImage() as $image) {
                $imagePath = $this->getParameter('images_directory') . '/' . $image->getImage();
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
                $prizeList->removeImage($image);
            }

            $this->entityManager->remove($prizeList);
            $this->entityManager->flush();

            return new JsonResponse([
                'message' => 'Liste de prix supprimée avec succès'
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse([
                'message' => 'Erreur lors de la suppression de la liste de prix',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
} 