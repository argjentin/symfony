<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Entity\Event;

class EventVoter extends Voter
{
    public const EDIT = 'EDIT';
    public const VIEW = 'VIEW';

    protected function supports(string $attribute, mixed $subject): bool
    {
        // Seuls les attributs EDIT et VIEW sont gérés par ce Voter
        return in_array($attribute, [self::EDIT, self::VIEW])
            && $subject instanceof Event;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        // Si l'utilisateur est anonyme, ne pas autoriser l'accès
        if (!$user instanceof UserInterface) {
            return false;
        }

        // Si l'objet n'est pas une instance de Event, cela ne devrait pas arriver
        if (!$subject instanceof Event) {
            return false;
        }

        // Appel à des méthodes de vérification spécifiques en fonction de l'attribut
        switch ($attribute) {
            case self::EDIT:
                return $this->canEdit($subject, $user);
            case self::VIEW:
                return $this->canView($subject, $user);
        }

        return false;
    }

    private function canEdit(Event $event, UserInterface $user): bool
    {
        // Seul le créateur de l'événement peut le modifier
        return $user === $event->getCreator();
    }

    private function canView(Event $event, UserInterface $user): bool
    {
        // Si l'événement est public, tout le monde peut le voir
        if ($event->isPublic()) {
            return true;
        }

        // Sinon, seuls les utilisateurs connectés peuvent voir l'événement
        return $user instanceof UserInterface;
    }
}