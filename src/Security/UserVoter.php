<?php
namespace App\Security;

use App\Entity\Client;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class UserVoter extends Voter
{
    // these strings are just invented: you can use anything
    const DELETE = 'delete';
    const EDIT = 'edit';
    const ITEM = "idem";

    protected function supports(string $attribute, $subject): bool
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, [self::DELETE, self::EDIT, self::ITEM])) {
            return false;
        }

        if ($subject && !$subject instanceof User) {
            return false;
        }
        return true;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $client = $token->getUser();

        if (!$client instanceof Client) {
            // the user must be logged in; if not, deny access
            return false;
        }

        /** @var User $user */
        $user = $subject;

        switch ($attribute) {
            case self::DELETE:
                return $this->canDelete($user, $client);
            case self::EDIT:
                return $this->canEdit($user, $client);
            case self::ITEM:
                return $this->canView($user, $client);
        }

    }

    private function canEdit(User $user, Client $client): bool
    {
        return $client === $user->getClient();

    }

    private function canView(User $user, Client $client): bool
    {
       return $client === $user->getClient();
    }

    private function canDelete(User $user, Client $client): bool
    {
        return $client === $user->getClient();
    }



}