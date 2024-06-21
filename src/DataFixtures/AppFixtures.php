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
            ['email' => 'benjamin@gmail.com', 'firstName' => 'benjamin', 'lastName' => 'Robert', 'password' => 'benjamin123'],
        ];

        foreach ($usersData as $userData) {
            $user = new User();
            $user->setEmail($userData['email']);
            $user->setFirstName($userData['firstName']);
            $user->setLastName($userData['lastName']);
            $user->setPassword($this->passwordHasher->hashPassword($user, $userData['password']));
            $user->setRoles(['ROLE_USER']);
            $manager->persist($user);
            $manager->flush();
            $this->addReference($userData['email'], $user);
        }

        // Création des événements avec des participants
        $eventsData = [
            ['title' => 'Event 1', 'description' => 'Description 1', 'datetime' => new \DateTime('+2 hours'), 'maxParticipants' => 2, 'public' => true, 'creator' => 'maxence@gmail.com', 'participants' => ['argjentin@gmail.com', 'benjamin@gmail.com', 'maxence@gmail.com']],
            ['title' => 'Event 2', 'description' => 'Description 2', 'datetime' => new \DateTime('+3 hours'), 'maxParticipants' => 3, 'public' => true, 'creator' => 'maxence@gmail.com', 'participants' => ['argjentin@gmail.com', 'benjamin@gmail.com']],
            ['title' => 'Event 3', 'description' => 'Description 3', 'datetime' => new \DateTime('+4 hours'), 'maxParticipants' => 2, 'public' => true, 'creator' => 'argjentin@gmail.com', 'participants' => ['maxence@gmail.com']],
            ['title' => 'Event 4', 'description' => 'Description 4', 'datetime' => new \DateTime('+5 hours'), 'maxParticipants' => 2, 'public' => true, 'creator' => 'argjentin@gmail.com', 'participants' => ['argjentin@gmail.com', 'benjamin@gmail.com']],
            ['title' => 'Event 5', 'description' => 'Description 5', 'datetime' => new \DateTime('+6 hours'), 'maxParticipants' => 2, 'public' => true, 'creator' => 'benjamin@gmail.com', 'participants' => ['argjentin@gmail.com', 'benjamin@gmail.com']],
            ['title' => 'Event 6', 'description' => 'Description 6', 'datetime' => new \DateTime('+7 hours'), 'maxParticipants' => 10, 'public' => true, 'creator' => 'benjamin@gmail.com', 'participants' => ['argjentin@gmail.com', 'maxence@gmail.com']]
        ];

        foreach ($eventsData as $eventData) {
            $event = new Event();
            $event->setTitle($eventData['title']);
            $event->setDescription($eventData['description']);
            $event->setDatetime($eventData['datetime']);
            $event->setMaxParticipants($eventData['maxParticipants']);
            $event->setPublic($eventData['public']);
            $event->setCreator($this->getReference($eventData['creator']));
            foreach ($eventData['participants'] as $participantEmail) {
                $event->addParticipant($this->getReference($participantEmail));
            }
            $manager->persist($event);
        }

        $manager->flush();
    }
}
