<?php

namespace App\User\Bulk;

use App\Entity\User;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag(self::AUTOCONFIGURE_TAG)]
interface BulkActionInterface {
    public const string AUTOCONFIGURE_TAG = 'app.users.bulk.action';

    public function performAction(User $user, mixed $parameter = null): void;

    public function getKey(): string;

    public function getMessageTranslationKey(): string;
}