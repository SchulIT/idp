<?php

namespace App\Saml;

class EduPersonAffliation {
    const MEMBER = 'member';
    const STAFF = 'staff';
    const STUDENT = 'student';
    const FACULTY = 'factulty';
    const AFFILIATE = 'affiliate';

    public static function getAffliations() {
        return [
            static::MEMBER,
            static::STUDENT,
            static::FACULTY,
            static::STAFF,
            static::AFFILIATE
        ];
    }
}