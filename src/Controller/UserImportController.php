<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\Messenger\MessageBusInterface;
use App\Form\ImportUsersFlow;
use App\Import\ImportUserData;
use App\Import\RecordInvalidException;
use App\Import\UserCsvImportHelper;
use App\Message\MustProvisionUser;
use App\Repository\UserRepositoryInterface;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use League\Csv\Exception as LeagueException;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserImportController extends AbstractController {

    public function __construct(private readonly MessageBusInterface $messageBus)
    {
    }
    #[Route(path: '/users/import', name: 'import_users')]
    public function start(ImportUsersFlow $flow, UserRepositoryInterface $userRepository, UserCsvImportHelper $helper,
                          TranslatorInterface $translator, LoggerInterface $logger): Response {
        $data = new ImportUserData();
        $flow->bind($data);
        $form = $flow->createForm();

        if ($flow->isValid($form)) {
            $flow->saveCurrentStepData($form);

            if ($flow->nextStep()) {
                try {
                    $form = $flow->createForm();
                } catch(RecordInvalidException $e) {
                    $flow->reset();
                    $form->addError(new FormError($translator->trans('import.error.invalid_record', [
                        '%field%' => $e->getField(),
                        '%offset%' => $e->getIndex()
                    ])));
                } catch (LeagueException $e) {
                    $flow->reset();
                    $logger->error('Error parsing CSV file.', [
                        'exception' => $e
                    ]);
                    $form->addError(new FormError('import.users.error.csv'));
                } catch (Exception $e) {
                    $logger->error('Error parsing CSV file.', [
                        'exception' => $e
                    ]);
                    $flow->reset();
                    $form->addError(new FormError('import.users.error.unknown'));
                }
            } else {
                try {
                    $userRepository->beginTransaction();
                    foreach ($data->getUsers() as $user) {
                        $userRepository->persist($user);
                    }

                    foreach($data->getRemoveUsers() as $user) {
                        $userRepository->remove($user);
                    }
                    $userRepository->commit();

                    foreach ($data->getUsers() as $user) {
                        $this->messageBus->dispatch(new MustProvisionUser($user->getId()));
                    }

                    $this->addFlash('success', 'import.users.success');

                    return $this->redirectToRoute('users');
                } catch (Exception $e) {
                    $userRepository->rollBack();
                    $form->addError(new FormError('import.error.unknown'));

                    $logger->error('Error persisting imported registration codes.', [
                        'exception' => $e
                    ]);
                }
            }
        }

        return $this->render('users/import.html.twig', [
            'form' => $form->createView(),
            'flow' => $flow,
            'headers' => $helper->getHeaders(),
            'required' => $helper->getRequiredHeaders()
        ]);
    }
}
