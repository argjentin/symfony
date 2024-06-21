<?php

namespace App\Tests\Entity;

use App\Entity\Event;
use App\Entity\User;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\User\UserInterface;


class EventTest extends TestCase
{
    private $event;

    protected function setUp(): void
    {
        parent::setUp();
        $this->event = new Event();
    }

    public function testTitle()
    {
        $title = "New Event";
        $this->event->setTitle($title);
        $this->assertEquals($title, $this->event->getTitle());
    }

    public function testDescription()
    {
        $description = "Description of the event";
        $this->event->setDescription($description);
        $this->assertEquals($description, $this->event->getDescription());
    }

    public function testDatetime()
    {
        $datetime = new \DateTime();
        $this->event->setDatetime($datetime);
        $this->assertSame($datetime, $this->event->getDatetime());
    }

    public function testPublic()
    {
        $this->event->setPublic(true);
        $this->assertTrue($this->event->isPublic());
    }

    public function testAddAndRemoveParticipant()
    {
        $participant = $this->createMock(UserInterface::class);
        $this->event->addParticipant($participant);
        $this->assertCount(1, $this->event->getParticipants());

        $this->event->removeParticipant($participant);
        $this->assertCount(0, $this->event->getParticipants());
    }
}
