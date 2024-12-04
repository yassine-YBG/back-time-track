<?php

namespace App\Controller;

use App\Entity\EmploiDuTemps;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class EmploiDuTempsController extends AbstractController
{




    #[Route('/api/emplois-du-temps', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent(), true);
        $user = $this->getUser();
       

        // Input validation
        if (!isset($data['titre'], $data['date_debut'], $data['date_fin'],  $data['enseignant'])) {
            return new JsonResponse(['error' => 'Missing required data'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $dateDebut = new \DateTime($data['date_debut']);
            $dateFin = new \DateTime($data['date_fin']);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Invalid date format'], Response::HTTP_BAD_REQUEST);
        }


       

        
        // Create and populate the EmploiDuTemps entity
        $emploiDuTemps = new EmploiDuTemps();
        $emploiDuTemps->setTitre($data['titre']);
        $emploiDuTemps->setDescription($data['description'] ?? null); // optional
        $emploiDuTemps->setDateDebut($dateDebut);
        $emploiDuTemps->setDateFin($dateFin);
        $emploiDuTemps->setRecurrent($data['recurrent'] ?? false); // optional, defaults to false
        $emploiDuTemps->setLieu($data['lieu'] ?? null); // optional
        $emploiDuTemps->setMatiere($data['matiere'] ?? null); // optional
       
        $emploiDuTemps->setEnseignant($data['enseignant']); 
        $emploiDuTemps->setUpdatedAt(new \DateTime()); // Set current timestamp for updated_at
        $emploiDuTemps->setUser($user); // 
        // Persist the entity
        $entityManager->persist($emploiDuTemps);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Emploi du temps created successfully!'], Response::HTTP_CREATED);
    }


   








    #[Route('/api/emplois-du-temps/{id}', methods: ['GET'])]
    public function getEmploiDuTemps(int $id, EntityManagerInterface $entityManager): Response
    {
        $emploiDuTemps = $entityManager->getRepository(EmploiDuTemps::class)->find($id);
        if (!$emploiDuTemps) {
            return new JsonResponse(['error' => 'Emploi du temps not found!'], Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse($emploiDuTemps);
    }







    #[Route('/api/emplois-du-temps', methods: ['GET'])]
    public function getAllEmploisDuTemps(EntityManagerInterface $entityManager): Response
    {
        $emploisDuTemps = $entityManager->getRepository(EmploiDuTemps::class)->findAll();
        $data = [];

        foreach ($emploisDuTemps as $emploiDuTemps) {
            $data[] = [
                'id' => $emploiDuTemps->getId(),
                'titre' => $emploiDuTemps->getTitre(),
                'description' => $emploiDuTemps->getDescription(),
                'date_debut' => $emploiDuTemps->getDateDebut()->format('Y-m-d H:i:s'),
                'date_fin' => $emploiDuTemps->getDateFin()->format('Y-m-d H:i:s'),
                'recurrent' => $emploiDuTemps->isRecurrent(),
                'lieu' => $emploiDuTemps->getLieu(),
                'matiere' => $emploiDuTemps->getMatiere(),
            
                'enseignant' => $emploiDuTemps->getEnseignant(), 
                'updated_at' => $emploiDuTemps->getUpdatedAt()->format('Y-m-d H:i:s'),
            ];
        }

        return new JsonResponse($data);
    }








   /* #[Route('/api/emplois-du-temps/{id}', methods: ['PUT'])]
    public function update(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $emploiDuTemps = $entityManager->getRepository(EmploiDuTemps::class)->find($id);
        if (!$emploiDuTemps) {
            return new JsonResponse(['error' => 'Emploi du temps not found!'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);






        // Update fields only if they exist in the request
        if (isset($data['titre'])) {
            $emploiDuTemps->setTitre($data['titre']);
        }
        if (isset($data['description'])) {
            $emploiDuTemps->setDescription($data['description']);
        }
        if (isset($data['date_debut'])) {
            try {
                $emploiDuTemps->setDateDebut(new \DateTime($data['date_debut']));
            } catch (\Exception $e) {
                return new JsonResponse(['error' => 'Invalid date format for date_debut'], Response::HTTP_BAD_REQUEST);
            }
        }
        if (isset($data['date_fin'])) {
            try {
                $emploiDuTemps->setDateFin(new \DateTime($data['date_fin']));
            } catch (\Exception $e) {
                return new JsonResponse(['error' => 'Invalid date format for date_fin'], Response::HTTP_BAD_REQUEST);
            }
        }
        if (isset($data['recurrent'])) {
            $emploiDuTemps->setRecurrent($data['recurrent']);
        }
        if (isset($data['lieu'])) {
            $emploiDuTemps->setLieu($data['lieu']);
        }
        if (isset($data['matiere'])) {
            $emploiDuTemps->setMatiere($data['matiere']);
        }

        
        if (isset($data['enseignant'])) {
            $emploiDuTemps->setEnseignant($data['enseignant']);
        }

        // Update the updated_at timestamp
        $emploiDuTemps->setUpdatedAt(new \DateTime());

        // Save updated entity
        $entityManager->flush();

        return new JsonResponse(['message' => 'Emploi du temps updated successfully!']);
    }*/

  
    #[Route('/api/emplois-du-temps/{id}', methods: ['PUT'])]
    public function update(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        // Retrieve the Emploi du Temps entity by ID
        $emploiDuTemps = $entityManager->getRepository(EmploiDuTemps::class)->find($id);
        
        if (!$emploiDuTemps) {
            return new JsonResponse(['error' => 'Emploi du temps not found!'], Response::HTTP_NOT_FOUND);
        }
    
        // Decode the request body to get the fields that need to be updated
        $data = json_decode($request->getContent(), true);
        
        if (!$data) {
            return new JsonResponse(['error' => 'Invalid data provided.'], Response::HTTP_BAD_REQUEST);
        }
    
        // Only update the fields that are provided in the request
        if (isset($data['titre'])) {
            $emploiDuTemps->setTitre($data['titre']);
        }
        if (isset($data['description'])) {
            $emploiDuTemps->setDescription($data['description']);
        }
        if (isset($data['date_debut'])) {
            try {
                $emploiDuTemps->setDateDebut(new \DateTime($data['date_debut']));
            } catch (\Exception $e) {
                return new JsonResponse(['error' => 'Invalid date format for date_debut'], Response::HTTP_BAD_REQUEST);
            }
        }
        if (isset($data['date_fin'])) {
            try {
                $emploiDuTemps->setDateFin(new \DateTime($data['date_fin']));
            } catch (\Exception $e) {
                return new JsonResponse(['error' => 'Invalid date format for date_fin'], Response::HTTP_BAD_REQUEST);
            }
        }
        if (isset($data['recurrent'])) {
            $emploiDuTemps->setRecurrent($data['recurrent']);
        }
        if (isset($data['lieu'])) {
            $emploiDuTemps->setLieu($data['lieu']);
        }
        if (isset($data['matiere'])) {
            $emploiDuTemps->setMatiere($data['matiere']);
        }
        if (isset($data['enseignant'])) {
            $emploiDuTemps->setEnseignant($data['enseignant']);
        }
    
        // Update the updated_at timestamp to track the modification time
        $emploiDuTemps->setUpdatedAt(new \DateTime());
    
        // Save the updated entity
        $entityManager->flush();
    
        return new JsonResponse(['message' => 'Emploi du temps updated successfully!']);
    }
    







    

    #[Route('/api/emplois-du-temps/{id}', methods: ['DELETE'])]
    public function delete(int $id, EntityManagerInterface $entityManager): Response
    {
        $emploiDuTemps = $entityManager->getRepository(EmploiDuTemps::class)->find($id);
        if (!$emploiDuTemps) {
            return new JsonResponse(['error' => 'Emploi du temps not found!'], Response::HTTP_NOT_FOUND);
        }

        $entityManager->remove($emploiDuTemps);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Emploi du temps deleted successfully!']);
    }
}
