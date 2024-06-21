<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Event;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager)
    {
        // Création des utilisateurs
        $usersData = [
            ['email' => 'maxence@gmail.com', 'firstName' => 'Maxence', 'lastName' => 'Frechin', 'password' => 'maxence123'],
            ['email' => 'argjentin@gmail.com', 'firstName' => 'Argjentin', 'lastName' => 'Korbi', 'password' => 'argjentin123'],
            ['email' => 'benjamain@gmail.com', 'firstName' => 'Benjamain', 'lastName' => 'Robert', 'password' => 'benjamain123'],
        ];

        foreach ($usersData as $userData) {
            $user = new User();
            $user->setEmail($userData['email']);
            $user->setFirstName($userData['firstName']);
            $user->setLastName($userData['lastName']);
            $user->setPassword($this->passwordHasher->hashPassword($user, $userData['password']));
            $user->setRoles(['ROLE_USER']);
            $manager->persist($user);
            $manager->flush(); // Assurez-vous de flush chaque utilisateur pour garantir l'ID
            $this->addReference($userData['email'], $user);
        }

        // Création des événements assignés à chaque utilisateur
        $eventsData = [
            ['title' => 'Event 1', 'description' => 'Description 1', 'datetime' => new \DateTime('+2 hours'), 'maxParticipants' => 10, 'public' => true, 'creator' => 'maxence@gmail.com'],
            ['title' => 'Event 2', 'description' => 'Description 2', 'datetime' => new \DateTime('+3 hours'), 'maxParticipants' => 5, 'public' => true, 'creator' => 'maxence@gmail.com'],
            ['title' => 'Event 3', 'description' => 'Description 3', 'datetime' => new \DateTime('+4 hours'), 'maxParticipants' => 7, 'public' => true, 'creator' => 'argjentin@gmail.com'],
            ['title' => 'Event 4', 'description' => 'Description 4', 'datetime' => new \DateTime('+5 hours'), 'maxParticipants' => 3, 'public' => true, 'creator' => 'argjentin@gmail.com'],
            ['title' => 'Event 5', 'description' => 'Description 5', 'datetime' => new \DateTime('+6 hours'), 'maxParticipants' => 6, 'public' => true, 'creator' => 'benjamain@gmail.com'],
            ['title' => 'Event 6', 'description' => 'Description 6', 'datetime' => new \DateTime('+7 hours'), 'maxParticipants' => 2, 'public' => true, 'creator' => 'benjamain@gmail.com'],
        ];

        foreach ($eventsData as $eventData) {
            $event = new Event();
            $event->setTitle($eventData['title']);
            $event->setDescription($eventData['description']);
            $event->setDatetime($eventData['datetime']);
            $event->setMaxParticipants($eventData['maxParticipants']);
            $event->setPublic($eventData['public']);
            $event->setCreator($this->getReference($eventData['creator']));
            $manager->persist($event);
        }

        $manager->flush();
    }
}
