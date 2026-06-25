<?php

namespace App\User\Bulk;

use App\Entity\User;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\HttpFoundation\Request;

#[AutoconfigureTag(self::AUTOCONFIGURE_TAG)]
interface BulkActionInterface {
    public const string AUTOCONFIGURE_TAG = 'app.users.bulk.action';

    public function performAction(User $user, Request $request): void;

    public function getKey(): string;

    public function getMessageTranslationKey(): string;
}