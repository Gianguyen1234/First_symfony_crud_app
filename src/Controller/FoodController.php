<?php

namespace App\Controller;

use App\Entity\Food;
use App\Form\FoodType;
use App\Repository\FoodRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/food')]
final class FoodController extends AbstractController
{
    // Display all food items
    #[Route(name: 'app_food_index', methods: ['GET'])]
    public function index(FoodRepository $foodRepository): Response
    {
        return $this->render('food/index.html.twig', [
            'food' => $foodRepository->findAll(),
        ]);
    }

    // Create new food item
    #[Route('/new', name: 'app_food_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $food = new Food();
        $form = $this->createForm(FoodType::class, $food); // Assuming you have a FoodType form class
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($food); // Prepare the entity to be saved
            $entityManager->flush(); // Save the entity to the database

            $this->addFlash('success', 'Food added successfully!'); // Flash success message

            return $this->redirectToRoute('app_food_show', ['id' => $food->getId()]); // Redirect to show the newly created food item
        }

        return $this->render('food/new.html.twig', [
            'food' => $food,
            'form' => $form->createView(), // Ensure the form is rendered correctly
        ]);
    }

    // Show details of a specific food item
    #[Route('/{id}', name: 'app_food_show', methods: ['GET'])]
    public function show(Food $food): Response
    {
        return $this->render('food/show.html.twig', [
            'food' => $food,
        ]);
    }

    // Edit existing food item
    #[Route('/{id}/edit', name: 'app_food_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Food $food, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(FoodType::class, $food); // Assuming you have a FoodType form class
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush(); // Update the entity

            $this->addFlash('success', 'Food updated successfully!'); // Flash success message

            return $this->redirectToRoute('app_food_index'); // Redirect back to food list
        }

        return $this->render('food/edit.html.twig', [
            'food' => $food,
            'form' => $form->createView(), // Ensure the form is rendered correctly
        ]);
    }

    // Delete food item
    #[Route('/{id}', name: 'app_food_delete', methods: ['POST'])]
    public function delete(Request $request, Food $food, EntityManagerInterface $entityManager): Response
    {
        // CSRF protection
        if (!$this->isCsrfTokenValid('delete' . $food->getId(), $request->request->get('_token'))) {
            $this->addFlash('danger', 'Invalid CSRF token.');
            return $this->redirectToRoute('app_food_index');
        }

        $entityManager->remove($food); // Remove the food entity
        $entityManager->flush(); // Commit the deletion

        $this->addFlash('success', 'Food deleted successfully!'); // Flash success message

        return $this->redirectToRoute('app_food_index'); // Redirect back to food list
    }
}
