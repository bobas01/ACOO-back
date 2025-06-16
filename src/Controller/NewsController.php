<?php

namespace App\Controller;

use App\Entity\News;
use App\Entity\Images;
use App\Entity\Events;
use App\Repository\NewsRepository;
use App\Service\ImageUploadService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

#[Route('/news')]
class NewsController extends AbstractController
{
    private SerializerInterface $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    #[Route('', name: 'app_news_index', methods: ['GET'])]
    public function getAllNews(NewsRepository $newsRepository, Request $request): JsonResponse
    {
        $news = $newsRepository->findAll();
        $data = [];
        
        foreach ($news as $item) {
            $imageUrl = null;
            $image = $item->getImage()->first();
            if ($image) {
                $imageUrl = $request->getSchemeAndHttpHost() . '/uploads/images/' . $image->getUrl();
            }
            
            $data[] = [
                'id' => $item->getId(),
                'title' => $item->getTitle(),
                'description' => $item->getDescription(),
                'image' => $imageUrl,
                'event' => $item->getEvent() ? '/events/' . $item->getEvent()->getId() : null,
                'created_at' => $item->getCreatedAt(),
                'updated_at' => $item->getUpdatedAt(),
                'id_admin' => $item->getIdAdmin() ? '/admin/' . $item->getIdAdmin()->getId() : null
            ];
        }
        
        return new JsonResponse($data, Response::HTTP_OK);
    }

    #[Route('/{id}', name: 'app_news_show', methods: ['GET'])]
    public function getNews(News $news, Request $request): JsonResponse
    {
        $imageUrl = null;
        $image = $news->getImage()->first();
        if ($image) {
            $imageUrl = $request->getSchemeAndHttpHost() . '/uploads/images/' . $image->getUrl();
        }
        
        $data = [
            'id' => $news->getId(),
            'title' => $news->getTitle(),
            'description' => $news->getDescription(),
            'image' => $imageUrl,
            'event' => $news->getEvent() ? '/events/' . $news->getEvent()->getId() : null,
            'created_at' => $news->getCreatedAt(),
            'updated_at' => $news->getUpdatedAt(),
            'id_admin' => $news->getIdAdmin() ? '/admin/' . $news->getIdAdmin()->getId() : null
        ];
        
        return new JsonResponse($data, Response::HTTP_OK);
    }

