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
    public function getAllRecurringSchedules(RecurringScheduleRepository $recurringScheduleRepository): JsonResponse
    {
        $schedules = $recurringScheduleRepository->findAll();
        $data = [];
        
        foreach ($schedules as $schedule) {
            $data[] = [
                'id' => $schedule->getId(),
                'title' => $schedule->getTitle(),
                'description' => $schedule->getDescription(),
                'location' => $schedule->getLocation(),
                'start_time' => $schedule->getStartTime() ? $schedule->getStartTime()->format('d/m/Y H:i') : null,
                'duration' => $schedule->getDuration(),
                'frequency' => $schedule->getFrequency(),
                'end_date' => $schedule->getEndDate() ? $schedule->getEndDate()->format('d/m/Y H:i') : null,
                'day_of_week' => $schedule->getDayOfWeek(),
                'sport' => $schedule->getSport() ? [
                    'id' => $schedule->getSport()->getId(),
                    'name' => $schedule->getSport()->getName()
                ] : null,
                'team' => $schedule->getTeam() ? [
                    'id' => $schedule->getTeam()->getId(),
                    'name' => $schedule->getTeam()->getName()
                ] : null,
                'created_at' => $schedule->getCreatedAt() ? $schedule->getCreatedAt()->format('d/m/Y H:i') : null,
                'updated_at' => $schedule->getUpdatedAt() ? $schedule->getUpdatedAt()->format('d/m/Y H:i') : null
            ];
        }
        
        return new JsonResponse($data, Response::HTTP_OK);
    }

    #[Route('/{id}', name: 'app_recurring_schedule_show', methods: ['GET'])]
    public function getRecurringSchedule(RecurringSchedule $recurringSchedule): JsonResponse
    {
        $data = [
            'id' => $recurringSchedule->getId(),
            'title' => $recurringSchedule->getTitle(),
            'description' => $recurringSchedule->getDescription(),
            'location' => $recurringSchedule->getLocation(),
            'start_time' => $recurringSchedule->getStartTime() ? $recurringSchedule->getStartTime()->format('d/m/Y H:i') : null,
            'duration' => $recurringSchedule->getDuration(),
            'frequency' => $recurringSchedule->getFrequency(),
            'end_date' => $recurringSchedule->getEndDate() ? $recurringSchedule->getEndDate()->format('d/m/Y H:i') : null,
            'day_of_week' => $recurringSchedule->getDayOfWeek(),
            'sport' => $recurringSchedule->getSport() ? [
                'id' => $recurringSchedule->getSport()->getId(),
                'name' => $recurringSchedule->getSport()->getName()
            ] : null,
            'team' => $recurringSchedule->getTeam() ? [
                'id' => $recurringSchedule->getTeam()->getId(),
                'name' => $recurringSchedule->getTeam()->getName()
            ] : null,
            'created_at' => $recurringSchedule->getCreatedAt() ? $recurringSchedule->getCreatedAt()->format('d/m/Y H:i') : null,
            'updated_at' => $recurringSchedule->getUpdatedAt() ? $recurringSchedule->getUpdatedAt()->format('d/m/Y H:i') : null
        ];
        
        return new JsonResponse($data, Response::HTTP_OK);
    }

    #[Route('', name: 'app_recurring_schedule_create', methods: ['POST'])]
    public function createRecurringSchedule(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
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

            $schedule->setCreatedAt(new \DateTimeImmutable());
            
            $entityManager->persist($schedule);
            $entityManager->flush();

            $response = [
                'id' => $schedule->getId(),
                'title' => $schedule->getTitle(),
                'description' => $schedule->getDescription(),
                'location' => $schedule->getLocation(),
                'start_time' => $schedule->getStartTime() ? $schedule->getStartTime()->format('d/m/Y H:i') : null,
                'duration' => $schedule->getDuration(),
                'frequency' => $schedule->getFrequency(),
                'end_date' => $schedule->getEndDate() ? $schedule->getEndDate()->format('d/m/Y H:i') : null,
                'day_of_week' => $schedule->getDayOfWeek(),
                'sport' => $schedule->getSport() ? [
                    'id' => $schedule->getSport()->getId(),
                    'name' => $schedule->getSport()->getName()
                ] : null,
                'team' => $schedule->getTeam() ? [
                    'id' => $schedule->getTeam()->getId(),
                    'name' => $schedule->getTeam()->getName()
                ] : null,
                'created_at' => $schedule->getCreatedAt()->format('d/m/Y H:i')
            ];
            
            return new JsonResponse($response, Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return new JsonResponse([
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/{id}', name: 'app_recurring_schedule_update', methods: ['POST'])]
    public function updateRecurringSchedule(Request $request, RecurringSchedule $recurringSchedule, EntityManagerInterface $entityManager): JsonResponse
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

        $response = [
            'id' => $recurringSchedule->getId(),
            'title' => $recurringSchedule->getTitle(),
            'description' => $recurringSchedule->getDescription(),
            'location' => $recurringSchedule->getLocation(),
            'start_time' => $recurringSchedule->getStartTime() ? $recurringSchedule->getStartTime()->format('d/m/Y H:i') : null,
            'duration' => $recurringSchedule->getDuration(),
            'frequency' => $recurringSchedule->getFrequency(),
            'end_date' => $recurringSchedule->getEndDate() ? $recurringSchedule->getEndDate()->format('d/m/Y H:i') : null,
            'day_of_week' => $recurringSchedule->getDayOfWeek(),
            'sport' => $recurringSchedule->getSport() ? [
                'id' => $recurringSchedule->getSport()->getId(),
                'name' => $recurringSchedule->getSport()->getName()
            ] : null,
            'team' => $recurringSchedule->getTeam() ? [
                'id' => $recurringSchedule->getTeam()->getId(),
                'name' => $recurringSchedule->getTeam()->getName()
            ] : null,
            'created_at' => $recurringSchedule->getCreatedAt() ? $recurringSchedule->getCreatedAt()->format('d/m/Y H:i') : null,
            'updated_at' => $recurringSchedule->getUpdatedAt() ? $recurringSchedule->getUpdatedAt()->format('d/m/Y H:i') : null
        ];
        
        return new JsonResponse($response, Response::HTTP_OK);
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