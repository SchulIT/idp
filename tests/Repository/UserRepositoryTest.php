<?php

declare(strict_types=1);

namespace App\Tests\Repository;

use App\Entity\ActiveDirectoryUser;
use App\Entity\User;
use App\Entity\UserType;
use App\Repository\UserRepositoryInterface;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class UserRepositoryTest extends KernelTestCase {

    private ?object $repository;

    private \App\Entity\User $user;
    private $adUser;

    public function setUp(): void {
        $kernel = self::bootKernel();
        $container = $kernel->getContainer();

        $this->repository = $container->get(UserRepositoryInterface::class);

        $type = $container->get('doctrine')->getManager()
            ->getRepository(UserType::class)
            ->findOneBy([
                'alias' => 'user'
            ]);

        $this->user = (new User())
            ->setUsername('user1@example.com')
            ->setFirstname('John')
            ->setLastname('Doe')
            ->setEmail('john.doe@example.com')
            ->setType($type)
            ->setRoles(['ROLE_USER']);

        $this->adUser = (new ActiveDirectoryUser())
            ->setUsername('user2@example.com')
            ->setFirstname('Jane')
            ->setLastname('Doe')
            ->setEmail('jane.doe@example.com')
            ->setType($type)
            ->setRoles(['ROLE_USER'])
            ->setOu('OU=Users,DC=test,DC=lokal')
            ->setGroups([])
            ->setObjectGuid(Uuid::uuid4())
            ->setUserPrincipalName('user2@example.com');

        $this->repository->persist($this->user);
        $this->repository->persist($this->adUser);
    }

    public function testConvertToUser(): void {
        $user = $this->repository->convertToUser($this->adUser);

        $this->assertNotNull($user);
        $this->assertNotInstanceOf(ActiveDirectoryUser::class, $user);
        $this->assertEquals($user->getId(), $this->adUser->getId());
    }

    public function testConvertToActiveDirectoryUser(): void {
        $newAdUser = (new ActiveDirectoryUser())
                ->setOu('OU=Users,DC=test,DC=lokal')
                ->setGroups([])
                ->setObjectGuid(Uuid::uuid4())
                ->setUserPrincipalName('user1@example.com');

        $adUser = $this->repository->convertToActiveDirectory($this->user, $newAdUser);

        $this->assertNotNull($adUser);
        $this->assertInstanceOf(ActiveDirectoryUser::class, $adUser);
        $this->assertEquals($adUser->getId(), $this->user->getId());
        $this->assertEquals($newAdUser->getOu(), $adUser->getOu());
        $this->assertEquals($newAdUser->getGroups(), $adUser->getGroups());
        $this->assertEquals($newAdUser->getObjectGuid()->toString(), $adUser->getObjectGuid()->toString());
        $this->assertEquals($newAdUser->getUserPrincipalName(), $adUser->getUserPrincipalName());
    }
}
