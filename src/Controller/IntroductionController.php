<?php

namespace App\Controller;

use App\Entity\Introduction;
use App\Entity\Images;
use App\Service\ImageUploadService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IntroductionController extends AbstractController
{
    #[Route('/api/introduction', name: 'create_introduction', methods: ['POST'])]
    public function createIntroduction(
        Request $request,
        EntityManagerInterface $entityManager,
        ImageUploadService $imageUploadService
    ): Response {
        try {
            // Récupérer les données du formulaire
            $title = $request->request->get('title');
            $description = $request->request->get('description');
            $imageFile = $request->files->get('image');

            if (!$title || !$description) {
                return $this->json([
                    'error' => 'Title and description are required'
                ], Response::HTTP_BAD_REQUEST);
            }


            $introduction = new Introduction();
            $introduction->setTitle($title);
            $introduction->setDescription($description);

            $entityManager->persist($introduction);

            $imageUrl = null;
            if ($imageFile) {
                $imagePath = $imageUploadService->upload($imageFile, 'introduction');

                $image = new Images();
                $image->setUrl($imagePath);
                $image->setIntroduction($introduction);

                $entityManager->persist($image);

                $imageUrl = $request->getSchemeAndHttpHost() . '/uploads/images/' . $imagePath;
            }

            $entityManager->flush();

            return $this->json([
                'message' => 'Introduction created successfully',
                'introduction' => [
                    'id' => $introduction->getId(),
                    'title' => $introduction->getTitle(),
                    'description' => $introduction->getDescription(),
                    'image' => $imageUrl
                ]
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
