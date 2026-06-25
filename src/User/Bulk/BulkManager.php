<?php

namespace App\User\Bulk;

use App\Repository\UserRepositoryInterface;
use App\Utils\ArrayUtils;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;
use Symfony\Component\HttpFoundation\Request;

readonly class BulkManager {

    public const int MAX_ITEMS = 50;

    /**
     * @var array<string, BulkActionInterface>
     */
    private array $actions;

    public function __construct(
        #[AutowireIterator(BulkActionInterface::AUTOCONFIGURE_TAG)] iterable $actions,
        private UserRepositoryInterface $userRepository
    ) {
        $this->actions = ArrayUtils::createArrayWithKeys(
            iterator_to_array($actions),
            fn(BulkActionInterface $action) => $action->getKey()
        );
    }

    /**
     * @param string[] $userUuids
     * @param string $action
     * @param Request $request
     * @return int
     */
    public function perform(array $userUuids, string $action, Request $request): int {
        if(count($userUuids) > self::MAX_ITEMS) {
            return 0;
        }

        $users = $this->userRepository->findAllByUuids($userUuids);
        $this->userRepository->beginTransaction();

        $counter = 0;

        $action = $this->actions[$action] ?? null;

        if($action === null) {
            return 0;
        }

        foreach($users as $user) {
            $action->performAction($user, $request);
            $counter++;
        }

        $this->userRepository->commit();

        return $counter;
    }

    /**
     * @return array<string, string>
     */
    public function getActions(): array {
        $actions = [ ];

        foreach($this->actions as $action) {
            $actions[$action->getKey()] = $action->getMessageTranslationKey();
        }

        return $actions;
    }

}