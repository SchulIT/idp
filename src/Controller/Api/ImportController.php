<?php

namespace App\Controller\Api;

use App\Import\User\UserImporter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ImportController extends Controller {

    /**
     * @Route("/api/import/users")
     * @Method("POST")
     */
    public function import(Request $request, UserImporter $importer) {
        $json = $request->getContent();
        $result = $importer->import($json);

        return $this->json($result);
    }
}