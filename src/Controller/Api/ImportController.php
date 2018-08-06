<?php

namespace App\Controller\Api;

use App\Import\User\UserImporter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/import")
 */
class ImportController extends AbstractApiController {

    /**
     * @Route("/users", methods={"POST"})
     */
    public function import(Request $request, UserImporter $importer) {
        $json = $request->getContent();
        $result = $importer->import($json);

        return $this->json($result);
    }
}