<?php

namespace App\Security\Session;

use App\Entity\User;
use BrowscapPHP\Browscap;
use DateTimeImmutable;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Helper to resolve active sessions. As the default PdoSessionHandler does not support
 * this, we add support by storing the relationship between a user and his/her sessions
 * in a separate database table.
 *
 * We use an EventSubscriber (ActiveSessionsSubscriber.php) to create such relationship
 * after a successful login.
 */
class ActiveSessionsResolver {

    public function __construct(private readonly Connection $connection, private readonly RequestStack $requestStack, private readonly Browscap $browscap) {

    }

    /**
     * @param User $user
     * @return ActiveSession[]
     * @throws Exception
     * @throws \Exception
     */
    public function getSessionsForUser(User $user): array {
        $result = $this->connection->executeQuery('SELECT * FROM session_user WHERE user_id = ?', [$user->getId()]);
        $sessions = [ ];

        $currentSessionId = $this->requestStack->getMainRequest()->getSession()->getId();

        foreach($result->fetchAllAssociative() as $row) {
            $sessions[] = new ActiveSession(
                (int)$row['user_id'],
                $row['session_id'],
                $row['user_agent'],
                new DateTimeImmutable($row['started_at']),
                $row['ip_address'],
                $row['session_id'] === $currentSessionId,
                !empty($row['user_agent']) ? $this->browscap->getBrowser($row['user_agent']) : null
            );
        }

        return $sessions;
    }

    public function createTable(Connection $connection): void {
        $schemaManager = $connection->createSchemaManager();
        $sql = "SHOW TABLES LIKE 'session_user';";
        $row = $connection->executeQuery($sql);

        if($row->fetchAssociative() !== false) {
            return;
        }

        $table = new Table('session_user');
        $table->addColumn('user_id', Types::INTEGER, ['unsigned' => true, 'length' => 10]);
        $table->addColumn('session_id', Types::BINARY, ['length' => 128]);
        $table->addColumn('user_agent', Types::TEXT, ['notnull' => false]);
        $table->addColumn('started_at', Types::DATETIME_IMMUTABLE);
        $table->addColumn('ip_address', Types::STRING, ['length' => 45, 'notnull' => false ]);
        $table->addForeignKeyConstraint('user', ['user_id'], ['id'], ['onUpdate' => 'CASCADE', 'onDelete' => 'CASCADE']);
        $table->addForeignKeyConstraint('sessions', ['session_id'], ['sess_id'], ['onUpdate' => 'CASCADE', 'onDelete' => 'CASCADE']);

        $schemaManager->createTable($table);
    }
}