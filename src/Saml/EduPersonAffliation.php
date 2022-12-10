<?php

namespace App\Saml;

/**
 * Enumeration for EduPersonAffliation
 */
class EduPersonAffliation {
    public const MEMBER = 'member';
    public const STAFF = 'staff';
    public const STUDENT = 'student';
    public const FACULTY = 'factulty';
    public const AFFILIATE = 'affiliate';

    public static function getAffliations(): array {
        return [
            static::MEMBER,
            static::STUDENT,
            static::FACULTY,
            static::STAFF,
            static::AFFILIATE
        ];
    }
}