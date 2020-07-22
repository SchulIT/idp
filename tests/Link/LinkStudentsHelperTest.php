<?php

namespace App\Tests\Link;

use App\Entity\User;
use App\Entity\UserType;
use App\Link\LinkStudentsHelper;
use App\Repository\UserRepositoryInterface;
use PHPUnit\Framework\TestCase;

class LinkStudentsHelperTest extends TestCase {
    public function testLinkStudent() {
        $repository = $this->createMock(UserRepositoryInterface::class);

        $parent = (new User())
            ->setUsername('parent@test.org')
            ->setExternalId(null);

        $student = (new User())
            ->setUsername('student@test.org')
            ->setExternalId('42')
            ->setType((new UserType())->setEduPerson(['student']));

        $helper = new LinkStudentsHelper($repository);
        $helper->link($parent, $student);

        $this->assertEquals('42', $parent->getExternalId());
    }

    public function testLinkAdditionalStudent() {
        $repository = $this->createMock(UserRepositoryInterface::class);

        $parent = (new User())
            ->setUsername('parent@test.org')
            ->setExternalId('1');

        $student = (new User())
            ->setUsername('student@test.org')
            ->setExternalId('42')
            ->setType((new UserType())->setEduPerson(['student']));

        $helper = new LinkStudentsHelper($repository);
        $helper->link($parent, $student);

        $this->assertEquals('1,42', $parent->getExternalId());
    }
}