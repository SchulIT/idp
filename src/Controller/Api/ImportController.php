<?php

namespace App\Controller\Api;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ImportController extends Controller {

    /**
     * @Route("/api/import/users")
     * @Method("POST")
     */
    public function import(Request $request) {
        $json = $request->getContent();
        $importer = $this->get('app.import.user');
        $result = $importer->import($json);

        return $this->json($result);
    }
}