<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class EventController extends AbstractController
{
    #[Route('/events', name: 'event_list')]
    public function list(): Response
    {
        return $this->render('event/list.html.twig');
    }

    #[Route('/event/create', name: 'event_create')]
    public function create(): Response
    {
        return $this->render('event/create.html.twig');
    }
}
