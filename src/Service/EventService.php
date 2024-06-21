<?php

namespace App\Service;

use App\Entity\Event;

class EventService
{
    public function calculateRemainingSeats(Event $event): int
    {
        return max(0, $event->getMaxParticipants() - count($event->getParticipants()));
    }
}
