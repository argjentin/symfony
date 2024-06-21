<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Event;
use App\Form\EventFormType;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use App\Service\NotificationService;
use App\Service\EventService;

class EventController extends AbstractController
{
    private $notificationService;
    private $eventService;

    public function __construct(NotificationService $notificationService, EventService $eventService)
    {
        $this->notificationService = $notificationService;
        $this->eventService = $eventService;
    }

    private function sendEmail(string $to, string $subject, string $content): void
    {
        try {
            $email = (new Email())
                ->from('kargjentin@protonmail.com')
                ->to($to)
                ->subject($subject)
                ->html($content);
    
            $this->mailer->send($email);
        } catch (\Exception $e) {
            // Log l'erreur pour un diagnostic plus poussé
            $this->logger->error('Erreur d\'envoi d\'email: ' . $e->getMessage());
        }
    }

    #[Route('/events', name: 'event_list')]
    public function list(Request $request, EntityManagerInterface $entityManager, PaginatorInterface $paginator): Response
    {
        $queryBuilder = $entityManager->getRepository(Event::class)->createQueryBuilder('e');
    
        // Vérifier si l'utilisateur n'est pas connecté
        if (!$this->getUser()) {
            // Ne sélectionner que les événements publics
            $queryBuilder->where('e.public = true');
        }
    
        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1), 
            1
        );
    
        return $this->render('event/list.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    #[Route('/event/create', name: 'event_create')]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {   
        if (!$this->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirectToRoute('app_login');
        }

        $event = new Event();
        $form = $this->createForm(EventFormType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $event->setCreator($this->getUser());
            $entityManager->persist($event);
            $entityManager->flush();
            $this->addFlash('success', 'Événement créé avec succès !');
            return $this->redirectToRoute('event_list');
        } else {
            $errors = [];
            foreach ($form->getErrors(true) as $error) {
                $errors[$error->getOrigin()->getName()] = $error->getMessage();
            }
            // Envoyer les erreurs au template
            $this->addFlash('form_errors', $errors);
        }

        return $this->render('event/create.html.twig', [
            'eventForm' => $form->createView(),
        ]);
    }


    #[Route('/event/edit/{id}', name: 'event_edit')]
    public function edit(Request $request, EntityManagerInterface $entityManager, int $id): Response
    {
        if (!$this->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirectToRoute('app_login');
        }

        $event = $entityManager->getRepository(Event::class)->find($id);
        if (!$event) {
            throw $this->createNotFoundException();
        }

        $this->denyAccessUnlessGranted('EDIT', $event);

        $form = $this->createForm(EventFormType::class, $event);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Événement modifié avec succès.');
            return $this->redirectToRoute('event_list');
        }

        return $this->render('event/edit.html.twig', [
            'eventForm' => $form->createView(),
        ]);
    }

    #[Route('/event/delete/{id}', name: 'event_delete')]
    public function delete(EntityManagerInterface $entityManager, int $id): Response
    {
        $event = $entityManager->getRepository(Event::class)->find($id);
        if (!$event) {
            throw $this->createNotFoundException();
        }

        $this->denyAccessUnlessGranted('EDIT', $event);

        $entityManager->remove($event);
        $entityManager->flush();
        $this->addFlash('success', 'Événement supprimé avec succès.');

        return $this->redirectToRoute('event_list');
    }

    #[Route('/event/register/{id}', name: 'event_register', methods: ['POST'])]
    public function register(Request $request, EntityManagerInterface $entityManager, int $id): Response
    {
        $event = $entityManager->getRepository(Event::class)->find($id);
        if (!$event || !$event->isPublic() || $event->getParticipants()->contains($this->getUser()) || $this->eventService->calculateRemainingSeats($event) <= 0) {
            $this->addFlash('error', 'Inscription impossible.');
            return $this->redirectToRoute('event_list');
        }

        $event->addParticipant($this->getUser());
        $entityManager->flush();

        $this->notificationService->sendEmail(
            $this->getUser()->getEmail(),
            'Confirmation d\'inscription',
            'Vous êtes inscrit à l\'événement ' . $event->getTitle()
        );

        $this->addFlash('success', 'Inscription réussie.');
        return $this->redirectToRoute('event_list');
    }


    #[Route('/event/unregister/{id}', name: 'event_unregister', methods: ['POST'])]
    public function unregister(Request $request, EntityManagerInterface $entityManager, int $id): Response
    {
        $event = $entityManager->getRepository(Event::class)->find($id);
        if (!$event || !$event->getParticipants()->contains($this->getUser())) {
            $this->addFlash('error', 'Désinscription impossible.');
            return $this->redirectToRoute('event_list');
        }

        $event->removeParticipant($this->getUser());
        $entityManager->flush();

        $this->notificationService->sendEmail(
            $this->getUser()->getEmail(),
            'Confirmation de désinscription',
            'Vous êtes désinscrit de l\'événement ' . $event->getTitle()
        );

        $this->addFlash('success', 'Désinscription réussie.');
        return $this->redirectToRoute('event_list');
    }
}