<?php

namespace App\Import\UserRegistrationCode;

use App\Import\ImportResult;

class UserRegistrationCodeImportResult extends ImportResult {
    /**
     * @Serializer\Accessor(getter="getAdded")
     * @Serializer\Type("array<App\Entity\UserRegistrationCode>")
     */
    private $added;

    /**
     * @Serializer\Accessor(getter="updated")
     * @Serializer\Type("array<App\Entity\UserRegistrationCode>")
     */
    private $updated;

    public function __construct($isSuccessful, $added, $updated) {
        parent::__construct($isSuccessful);

        $this->added = $added;
        $this->updated = $updated;
    }

    public function getAdded() {
        return $this->added;
    }

    public function getUpdated() {
        return $this->updated;
    }
}