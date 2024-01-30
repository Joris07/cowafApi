<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class TestController extends AbstractController
{
    /**
     * @Route("/tasks", name="tasks_list")
     */
    public function list(): Response
    {
        return $this->json(['message' => 'List of tasks']);
    }
}
