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

#[Route('/schedule-exceptions')]
class ScheduleExeptionController extends AbstractController
{
 
#[Route('', name: 'app_schedule_exeption_index', methods: ['GET'])]
public function index(ScheduleExeptionRepository $scheduleExeptionRepository): JsonResponse
{
    $exeptions = $scheduleExeptionRepository->findAll();
    $data = [];
    
    foreach ($exeptions as $exeption) {
        $data[] = [
            'id' => $exeption->getId(),
            'recurring_schedule' => $exeption->getRecurringSchedule() ? [
                'id' => $exeption->getRecurringSchedule()->getId(),
                'title' => $exeption->getRecurringSchedule()->getTitle()
            ] : null,
            'date' => $exeption->getDate() ? $exeption->getDate()->format('Y-m-d\TH:i:sP') : null,
            'startTime' => $exeption->getStartTime() ? $exeption->getStartTime()->format('Y-m-d\TH:i:sP') : null,
            'endTime' => $exeption->getEndTime() ? $exeption->getEndTime()->format('Y-m-d\TH:i:sP') : null,
            'location' => $exeption->getLocation(),
            'reason' => $exeption->getReason(),
            'createdAt' => $exeption->getCreatedAt() ? $exeption->getCreatedAt()->format('Y-m-d\TH:i:sP') : null,
            'updatedAt' => $exeption->getUpdatedAt() ? $exeption->getUpdatedAt()->format('Y-m-d\TH:i:sP') : null
        ];
    }
    
    return new JsonResponse($data, Response::HTTP_OK);
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
        try {
            $data = json_decode($request->getContent(), true);
            
            if (!$data) {
                return new JsonResponse(['error' => 'Données JSON invalides'], Response::HTTP_BAD_REQUEST);
            }

            $exeption = new ScheduleExeption();
            
            if (isset($data['exeption_date'])) {
                $exeptionDate = \DateTime::createFromFormat('d/m/Y', $data['exeption_date']);
                if (!$exeptionDate) {
                    return new JsonResponse(['error' => 'Format de date exeption_date invalide. Utilisez JJ/MM/AAAA HH:mm'], Response::HTTP_BAD_REQUEST);
                }
                $exeption->setDate($exeptionDate);
            }

            if (isset($data['start_time'])) {
                $startTime = \DateTime::createFromFormat('H:i', $data['start_time']);
                if (!$startTime) {
                    return new JsonResponse(['error' => 'Format de date start_time invalide. Utilisez  HH:mm'], Response::HTTP_BAD_REQUEST);
                }
                $exeption->setStartTime($startTime);
            }

            if (isset($data['end_time'])) {
                $endTime = \DateTime::createFromFormat('H:i', $data['end_time']);
                if (!$endTime) {
                    return new JsonResponse(['error' => 'Format de date end_time invalide. Utilisez HH:mm'], Response::HTTP_BAD_REQUEST);
                }
                $exeption->setEndTime($endTime);
            }

 



            if (isset($data['location'])) $exeption->setLocation($data['location']);
            if (isset($data['is_cancelled'])) $exeption->setIsCancelled($data['is_cancelled']);
            if (isset($data['reason'])) $exeption->setReason($data['reason']);

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

            $exeption->setCreatedAt(new \DateTimeImmutable());
            
            $entityManager->persist($exeption);
            $entityManager->flush();

            $response = [
                'id' => $exeption->getId(),
                'exeption_date' => $exeption->getDate() ? $exeption->getDate()->format('d/m/Y H:i') : null,
                'start_time' => $exeption->getStartTime() ? $exeption->getStartTime()->format('d/m/Y H:i') : null,
                'end_time' => $exeption->getEndTime() ? $exeption->getEndTime()->format('d/m/Y H:i') : null,
                'location' => $exeption->getLocation(),
                'is_cancelled' => $exeption->isCancelled(),
                'reason' => $exeption->getReason(),
                'recurring_schedule' => $exeption->getRecurringSchedule() ? [
                    'id' => $exeption->getRecurringSchedule()->getId(),
                    'title' => $exeption->getRecurringSchedule()->getTitle()
                ] : null,
                'created_at' => $exeption->getCreatedAt()->format('d/m/Y H:i')
            ];
            
            return new JsonResponse($response, Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return new JsonResponse([
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/{id}', name: 'app_schedule_exeption_update', methods: ['POST'])]
    public function update(Request $request, ScheduleExeption $scheduleExeption, EntityManagerInterface $entityManager, SerializerInterface $serializer): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        if (!$data) {
            return new JsonResponse(['error' => 'Données JSON invalides'], Response::HTTP_BAD_REQUEST);
        }

        if (isset($data['exeption_date'])) {
            $exeptionDate = \DateTime::createFromFormat('d/m/Y H:i', $data['exeption_date']);
            if (!$exeptionDate) {
                return new JsonResponse(['error' => 'Format de date exeption_date invalide. Utilisez JJ/MM/AAAA HH:mm'], Response::HTTP_BAD_REQUEST);
            }
            $scheduleExeption->setDate($exeptionDate);
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

        if (isset($data['location'])) $scheduleExeption->setLocation($data['location']);
        if (isset($data['is_cancelled'])) $scheduleExeption->setIsCancelled($data['is_cancelled']);
        if (isset($data['reason'])) $scheduleExeption->setReason($data['reason']);

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