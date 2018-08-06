<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Import\User\UserImporter;
use App\Repository\UserRepositoryInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/users")
 */
class UserController extends AbstractApiController {

    /**
     * Returns a list of users.
     *
     * @Route("", methods={"GET"})
     * @SWG\Response(
     *     response=200,
     *     description="Returns a list of users",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=User::class))
     *     )
     * )
     * @SWG\Parameter(
     *     name="offset",
     *     required=false,
     *     in="query",
     *     type="integer",
     *     description="For paginated results: specifies the position of the first user to return."
     * )
     * @SWG\Parameter(
     *     name="limit",
     *     required=false,
     *     in="query",
     *     type="integer",
     *     description="For paginated results: specifies the number of users to return."
     * )
     */
    public function list(Request $request, UserRepositoryInterface $userRepository) {
        $offset = $request->query->get('offset');
        $limit = $request->query->get('limit');

        if($offset === null || !is_numeric($offset) || $offset < 0) {
            $offset = 0;
        }

        if($limit !== null && (!is_numeric($limit) || $limit < 0)) {
            $limit = null;
        }

        $users = $userRepository->findAll($offset, $limit);
        return $this->json($users);
    }

    /**
     * Returns a single user.
     *
     * @Route("/{username}", methods={"GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Returns a single user.",
     *     @Model(type=User::class)
     * )
     * @SWG\Response(
     *     response=404,
     *     description="Empty HTTP 404 response in case the user was not found."
     * )
     * @SWG\Parameter(
     *     name="username",
     *     required=true,
     *     in="path",
     *     type="string",
     *     description="Username of the user which should be returned."
     * )
     */
    public function user($username, UserRepositoryInterface $userRepository) {
        $user = $userRepository->findOneByUsername($username);

        if($user === null) {
            throw new NotFoundHttpException();
        }

        return $this->json($user);
    }

    /**
     * Imports users. Note: this procedure adds non-existing users and updates existing ones. Users are identified by
     * their username.
     *
     * @Route("/import", methods={"POST"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Import went successfully.",
     *     @Model(type=App\Import\User\UserImportResult::class)
     * )
     * @SWG\Response(
     *     response=400,
     *     description="Import did not went successfully (no data has been changed).",
     *     @Model(type=App\Import\FailedImportResult::class)
     * )
     * @SWG\Parameter(
     *     name="payload",
     *     in="body",
     *     description="List of users which should be imported.",
     *     @Model(type=App\Import\User\UserImportData::class)
     * )
     */
    public function import(Request $request, UserImporter $importer) {
        $json = $request->getContent();
        $result = $importer->import($json);

        return $this->json($result);
    }
}