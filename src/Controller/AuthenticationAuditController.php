<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\AuthenticationAudit;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_SUPER_ADMIN')]
class AuthenticationAuditController extends AbstractController {
    private const int ItemsPerPage = 25;

    #[Route('/admin/authentication_audit', name: 'authentication_audit')]
    public function index(EntityManagerInterface $em, Request $request): Response {
        $page = $request->query->getInt('page', 1);
        $username = $request->query->get('username', null);
        $requestId = $request->query->get('request_id', null);

        if($page < 1) {
            $page = 1;
        }

        $qb = $em->createQueryBuilder()
            ->select('a')
            ->from(AuthenticationAudit::class, 'a')
            ->orderBy('a.createdAt', 'DESC')
            ->setFirstResult(($page - 1) * self::ItemsPerPage)
            ->setmaxResults(self::ItemsPerPage);

        if(!empty($username)) {
            $qb->andWhere('a.username LIKE :username')
                ->setParameter('username', '%' . $username . '%');
        }

        if(!empty($requestId)) {
            $qb->andWhere('a.requestId = :requestId')
                ->setParameter('requestId', $requestId);
        }

        $paginator = new Paginator($qb->getQuery());
        $count = $paginator->count();
        $pages = 0;

        if($count > 0) {
            $pages = ceil((float)$count / self::ItemsPerPage);
        }

        return $this->render('authentication_audit/index.html.twig', [
            'items' => $paginator,
            'page' => $page,
            'pages' => $pages,
            'username' => $username,
            'requestId' => $requestId
        ]);
    }
}
