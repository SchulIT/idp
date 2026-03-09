<?php

namespace App\Invitation;

use App\Entity\RegistrationCode;
use App\Repository\RegistrationCodeRepositoryInterface;
use App\Settings\InvitationSettings;
use DateTime;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Twig\Environment;

readonly class Sender {
    public function __construct(
        private InvitationSettings $invitationSettings,
        private RegistrationCodeRepositoryInterface $codeRepository,
        private MailerInterface $mailer,
        private Environment $twig
    ) { }

    public function canSend(): bool {
        return !empty($this->invitationSettings->message)
            && !empty($this->invitationSettings->returnAddress)
            && !empty($this->invitationSettings->subject);
    }

    public function sendSingle(SendInvitationRequest $request): void {
        $code = $request->code;

        if($code->getRedeemingUser() !== null) {
            return;
        }

        $code->setInvitationEmail($request->email);
        $code->setInvitationSentAt(null);

        $this->sendInvitation($code);
    }

    public function send(): void {
        $this->codeRepository->beginTransaction();

        foreach($this->codeRepository->findAllPendingInvitation() as $code) {
            $this->sendInvitation($code);
        }

        $this->codeRepository->commit();
    }

    private function sendInvitation(RegistrationCode $code): void {
        if(empty($code->getInvitationEmail()) || $code->getInvitationSentAt() !== null) {
            return;
        }

        $message = $this->invitationSettings->message;
        $message = str_replace('%code%', $code->getCode(), $message);
        $message = str_replace('%firstname%', $code->getStudent()->getFirstname(), $message);
        $message = str_replace('%lastname%', $code->getStudent()->getLastname(), $message);

        $context = [
            'code' => $code,
            'message' => $message,
            'subject' => $this->invitationSettings->subject,
        ];

        $email = (new Email())
            ->to($code->getInvitationEmail())
            ->subject($this->invitationSettings->subject)
            ->text(
                $this->twig->render('mail/invitation.txt.twig', $context)
            )
            ->html(
                $this->twig->render('mail/invitation.html.twig', $context)
            )
            ->replyTo($this->invitationSettings->returnAddress);

        $this->mailer->send($email);

        $code->setInvitationSentAt(new DateTime());
        $this->codeRepository->persist($code);
    }
}