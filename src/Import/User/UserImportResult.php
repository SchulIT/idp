<?php

namespace App\Import\User;

use App\Import\ImportResult as BaseImportResult;
use JMS\Serializer\Annotation as Serializer;

class UserImportResult extends BaseImportResult {

    /**
     * @Serializer\Accessor(getter="getAdded")
     * @Serializer\Type("array<App\Entity\User>")
     */
    private $added;

    /**
     * @Serializer\Accessor(getter="updated")
     * @Serializer\Type("array<App\Entity\User>")
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