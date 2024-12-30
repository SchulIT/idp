<?php

namespace App\Controller;

use App\Entity\RegistrationCode;
use App\Entity\User;
use App\Form\RegistrationCodeBulkStudentsWithoutParentAccountType;
use App\Form\RegistrationCodeBulkType;
use App\Form\RegistrationCodeType;
use App\Repository\RegistrationCodeRepositoryInterface;
use App\Repository\UserRepositoryInterface;
use App\Security\Registration\CodeGenerator;
use League\Csv\ByteSequence;
use League\Csv\Writer;
use SchulIT\CommonBundle\Form\ConfirmType;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route(path: '/registration_codes')]
class RegistrationCodeController extends AbstractController {

    private const CodesPerPage = 25;

    public function __construct(private readonly RegistrationCodeRepositoryInterface $repository)
    {
    }

    #[Route(path: '', name: 'registration_codes')]
    public function index(Request $request, UserRepositoryInterface $userRepository): Response {
        $query = $request->query->get('q');
        $page = $request->query->getInt('page');
        $grade = $request->query->get('grade');

        $grades = $userRepository->findGrades();

        if(!in_array($grade, $grades)) {
            $grade = null;
        }

        $paginator = $this->repository->getPaginatedUsers(self::CodesPerPage, $page, $query, $grade);
        $pages = 1;
        if($paginator->count() > 0) {
            $pages = ceil((float)$paginator->count() / self::CodesPerPage);
        }

        return $this->render('codes/index.html.twig', [
            'codes' => $paginator->getIterator(),
            'page' => $page,
            'pages' => $pages,
            'query' => $query,
            'grade' => $grade,
            'grades' => $grades
        ]);
    }

    #[Route(path: '/export', name: 'export_codes')]
    public function export(Request $request, UserRepositoryInterface $userRepository, TranslatorInterface $translator): Response {
        $grade = $request->query->get('grade');

        $grades = $userRepository->findGrades();

        if(!in_array($grade, $grades)) {
            $grade = null;
        }

        if($grade !== null) {
            $filename = sprintf('codes_%s.csv', $grade);
            $codes = $this->repository->findByGrade($grade);
        } else {
            $filename = 'codes.csv';
            $codes = $this->repository->findAll();
        }

        $csv = Writer::createFromString();
        $csv->setDelimiter(';');
        $csv->setOutputBOM(ByteSequence::BOM_UTF8);
        $csv->insertOne([
            $translator->trans('label.firstname'),
            $translator->trans('label.lastname'),
            $translator->trans('label.code'),
            $translator->trans('label.grade'),
            $translator->trans('codes.redeemed')
        ]);

        foreach($codes as $code) {
            $csv->insertOne([
                $code->getStudent()->getFirstname(),
                $code->getStudent()->getLastname(),
                $code->getCode(),
                $code->getStudent()->getGrade(),
                $code->getRedeemingUser() !== null ? $translator->trans('yes') : $translator->trans('no')
            ]);
        }

        $response = new Response(
            $csv->toString(),
            Response::HTTP_OK,
            [
                'Content-Type' => 'application/csv'
            ]);

        $disposition = HeaderUtils::makeDisposition(
            HeaderUtils::DISPOSITION_ATTACHMENT,
            $filename
        );

        $response->headers->set('Content-Disposition', $disposition);

        return $response;
    }

    #[Route(path: '/remove/redeemed', name: 'remove_redeemed_codes')]
    public function removeRedeemed(Request $request): Response {
        $form = $this->createForm(ConfirmType::class, null, [
            'message' => 'codes.remove_redeemed.confirm'
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->removeRedeemed();
            $this->addFlash('success', 'codes.remove_redeemed.success');
            return $this->redirectToRoute('registration_codes');
        }

        return $this->render('codes/remove_redeemed.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route(path: '/add', name: 'add_registration_code')]
    public function add(Request $request, UserRepositoryInterface $userRepository): Response {
        $code = new RegistrationCode();

        if(($studentUuid = $request->query->get('student')) !== null) {
            $code->setStudent($userRepository->findOneByUuid($studentUuid));
        }

        $form = $this->createForm(RegistrationCodeType::class, $code);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->persist($code);
            $this->addFlash('success', 'codes.add.success');

            return $this->redirectToRoute('registration_codes');
        }

        return $this->render('codes/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route(path: '/bulk', name: 'add_registration_code_bulk')]
    public function addBulk(Request $request, UserRepositoryInterface $userRepository, CodeGenerator $codeGenerator): Response {
        $form = $this->createForm(RegistrationCodeBulkType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $grade = $form->get('grade')->getData();
            $users = $userRepository->findStudentsByGrade($grade);
            $count = 0;

            foreach($users as $user) {
                $code = (new RegistrationCode())
                    ->setCode($codeGenerator->generateCode())
                    ->setValidFrom($form->get('validFrom')->getData())
                    ->setStudent($user);

                $this->repository->persist($code);
                $count++;
            }

            $this->addFlash('success', 'codes.bulk.success');
            return $this->redirectToRoute('registration_codes', [
                'grade' => $grade
            ]);
        }

        return $this->render('codes/bulk.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route(path: '/bulk/students_without_parent', name: 'add_registration_code_bulk_for_students_without_parent')]
    public function addBulkForStudentsWithoutParent(Request $request, CodeGenerator $codeGenerator): Response {
        $form = $this->createForm(RegistrationCodeBulkStudentsWithoutParentAccountType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            /** @var User[] $users */
            $users = $form->get('students')->getData();
            $count = 0;

            foreach($users as $user) {
                if($user->getType()->getAlias() !== 'student') {
                    continue;
                }

                if($this->repository->codeForStudentExists($user)) {
                    continue;
                }

                $code = (new RegistrationCode())
                    ->setCode($codeGenerator->generateCode())
                    ->setValidFrom($form->get('validFrom')->getData())
                    ->setStudent($user);

                $this->repository->persist($code);
                $count++;
            }

            $this->addFlash('success', 'codes.bulk_noparents.success');
            return $this->redirectToRoute('registration_codes');
        }

        return $this->render('codes/bulk_students_without_parents.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route(path: '/{uuid}/edit', name: 'edit_registration_code')]
    public function edit(#[MapEntity(mapping: ['uuid' => 'uuid'])] RegistrationCode $code, Request $request): Response {
        if($code->getRedeemingUser() !== null) {
            $this->addFlash('error', 'codes.edit.error.already_redeemed.message');
            return $this->redirectToRoute('registration_codes');
        }

        $form = $this->createForm(RegistrationCodeType::class, $code);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->persist($code);

            $this->addFlash('success', 'codes.edit.success');

            return $this->redirectToRoute('registration_codes');
        }

        return $this->render('codes/edit.html.twig', [
            'form' => $form->createView(),
            'code' => $code
        ]);
    }

    #[Route(path: '/{uuid}/remove', name: 'remove_registration_code')]
    public function remove(#[MapEntity(mapping: ['uuid' => 'uuid'])] RegistrationCode $code, Request $request): Response {
        $form = $this->createForm(ConfirmType::class, [], [
            'message' => 'codes.remove.confirm',
            'message_parameters' => [
                '%code%' => $code->getCode()
            ]
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->remove($code);

            $this->addFlash('success', 'codes.remove.success');
            return $this->redirectToRoute('registration_codes');
        }

        return $this->render('codes/remove.html.twig', [
            'code' => $code,
            'form' => $form->createView()
        ]);
    }

}