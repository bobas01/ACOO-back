<?php

namespace App\Controller;

use App\Entity\ScheduleExeption;
use App\Entity\RecurringSchedule;
use App\Repository\ScheduleExeptionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/schedule-exeptions')]
class ScheduleExeptionController extends AbstractController
{
    #[Route('', name: 'app_schedule_exeption_index', methods: ['GET'])]
    public function index(ScheduleExeptionRepository $scheduleExeptionRepository, SerializerInterface $serializer): JsonResponse
    {
        $exeptions = $scheduleExeptionRepository->findAll();
        $jsonExeptions = $serializer->serialize($exeptions, 'json', ['groups' => 'schedule_exeption:read']);
        
        return new JsonResponse($jsonExeptions, Response::HTTP_OK, [], true);
    }

    #[Route('/{id}', name: 'app_schedule_exeption_show', methods: ['GET'])]
    public function show(ScheduleExeption $scheduleExeption, SerializerInterface $serializer): JsonResponse
    {
        $jsonExeption = $serializer->serialize($scheduleExeption, 'json', ['groups' => 'schedule_exeption:read']);
        
        return new JsonResponse($jsonExeption, Response::HTTP_OK, [], true);
    }

    #[Route('', name: 'app_schedule_exeption_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager, SerializerInterface $serializer): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        if (!$data) {
            return new JsonResponse(['error' => 'Données JSON invalides'], Response::HTTP_BAD_REQUEST);
        }

        $exeption = new ScheduleExeption();
        
        // Conversion des dates au format français
        if (isset($data['exeption_date'])) {
            $exeptionDate = \DateTime::createFromFormat('d/m/Y H:i', $data['exeption_date']);
            if (!$exeptionDate) {
                return new JsonResponse(['error' => 'Format de date exeption_date invalide. Utilisez JJ/MM/AAAA HH:mm'], Response::HTTP_BAD_REQUEST);
            }
            $exeption->setExeptionDate($exeptionDate);
        }

        if (isset($data['start_time'])) {
            $startTime = \DateTime::createFromFormat('d/m/Y H:i', $data['start_time']);
            if (!$startTime) {
                return new JsonResponse(['error' => 'Format de date start_time invalide. Utilisez JJ/MM/AAAA HH:mm'], Response::HTTP_BAD_REQUEST);
            }
            $exeption->setStartTime($startTime);
        }

        if (isset($data['end_time'])) {
            $endTime = \DateTime::createFromFormat('d/m/Y H:i', $data['end_time']);
            if (!$endTime) {
                return new JsonResponse(['error' => 'Format de date end_time invalide. Utilisez JJ/MM/AAAA HH:mm'], Response::HTTP_BAD_REQUEST);
            }
            $exeption->setEndTime($endTime);
        }

        // Autres champs
        if (isset($data['location'])) $exeption->setLocation($data['location']);
        if (isset($data['is_cancelled'])) $exeption->setIsCancelled($data['is_cancelled']);
        if (isset($data['reason'])) $exeption->setReason($data['reason']);

        // Gestion de la relation avec le planning récurrent
        if (isset($data['recurring_schedule'])) {
            $recurringSchedule = $entityManager->getRepository(RecurringSchedule::class)->find($data['recurring_schedule']);
            if ($recurringSchedule) {
                $exeption->setRecurringSchedule($recurringSchedule);
            } else {
                return new JsonResponse(['error' => 'Planning récurrent non trouvé'], Response::HTTP_BAD_REQUEST);
            }
        } else {
            return new JsonResponse(['error' => 'Le planning récurrent est requis'], Response::HTTP_BAD_REQUEST);
        }
        
        $entityManager->persist($exeption);
        $entityManager->flush();

        $jsonExeption = $serializer->serialize($exeption, 'json', ['groups' => 'schedule_exeption:read']);
        
        return new JsonResponse($jsonExeption, Response::HTTP_CREATED, [], true);
    }

    #[Route('/{id}', name: 'app_schedule_exeption_update', methods: ['POST'])]
    public function update(Request $request, ScheduleExeption $scheduleExeption, EntityManagerInterface $entityManager, SerializerInterface $serializer): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        if (!$data) {
            return new JsonResponse(['error' => 'Données JSON invalides'], Response::HTTP_BAD_REQUEST);
        }

        // Conversion des dates au format français
        if (isset($data['exeption_date'])) {
            $exeptionDate = \DateTime::createFromFormat('d/m/Y H:i', $data['exeption_date']);
            if (!$exeptionDate) {
                return new JsonResponse(['error' => 'Format de date exeption_date invalide. Utilisez JJ/MM/AAAA HH:mm'], Response::HTTP_BAD_REQUEST);
            }
            $scheduleExeption->setExeptionDate($exeptionDate);
        }

        if (isset($data['start_time'])) {
            $startTime = \DateTime::createFromFormat('d/m/Y H:i', $data['start_time']);
            if (!$startTime) {
                return new JsonResponse(['error' => 'Format de date start_time invalide. Utilisez JJ/MM/AAAA HH:mm'], Response::HTTP_BAD_REQUEST);
            }
            $scheduleExeption->setStartTime($startTime);
        }

        if (isset($data['end_time'])) {
            $endTime = \DateTime::createFromFormat('d/m/Y H:i', $data['end_time']);
            if (!$endTime) {
                return new JsonResponse(['error' => 'Format de date end_time invalide. Utilisez JJ/MM/AAAA HH:mm'], Response::HTTP_BAD_REQUEST);
            }
            $scheduleExeption->setEndTime($endTime);
        }

        // Autres champs
        if (isset($data['location'])) $scheduleExeption->setLocation($data['location']);
        if (isset($data['is_cancelled'])) $scheduleExeption->setIsCancelled($data['is_cancelled']);
        if (isset($data['reason'])) $scheduleExeption->setReason($data['reason']);

        // Gestion de la relation avec le planning récurrent
        if (isset($data['recurring_schedule'])) {
            $recurringSchedule = $entityManager->getRepository(RecurringSchedule::class)->find($data['recurring_schedule']);
            if ($recurringSchedule) {
                $scheduleExeption->setRecurringSchedule($recurringSchedule);
            } else {
                return new JsonResponse(['error' => 'Planning récurrent non trouvé'], Response::HTTP_BAD_REQUEST);
            }
        }
        
        $entityManager->flush();

        $jsonExeption = $serializer->serialize($scheduleExeption, 'json', ['groups' => 'schedule_exeption:read']);
        
        return new JsonResponse($jsonExeption, Response::HTTP_OK, [], true);
    }

    #[Route('/{id}', name: 'app_schedule_exeption_delete', methods: ['DELETE'])]
    public function delete(ScheduleExeption $scheduleExeption, EntityManagerInterface $entityManager): JsonResponse
    {
        $entityManager->remove($scheduleExeption);
        $entityManager->flush();
        
        return new JsonResponse([
            'message' => 'L\'exception de planning a été supprimée avec succès',
            'id' => $scheduleExeption->getId()
        ], Response::HTTP_OK);
    }
} 