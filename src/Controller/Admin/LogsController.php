<?php

namespace App\Controller\Admin;

use SchulIT\CommonBundle\Controller\LogController as BaseLogController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted("ROLE_ADMIN")]
#[Route('/admin/logs')]
class LogsController extends BaseLogController {}