    #[Route('', name: 'app_news_create', methods: ['POST'])]
    public function createNews(
        Request $request,
        EntityManagerInterface $entityManager,
        ImageUploadService $imageUploadService
    ): JsonResponse {
        try {
            $data = json_decode($request->getContent(), true);

            if (!isset($data['title']) || !isset($data['description'])) {
                return $this->json([
                    'error' => 'Title and description are required'
                ], Response::HTTP_BAD_REQUEST);
            }

            $news = new News();
            $news->setTitle($data['title']);
            $news->setDescription($data['description']);
            $news->setCreatedAt(new \DateTimeImmutable());

            // Si des données d'événement sont fournies, créer un événement
            if (isset($data['startDatetime'])) {
                $event = new Events();
                $event->setTitle($data['title']);
                $event->setContent($data['description']);
                $event->setEventType($data['eventType'] ?? 'default');
                $event->setLocation($data['location'] ?? '');
                $event->setIsCancelled(false);

                // Conversion de la date de début au format français
                $startDate = \DateTime::createFromFormat('d/m/Y H:i', $data['startDatetime']);
                if (!$startDate) {
                    return $this->json([
                        'error' => 'Format de date de début invalide. Utilisez le format JJ/MM/AAAA HH:mm'
                    ], Response::HTTP_BAD_REQUEST);
                }
                $event->setStartDatetime($startDate);

                // Conversion de la date de fin au format français si elle existe
                if (isset($data['endDatetime'])) {
                    $endDate = \DateTime::createFromFormat('d/m/Y H:i', $data['endDatetime']);
                    if (!$endDate) {
                        return $this->json([
                            'error' => 'Format de date de fin invalide. Utilisez le format JJ/MM/AAAA HH:mm'
                        ], Response::HTTP_BAD_REQUEST);
                    }
                    $event->setEndDatetime($endDate);
                }

                if (isset($data['sport'])) {
                    $sport = $entityManager->getRepository('App\Entity\Sports')->find($data['sport']);
                    if ($sport) {
                        $event->setSport($sport);
                    }
                }

                $entityManager->persist($event);
                $entityManager->flush(); // Flush pour obtenir l'ID de l'événement
                $news->setEvent($event);
            }

            if (isset($data['id_admin'])) {
                $adminId = is_string($data['id_admin']) ? basename($data['id_admin']) : $data['id_admin'];
                $admin = $entityManager->getRepository('App\Entity\Admin')->find($adminId);
                if ($admin) {
                    $news->setIdAdmin($admin);
                }
            }

            $entityManager->persist($news);

            $imageUrl = null;
            if (isset($data['images']) && is_array($data['images']) && !empty($data['images'])) {
                try {
                    // Prendre la première image du tableau
                    $imageData = $data['images'][0];
                    
                    // Vérifier si l'image est en base64
                    if (strpos($imageData, 'data:image') === 0) {
                        // Extraire le type MIME et les données base64
                        list($type, $imageData) = explode(';', $imageData);
                        list(, $imageData) = explode(',', $imageData);
                        
                        // Décoder l'image base64
                        $imageData = base64_decode($imageData);
                        
                        // Créer un fichier temporaire
                        $tempFile = tempnam(sys_get_temp_dir(), 'news_image_');
                        file_put_contents($tempFile, $imageData);
                        
                        // Déterminer l'extension du fichier
                        $mimeType = mime_content_type($tempFile);
                        $extension = str_replace('image/', '', $mimeType);
                        
                        // Créer un UploadedFile
                        $imageFile = new \Symfony\Component\HttpFoundation\File\UploadedFile(
                            $tempFile,
                            'image.' . $extension,
                            $mimeType,
                            null,
                            true
                        );

                        $imagePath = $imageUploadService->upload($imageFile, 'news');

                        $image = new Images();
                        $image->setUrl($imagePath);
                        $image->setNews($news);

                        $entityManager->persist($image);

                        $imageUrl = $request->getSchemeAndHttpHost() . '/uploads/images/' . $imagePath;
                        
                        // Supprimer le fichier temporaire
                        unlink($tempFile);
                    }
                } catch (\Exception $e) {
                    // En cas d'erreur avec l'image, continuer sans l'image
                    error_log('Erreur lors du traitement de l\'image : ' . $e->getMessage());
                }
            }

            $entityManager->flush();

            // Préparation de la réponse avec les dates formatées manuellement
            $response = [
                'id' => $news->getId(),
                'title' => $news->getTitle(),
                'description' => $news->getDescription(),
                'image' => $imageUrl,
                'event' => $news->getEvent() ? [
                    'id' => $news->getEvent()->getId(),
                    'title' => $news->getEvent()->getTitle(),
                    'content' => $news->getEvent()->getContent(),
                    'eventType' => $news->getEvent()->getEventType(),
                    'location' => $news->getEvent()->getLocation(),
                    'startDatetime' => $news->getEvent()->getStartDatetime() ? $news->getEvent()->getStartDatetime()->format('d/m/Y H:i') : null,
                    'endDatetime' => $news->getEvent()->getEndDatetime() ? $news->getEvent()->getEndDatetime()->format('d/m/Y H:i') : null,
                    'sport' => $news->getEvent()->getSport() ? [
                        'id' => $news->getEvent()->getSport()->getId(),
                        'name' => $news->getEvent()->getSport()->getName()
                    ] : null
                ] : null,
                'created_at' => $news->getCreatedAt() ? $news->getCreatedAt()->format('d/m/Y H:i') : null,
                'id_admin' => $news->getIdAdmin() ? '/admin/' . $news->getIdAdmin()->getId() : null
            ];

            return new JsonResponse($response, Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/{id}', name: 'app_news_update', methods: ['POST'])]
    public function updateNews(
        int $id,
        Request $request,
        EntityManagerInterface $entityManager,
        ImageUploadService $imageUploadService
    ): JsonResponse {
        try {
            $news = $entityManager->getRepository(News::class)->find($id);

            if (!$news) {
                return $this->json([
                    'error' => 'News not found'
                ], Response::HTTP_NOT_FOUND);
            }

            $data = json_decode($request->getContent(), true);

            if (isset($data['title'])) {
                $news->setTitle($data['title']);
            }
            if (isset($data['description'])) {
                $news->setDescription($data['description']);
            }
            if (isset($data['event'])) {
                $eventId = is_string($data['event']) ? basename($data['event']) : $data['event'];
                $event = $entityManager->getRepository('App\Entity\Events')->find($eventId);
                if ($event) {
                    $news->setEvent($event);
                }
            }
            if (isset($data['id_admin'])) {
                $adminId = is_string($data['id_admin']) ? basename($data['id_admin']) : $data['id_admin'];
                $admin = $entityManager->getRepository('App\Entity\Admin')->find($adminId);
                if ($admin) {
                    $news->setIdAdmin($admin);
                }
            }

            $news->setUpdatedAt(new \DateTimeImmutable());

            // Gestion de la nouvelle image si elle est fournie
            $imageUrl = null;

            if (isset($data['image']) && !empty($data['image'])) {
                // Supprimer l'ancienne image si elle existe
                $oldImages = $news->getImage();
                foreach ($oldImages as $oldImage) {
                    $oldPath = $this->getParameter('images_directory') . '/' . $oldImage->getUrl();
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                    $entityManager->remove($oldImage);
                }

                // Décoder l'image base64
                $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $data['image']));
                
                // Créer un fichier temporaire
                $tempFile = tempnam(sys_get_temp_dir(), 'news_image_');
                file_put_contents($tempFile, $imageData);
                
                // Créer un UploadedFile
                $imageFile = new \Symfony\Component\HttpFoundation\File\UploadedFile(
                    $tempFile,
                    'image.jpg',
                    'image/jpeg',
                    null,
                    true
                );

                $imagePath = $imageUploadService->upload($imageFile, 'news');

                $image = new Images();
                $image->setUrl($imagePath);
                $image->setNews($news);

                $entityManager->persist($image);
                $imageUrl = $request->getSchemeAndHttpHost() . '/uploads/images/' . $imagePath;
                
                // Supprimer le fichier temporaire
                unlink($tempFile);
            } else {
                // Récupérer l'URL de l'image existante si elle existe
                $existingImage = $news->getImage()->first();
                if ($existingImage) {
                    $imageUrl = $request->getSchemeAndHttpHost() . '/uploads/images/' . $existingImage->getUrl();
                }
            }

            $entityManager->flush();

            $response = [
                'id' => $news->getId(),
                'title' => $news->getTitle(),
                'description' => $news->getDescription(),
                'image' => $imageUrl,
                'event' => $news->getEvent() ? '/events/' . $news->getEvent()->getId() : null,
                'created_at' => $news->getCreatedAt(),
                'updated_at' => $news->getUpdatedAt(),
                'id_admin' => $news->getIdAdmin() ? '/admin/' . $news->getIdAdmin()->getId() : null
            ];

            return new JsonResponse($response, Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/{id}', name: 'app_news_delete', methods: ['DELETE'])]
    public function deleteNews(int $id, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            $news = $entityManager->getRepository(News::class)->find($id);

            if (!$news) {
                return $this->json([
                    'error' => 'News not found'
                ], Response::HTTP_NOT_FOUND);
            }

            // Supprimer les images associées
            foreach ($news->getImage() as $image) {
                // Supprimer le fichier physique
                $imagePath = $this->getParameter('images_directory') . '/' . $image->getUrl();
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
                $entityManager->remove($image);
            }

            // Supprimer la news
            $entityManager->remove($news);
            $entityManager->flush();

            return $this->json([
                'message' => 'News deleted successfully'
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
} 