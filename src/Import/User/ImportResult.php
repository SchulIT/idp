<?php

namespace App\Import\User;

use App\Import\ImportResult as BaseImportResult;
use JMS\Serializer\Annotation as Serializer;

class ImportResult extends BaseImportResult {

    /**
     * @Serializer\Accessor(getter="getAdded")
     */
    private $added;

    /**
     * @Serializer\Accessor(getter="updated")
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