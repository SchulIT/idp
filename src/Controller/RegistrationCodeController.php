<?php

namespace App\Controller;

use App\Entity\RegistrationCode;
use App\Form\AttributeDataTrait;
use App\Form\ImportRegistrationCodesFlow;
use App\Form\RegistrationCodeBulkType;
use App\Form\RegistrationCodeType;
use App\Import\ImportRegistrationCodeData;
use App\Import\RecordInvalidException;
use App\Import\RegistrationCsvImportHelper;
use App\Repository\RegistrationCodeRepositoryInterface;
use App\Repository\UserRepositoryInterface;
use App\Security\Registration\CodeGenerator;
use App\Service\AttributePersister;
use App\View\Filter\UserTypeFilter;
use Exception;
use League\Csv\Exception as LeagueException;
use League\Csv\Writer;
use Psr\Log\LoggerInterface;
use SchulIT\CommonBundle\Form\ConfirmType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/admin/registration_codes")
 */
class RegistrationCodeController extends AbstractController {

    use AttributeDataTrait;

    private const CodesPerPage = 25;

    private $repository;

    public function __construct(RegistrationCodeRepositoryInterface $repository) {
        $this->repository = $repository;
    }

    /**
     * @Route("", name="registration_codes")
     */
    public function index(Request $request, UserRepositoryInterface $userRepository) {
        $query = $request->query->get('q');
        $page = $request->query->getInt('page');
        $grade = $request->query->get('grade');

        $grades = $userRepository->findGrades();

        if(!in_array($grade, $grades)) {
            $grade = null;
        }

        $paginator = $this->repository->getPaginatedUsers(static::CodesPerPage, $page, $query);
        $pages = 1;
        if($paginator->count() > 0) {
            $pages = ceil((float)$paginator->count() / static::CodesPerPage);
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

    /**
     * @Route("/export", name="export_codes")
     */
    public function export(Request $request, UserRepositoryInterface $userRepository) {
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
        $csv->setOutputBOM(Writer::BOM_UTF8);
        $csv->insertOne(['Vorname', 'Nachname', 'Code', 'Klasse']);

        foreach($codes as $code) {
            $csv->insertOne([
                $code->getStudent()->getFirstname(),
                $code->getStudent()->getLastname(),
                $code->getCode(),
                $code->getStudent()->getGrade()
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

    /**
     * @Route("/remove/redeemed", name="remove_redeemed_codes")
     */
    public function removeRedeemed(Request $request) {
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

    /**
     * @Route("/add", name="add_registration_code")
     */
    public function add(Request $request) {
        $code = new RegistrationCode();
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

    /**
     * @Route("/bulk", name="add_registration_code_bulk")
     */
    public function addBulk(Request $request, UserRepositoryInterface $userRepository, CodeGenerator $codeGenerator) {
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

    /**
     * @Route("/{uuid}/edit", name="edit_registration_code")
     */
    public function edit(RegistrationCode $code, Request $request) {
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

    /**
     * @Route("/{uuid}/remove", name="remove_registration_code")
     */
    public function remove(RegistrationCode $code, Request $request) {
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