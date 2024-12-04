<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Password\PasswordHasherInterface;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

use Symfony\Component\HttpFoundation\JsonResponse;

class UserController extends AbstractController
{



    
    #[Route('/api/users/register', methods: ['POST'])]
    public function register(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $data = json_decode($request->getContent(), true);


// Input validation: Check for missing fields
if (!isset($data['nom'], $data['email'], $data['mot_de_passe'], $data['role'])) {
    return new JsonResponse(['error' => 'Missing data'], Response::HTTP_BAD_REQUEST);
}



        // Create a new User entity and set its properties
        $user = new User();
        $user->setNom($data['nom']);
        $user->setEmail($data['email']);
        $user->setRole($data['role']);


         // Hash the password and set it on the user entity
        $hashedPassword = $passwordHasher->hashPassword($user, $data['mot_de_passe']);
        $user->setMotDePasse($hashedPassword);



        // Persist the user entity
        $entityManager->persist($user);
        $entityManager->flush();

        return new JsonResponse(['message' => 'User registered successfully!'], Response::HTTP_CREATED);
    }

    
    
   




    #[Route('/api/login', methods: ['POST'])]
    public function login(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $data = json_decode($request->getContent(), true);

        // Find user by email
        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $data['email']]);
        if (!$user || !$passwordHasher->isPasswordValid($user, $data['mot_de_passe'])) {
            return new JsonResponse(['error' => 'Invalid credentials'], Response::HTTP_UNAUTHORIZED);
        }
        $role = $user->getRole(); 
        $dashboardUrl = '';
        switch ($role) {
            case 'etudiant':
                $dashboardUrl = '/dashboard'; // Path for the student dashboard
                break;
            case 'enseignant':
                $dashboardUrl = '/dashboard'; // Path for the teacher dashboard
                break;
            case 'admin':
                $dashboardUrl = '/admin-dashboard'; // Path for the admin dashboard
                break;
            default:
                return new JsonResponse(['error' => 'Role not recognized'], Response::HTTP_BAD_REQUEST);
        }




        // Logic for generating JWT or session token (not yet implemented)
        return new JsonResponse([
            'message' => 'User logged in successfully',
            'user' => [
                'id' => $user->getId(),
                'nom' => $user->getNom(),
                'email' => $user->getEmail()
            ],
            'token' => 'your_generated_token',
            'dashboardUrl' => $dashboardUrl, // Placeholder for token logic
        ]);
    }

    


  

#[Route('/api/logout', methods: ['GET'])]
public function logout(): Response
{
    // Symfony will automatically invalidate the session
    return new JsonResponse(['message' => 'User logged out successfully'], Response::HTTP_OK);
}




#[Route('/api/users', methods: ['GET'])]
public function getUsers(EntityManagerInterface $entityManager): Response
{
    $users = $entityManager->getRepository(User::class)->findAll();

    // Serialize with groups
    return $this->json($users, Response::HTTP_OK, [], [
        'groups' => ['user:read']
    ]);
}










#[Route('/api/users/{id}', methods: ['GET'])]
public function getUserById(int $id, EntityManagerInterface $entityManager): Response
{
    $user = $entityManager->getRepository(User::class)->find($id);
    if (!$user) {
        return new JsonResponse(['message' => 'User not found!'], Response::HTTP_NOT_FOUND);
    }

    return $this->json($user, Response::HTTP_OK, [], [
        'groups' => ['user:read']
    ]);
}







    #[Route('/api/users/{id}', methods: ['PUT'])]
    public function updateUser(int $id, Request $request, EntityManagerInterface $entityManager,UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = $entityManager->getRepository(User::class)->find($id);
        if (!$user) {
            return new JsonResponse(['message' => 'User not found!'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);
        // Update user properties
        $user->setNom($data['nom']);
        $user->setEmail($data['email']);
        $user->setRole($data['role']);
        
        if (!empty($data['mot_de_passe'])) {
            $hashedPassword = $passwordHasher->hashPassword($user, $data['mot_de_passe']);
            $user->setMotDePasse($hashedPassword);
        }

        // Save updated user
        $entityManager->flush();

        return new JsonResponse(['message' => 'User updated successfully!']);
    }






    #[Route('/api/users/{id}', methods: ['DELETE'])]
    public function deleteUser(int $id, EntityManagerInterface $entityManager): Response
    {
        $user = $entityManager->getRepository(User::class)->find($id);
        if (!$user) {
            return new JsonResponse(['message' => 'User not found!'], Response::HTTP_NOT_FOUND);
        }

        $entityManager->remove($user);
        $entityManager->flush();

        return new JsonResponse(['message' => 'User deleted successfully!']);
    }
    
}
