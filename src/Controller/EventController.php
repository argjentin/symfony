<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class EventController extends AbstractController
{
    #[Route('/events', name: 'app_events')]
    public function index(): Response
    {
        return $this->render('event/events-list.twig');
    }

    #[Route('/events/registered', name: 'app_events_registered')]
    public function registered(): Response
    {
        return $this->render('event/events-registered.twig');
    }
}
