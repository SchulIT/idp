<?php

declare(strict_types=1);

namespace App\Tests\ActiveDirectory;

use App\ActiveDirectory\OptionResolver;
use App\Entity\ActiveDirectoryGradeSyncOption;
use App\Entity\ActiveDirectorySyncOption;
use App\Entity\ActiveDirectorySyncSourceType;
use App\Entity\UserType;
use PHPUnit\Framework\TestCase;

final class OptionsResolverTest extends TestCase {
    public function testResolveGradeByGroup(): void {
        $options = [
            (new ActiveDirectoryGradeSyncOption())->setSource('5A')->setSourceType(ActiveDirectorySyncSourceType::Group)->setGrade('5A'),
            (new ActiveDirectoryGradeSyncOption())->setSource('6A')->setSourceType(ActiveDirectorySyncSourceType::Group)->setGrade('6A'),
            (new ActiveDirectoryGradeSyncOption())->setSource('7A')->setSourceType(ActiveDirectorySyncSourceType::Group)->setGrade('7A'),
            (new ActiveDirectoryGradeSyncOption())->setSource('8A')->setSourceType(ActiveDirectorySyncSourceType::Group)->setGrade('8A')
        ];

        $optionResolver = new OptionResolver();

        $this->assertEquals($options[1], $optionResolver->getOption($options, 'OU=6A,DC=test,DC=lokal', [ '6A' ]));
        $this->assertEquals($options[1], $optionResolver->getOption($options, 'OU=6A,DC=test,DC=lokal', [ 'Some-Group', '6A' ]));
        $this->assertEquals($options[0], $optionResolver->getOption($options, 'OU=6A,DC=test,DC=lokal', [ '5A', '6A' ]));
    }

    public function testResolveGradeByOU(): void {
        $options = [
            (new ActiveDirectoryGradeSyncOption())->setSource('OU=5A,DC=test,DC=lokal')->setSourceType(ActiveDirectorySyncSourceType::Ou)->setGrade('5A'),
            (new ActiveDirectoryGradeSyncOption())->setSource('OU=6A,DC=test,DC=lokal')->setSourceType(ActiveDirectorySyncSourceType::Ou)->setGrade('6A'),
            (new ActiveDirectoryGradeSyncOption())->setSource('OU=7A,DC=test,DC=lokal')->setSourceType(ActiveDirectorySyncSourceType::Ou)->setGrade('7A'),
            (new ActiveDirectoryGradeSyncOption())->setSource('OU=8A,DC=test,DC=lokal')->setSourceType(ActiveDirectorySyncSourceType::Ou)->setGrade('8A')
        ];

        $optionResolver = new OptionResolver();

        $this->assertEquals($options[1], $optionResolver->getOption($options, 'OU=6A,DC=test,DC=lokal', [ '6A' ]));
        $this->assertEquals($options[1], $optionResolver->getOption($options, 'OU=6A,DC=test,DC=lokal', [ 'Some-Group', '6A' ]));
        $this->assertEquals($options[1], $optionResolver->getOption($options, 'OU=6A,DC=test,DC=lokal', [ '5A' ]));
    }

    public function testResolveUserTypeByGroup(): void {
        $userTypeTeacher = (new UserType())
            ->setName('Teacher');

        $userTypeStudent = (new UserType())
            ->setName('Student');

        $userTypeFoo = (new UserType())
            ->setName('Foo');

        $options = [
            (new ActiveDirectorySyncOption())->setSource('Teachers')->setSourceType(ActiveDirectorySyncSourceType::Group)->setUserType($userTypeTeacher),
            (new ActiveDirectorySyncOption())->setSource('Students')->setSourceType(ActiveDirectorySyncSourceType::Group)->setUserType($userTypeStudent),
            (new ActiveDirectorySyncOption())->setSource('Foo')->setSourceType(ActiveDirectorySyncSourceType::Group)->setUserType($userTypeFoo)
        ];

        $optionResolver = new OptionResolver();

        $this->assertEquals($userTypeTeacher, $optionResolver->getOption($options, 'OU=Teachers,DC=test,DC=lokal', ['Teachers', 'Some-Group'])->getUserType());
        $this->assertEquals($userTypeStudent, $optionResolver->getOption($options, 'OU=Students,DC=test,DC=lokal', ['Students', 'Some-Group'])->getUserType());
        $this->assertEquals($userTypeTeacher, $optionResolver->getOption($options, 'OU=Students,DC=test,DC=lokal', ['Teachers', 'Students', 'Some-Group'])->getUserType());
        $this->assertNull($optionResolver->getOption($options, 'OU=Bla,DC=test,DC=lokal', [ ]));
    }

    public function testResolveUserTypeByOU(): void {
        $userTypeTeacher = (new UserType())
            ->setName('Teacher');

        $userTypeStudent = (new UserType())
            ->setName('Student');

        $userTypeFoo = (new UserType())
            ->setName('Foo');

        $options = [
            (new ActiveDirectorySyncOption())->setSource('OU=Teachers,DC=test,DC=lokal')->setSourceType(ActiveDirectorySyncSourceType::Ou)->setUserType($userTypeTeacher),
            (new ActiveDirectorySyncOption())->setSource('OU=Students,DC=test,DC=lokal')->setSourceType(ActiveDirectorySyncSourceType::Ou)->setUserType($userTypeStudent),
            (new ActiveDirectorySyncOption())->setSource('OU=Foo,DC=test,DC=lokal')->setSourceType(ActiveDirectorySyncSourceType::Ou)->setUserType($userTypeFoo)
        ];

        $optionResolver = new OptionResolver();

        $this->assertEquals($userTypeTeacher, $optionResolver->getOption($options, 'OU=Teachers,DC=test,DC=lokal', ['Teachers', 'Some-Group'])->getUserType());
        $this->assertEquals($userTypeStudent, $optionResolver->getOption($options, 'OU=Students,DC=test,DC=lokal', ['Students', 'Some-Group'])->getUserType());
        $this->assertNull($optionResolver->getOption($options, 'OU=Bla,DC=test,DC=lokal', [ ]));
    }
}
