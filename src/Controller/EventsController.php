<?php

namespace App\Controller;

use App\Entity\Events;
use App\Repository\EventsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/events')]
class EventsController extends AbstractController
{
    private SerializerInterface $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    #[Route('', name: 'app_events_index', methods: ['GET'])]
    public function index(EventsRepository $eventsRepository): JsonResponse
    {
        $events = $eventsRepository->findAll();
        $data = [];
        
        foreach ($events as $event) {
            $data[] = [
                'id' => $event->getId(),
                'title' => $event->getTitle(),
                'content' => $event->getContent(),
                'eventType' => $event->getEventType(),
                'location' => $event->getLocation(),
                'isCancelled' => $event->isCancelled(),
                'startDatetime' => $event->getStartDatetime() ? $event->getStartDatetime()->format('d/m/Y H:i') : null,
                'endDatetime' => $event->getEndDatetime() ? $event->getEndDatetime()->format('d/m/Y H:i') : null,
                'sport' => $event->getSport() ? [
                    'id' => $event->getSport()->getId(),
                    'name' => $event->getSport()->getName()
                ] : null
            ];
        }
        
        return new JsonResponse($data, Response::HTTP_OK);
    }

    #[Route('/{id}', name: 'app_events_show', methods: ['GET'])]
    public function show(Events $event): JsonResponse
    {
        $data = [
            'id' => $event->getId(),
            'title' => $event->getTitle(),
            'content' => $event->getContent(),
            'eventType' => $event->getEventType(),
            'location' => $event->getLocation(),
            'isCancelled' => $event->isCancelled(),
            'startDatetime' => $event->getStartDatetime() ? $event->getStartDatetime()->format('d/m/Y H:i') : null,
            'endDatetime' => $event->getEndDatetime() ? $event->getEndDatetime()->format('d/m/Y H:i') : null,
            'sport' => $event->getSport() ? [
                'id' => $event->getSport()->getId(),
                'name' => $event->getSport()->getName()
            ] : null
        ];
        
        return new JsonResponse($data, Response::HTTP_OK);
    }

    #[Route('', name: 'app_events_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);

            if (!isset($data['title']) || !isset($data['content']) || !isset($data['startDatetime'])) {
                return $this->json([
                    'error' => 'Title, content and startDatetime are required'
                ], Response::HTTP_BAD_REQUEST);
            }

            $event = new Events();
            $event->setTitle($data['title']);
            $event->setContent($data['content']);
            $event->setEventType($data['eventType'] ?? 'default');
            $event->setLocation($data['location'] ?? '');
            $event->setIsCancelled(false);

            $startDate = \DateTime::createFromFormat('d/m/Y H:i', $data['startDatetime']);
            if (!$startDate) {
                return $this->json([
                    'error' => 'Format de date de début invalide. Utilisez le format JJ/MM/AAAA HH:mm'
                ], Response::HTTP_BAD_REQUEST);
            }
            $event->setStartDatetime($startDate);

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
            $entityManager->flush();

            $response = [
                'id' => $event->getId(),
                'title' => $event->getTitle(),
                'content' => $event->getContent(),
                'eventType' => $event->getEventType(),
                'location' => $event->getLocation(),
                'isCancelled' => $event->isCancelled(),
                'startDatetime' => $event->getStartDatetime()->format('d/m/Y H:i'),
                'endDatetime' => $event->getEndDatetime() ? $event->getEndDatetime()->format('d/m/Y H:i') : null,
                'sport' => $event->getSport() ? [
                    'id' => $event->getSport()->getId(),
                    'name' => $event->getSport()->getName()
                ] : null
            ];

            return new JsonResponse($response, Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/{id}', name: 'app_events_update', methods: ['POST'])]
    public function update(Request $request, Events $event, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);

            if (isset($data['title'])) {
                $event->setTitle($data['title']);
            }
            if (isset($data['content'])) {
                $event->setContent($data['content']);
            }
            if (isset($data['eventType'])) {
                $event->setEventType($data['eventType']);
            }
            if (isset($data['location'])) {
                $event->setLocation($data['location']);
            }
            if (isset($data['startDatetime'])) {
                $startDate = \DateTime::createFromFormat('d/m/Y H:i', $data['startDatetime']);
                if (!$startDate) {
                    return $this->json([
                        'error' => 'Format de date de début invalide. Utilisez le format JJ/MM/AAAA HH:mm'
                    ], Response::HTTP_BAD_REQUEST);
                }
                $event->setStartDatetime($startDate);
            }
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

            $entityManager->flush();

            $response = [
                'id' => $event->getId(),
                'title' => $event->getTitle(),
                'content' => $event->getContent(),
                'eventType' => $event->getEventType(),
                'location' => $event->getLocation(),
                'isCancelled' => $event->isCancelled(),
                'startDatetime' => $event->getStartDatetime()->format('d/m/Y H:i'),
                'endDatetime' => $event->getEndDatetime() ? $event->getEndDatetime()->format('d/m/Y H:i') : null,
                'sport' => $event->getSport() ? [
                    'id' => $event->getSport()->getId(),
                    'name' => $event->getSport()->getName()
                ] : null
            ];

            return new JsonResponse($response, Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/{id}', name: 'app_events_delete', methods: ['DELETE'])]
    public function delete(Events $event, EntityManagerInterface $entityManager): JsonResponse
    {
        $entityManager->remove($event);
        $entityManager->flush();
        
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
} 