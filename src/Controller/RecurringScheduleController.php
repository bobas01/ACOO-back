<?php

namespace App\Controller;

use App\Entity\RecurringSchedule;
use App\Entity\Sports;
use App\Entity\Teams;
use App\Repository\RecurringScheduleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/recurring-schedules')]
class RecurringScheduleController extends AbstractController
{
    #[Route('', name: 'app_recurring_schedule_index', methods: ['GET'])]
    public function getAllRecurringSchedules(RecurringScheduleRepository $recurringScheduleRepository, SerializerInterface $serializer): JsonResponse
    {
        $schedules = $recurringScheduleRepository->findAll();
        $jsonSchedules = $serializer->serialize($schedules, 'json', ['groups' => 'recurring_schedule:read']);
        return new JsonResponse($jsonSchedules, Response::HTTP_OK, [], true);
    }

    #[Route('/{id}', name: 'app_recurring_schedule_show', methods: ['GET'])]
    public function getRecurringSchedule(RecurringSchedule $recurringSchedule, SerializerInterface $serializer): JsonResponse
    {
        $jsonSchedule = $serializer->serialize($recurringSchedule, 'json', ['groups' => 'recurring_schedule:read']);
        return new JsonResponse($jsonSchedule, Response::HTTP_OK, [], true);
    }

    #[Route('', name: 'app_recurring_schedule_create', methods: ['POST'])]
    public function createRecurringSchedule(Request $request, EntityManagerInterface $entityManager, SerializerInterface $serializer): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        if (!$data) {
            return new JsonResponse(['error' => 'Données JSON invalides'], Response::HTTP_BAD_REQUEST);
        }

        $schedule = new RecurringSchedule();
        
        if (isset($data['start_time'])) {
            $startTime = \DateTime::createFromFormat('d/m/Y H:i', $data['start_time']);
            if (!$startTime) {
                return new JsonResponse(['error' => 'Format de date start_time invalide. Utilisez JJ/MM/AAAA HH:mm'], Response::HTTP_BAD_REQUEST);
            }
            $schedule->setStartTime($startTime);
        }

        if (isset($data['end_date'])) {
            $endDate = \DateTime::createFromFormat('d/m/Y H:i', $data['end_date']);
            if (!$endDate) {
                return new JsonResponse(['error' => 'Format de date end_date invalide. Utilisez JJ/MM/AAAA HH:mm'], Response::HTTP_BAD_REQUEST);
            }
            $schedule->setEndDate($endDate);
        }

        if (isset($data['title'])) $schedule->setTitle($data['title']);
        if (isset($data['description'])) $schedule->setDescription($data['description']);
        if (isset($data['location'])) $schedule->setLocation($data['location']);
        if (isset($data['duration'])) $schedule->setDuration($data['duration']);
        if (isset($data['frequency'])) $schedule->setFrequency($data['frequency']);
        if (isset($data['day_of_week'])) $schedule->setDayOfWeek($data['day_of_week']);

        if (isset($data['sport_id'])) {
            $sport = $entityManager->getRepository(Sports::class)->find($data['sport_id']);
            if ($sport) {
                $schedule->setSport($sport);
            }
        }

        if (isset($data['team_id'])) {
            $team = $entityManager->getRepository(Teams::class)->find($data['team_id']);
            if ($team) {
                $schedule->setTeam($team);
            }
        }
        
        $entityManager->persist($schedule);
        $entityManager->flush();

        $jsonSchedule = $serializer->serialize($schedule, 'json', ['groups' => 'recurring_schedule:read']);
        
        return new JsonResponse($jsonSchedule, Response::HTTP_CREATED, [], true);
    }

    #[Route('/{id}', name: 'app_recurring_schedule_update', methods: ['POST'])]
    public function updateRecurringSchedule(Request $request, RecurringSchedule $recurringSchedule, EntityManagerInterface $entityManager, SerializerInterface $serializer): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        if (!$data) {
            return new JsonResponse(['error' => 'Données JSON invalides'], Response::HTTP_BAD_REQUEST);
        }

        if (isset($data['start_time'])) {
            $startTime = \DateTime::createFromFormat('d/m/Y H:i', $data['start_time']);
            if (!$startTime) {
                return new JsonResponse(['error' => 'Format de date start_time invalide. Utilisez JJ/MM/AAAA HH:mm'], Response::HTTP_BAD_REQUEST);
            }
            $recurringSchedule->setStartTime($startTime);
        }

        if (isset($data['end_date'])) {
            $endDate = \DateTime::createFromFormat('d/m/Y H:i', $data['end_date']);
            if (!$endDate) {
                return new JsonResponse(['error' => 'Format de date end_date invalide. Utilisez JJ/MM/AAAA HH:mm'], Response::HTTP_BAD_REQUEST);
            }
            $recurringSchedule->setEndDate($endDate);
        }

        if (isset($data['title'])) $recurringSchedule->setTitle($data['title']);
        if (isset($data['description'])) $recurringSchedule->setDescription($data['description']);
        if (isset($data['location'])) $recurringSchedule->setLocation($data['location']);
        if (isset($data['duration'])) $recurringSchedule->setDuration($data['duration']);
        if (isset($data['frequency'])) $recurringSchedule->setFrequency($data['frequency']);
        if (isset($data['day_of_week'])) $recurringSchedule->setDayOfWeek($data['day_of_week']);

        if (isset($data['sport_id'])) {
            $sport = $entityManager->getRepository(Sports::class)->find($data['sport_id']);
            if ($sport) {
                $recurringSchedule->setSport($sport);
            }
        }

        if (isset($data['team_id'])) {
            $team = $entityManager->getRepository(Teams::class)->find($data['team_id']);
            if ($team) {
                $recurringSchedule->setTeam($team);
            }
        }
        
        $entityManager->flush();

        $jsonSchedule = $serializer->serialize($recurringSchedule, 'json', ['groups' => 'recurring_schedule:read']);
        
        return new JsonResponse($jsonSchedule, Response::HTTP_OK, [], true);
    }

    #[Route('/{id}', name: 'app_recurring_schedule_delete', methods: ['DELETE'])]
    public function deleteRecurringSchedule(RecurringSchedule $recurringSchedule, EntityManagerInterface $entityManager): JsonResponse
    {
        $entityManager->remove($recurringSchedule);
        $entityManager->flush();
        
        return new JsonResponse([
            'message' => 'Le planning récurrent a été supprimé avec succès',
            'id' => $recurringSchedule->getId()
        ], Response::HTTP_OK);
    }
}