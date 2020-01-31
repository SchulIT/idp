<?php

namespace App\Security\Registration;

use App\Entity\User;
use App\Entity\UserRegistrationCode;
use App\Repository\UserRegistrationCodeRepositoryInterface;
use App\Repository\UserRepositoryInterface;
use App\Service\AttributePersister;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class RegistrationCodeManager {

    private const DefaultTokenLifetime = '2 hours';

    private const RegistrationSessionKey = 'registration_code';

    private $from;
    private $domainBlacklist;

    private $codeRepository;
    private $userRepository;
    private $attributePersister;
    private $passwordEncoder;
    private $session;
    private $translator;
    private $mailer;
    private $twig;

    public function __construct(string $from, string $domainBlacklist, UserRegistrationCodeRepositoryInterface $codeRepository,
                                UserRepositoryInterface $userRepository, AttributePersister $attributePersister,
                                UserPasswordEncoderInterface $passwordEncoder, SessionInterface $session,
                                TranslatorInterface $translator, \Swift_Mailer $mailer, Environment $twig) {
        $this->from = $from;
        $this->domainBlacklist = $domainBlacklist;
        $this->codeRepository = $codeRepository;
        $this->userRepository = $userRepository;
        $this->attributePersister = $attributePersister;
        $this->passwordEncoder = $passwordEncoder;
        $this->session = $session;
        $this->translator = $translator;
        $this->mailer = $mailer;
        $this->twig = $twig;
    }

    /**
     * @param string $code
     * @throws CodeAlreadyRedeemedException
     * @throws CodeNotFoundException
     */
    public function redeem(string $code): void {
        $registrationCode = $this->codeRepository->findOneByCode($code);

        if($registrationCode === null) {
            throw new CodeNotFoundException();
        }

        if($registrationCode->wasRedeemed()) {
            throw new CodeAlreadyRedeemedException();
        }

        $this->session->set(static::RegistrationSessionKey, $registrationCode->getCode());
    }

    /**
     * @return UserRegistrationCode
     */
    public function getLastRedeemedCode(): ?UserRegistrationCode {
        if($this->session->get(static::RegistrationSessionKey) === null) {
            return null;
        }

        $code = $this->codeRepository->findOneByCode($this->session->get(static::RegistrationSessionKey));

        if($code === null) {
            return null;
        }

        $code->setToken(bin2hex(openssl_random_pseudo_bytes(64)));

        return $code;
    }

    public function mustComplete(UserRegistrationCode $code): bool {
        return empty($code->getFirstname()) || empty($code->getLastname()) || empty($code->getEmail());
    }

    /**
     * @param UserRegistrationCode $code
     * @param User $user
     * @param string $password
     * @throws EmailAlreadyExistsException
     * @throws EmailDomainNotAllowedException
     */
    public function complete(UserRegistrationCode $code, User $user, string $password): void {
        // First check: is domain blacklisted?
        if($this->isDomainBlacklisted($user->getEmail())) {
            throw new EmailDomainNotAllowedException();
        }

        // Second check: is address already in use?
        if($this->userRepository->findOneByEmail($user->getEmail()) !== null) {
            throw new EmailAlreadyExistsException();
        }

        $user
            ->setUsername($code->getUsername())
            ->setType($code->getType())
            ->setGrade($code->getGrade())
            ->setInternalId($code->getInternalId())
            ->setIsActive(false);
        $this->attributePersister->persistUserAttributes($code->getAttributes(), $user);
        $user->setPassword($this->passwordEncoder->encodePassword($user, $password));

        $code->setRedeemingUser($user);

        $this->userRepository->persist($user);
        $this->codeRepository->persist($code);

        // Send email
        $content = $this->twig
            ->render('mail/registration.twig', [
                'token' => $code->getToken(),
                'firstname' => $user->getFirstname(),
                'lastname'=> $user->getLastname(),
                'expiry_date' => (new \DateTime())->modify(sprintf('+%s', static::DefaultTokenLifetime))
            ]);

        $message = (new \Swift_Message())
            ->setSubject($this->translator->trans('registration.title', [], 'mail'))
            ->setTo($user->getEmail())
            ->setFrom($this->from)
            ->setBody($content);

        $this->mailer->send($message);
    }

    /**
     * @param string $token
     * @return bool Whether the user account was activated or not
     * @throws TokenNotFoundException
     */
    public function confirm(string $token): bool {
        $this->cleanUp();

        $code = $this->codeRepository->findOneByToken($token);

        if($code === null) {
            throw new TokenNotFoundException();
        }

        if($code->getConfirmedAt() === null) {
            $user = $code->getRedeemingUser();
            $user->setIsActive(true);

            $code->setConfirmedAt(new \DateTime());

            $this->userRepository->persist($user);
            $this->codeRepository->persist($code);

            return true;
        }

        return false;
    }

    /**
     * Cleans up all token which are older than DefaultTokenLifetime
     */
    private function cleanUp() {
        $threshold = (new \DateTime())
            ->modify(sprintf('-%s', static::DefaultTokenLifetime));

        $this->codeRepository->resetTokens($threshold);
    }

    private function isDomainBlacklisted(string $email) {
        // Assume we got a valid address because Symfony has already validated the email address for us earlier in the form
        $domains = explode(';', $this->domainBlacklist);
        $emailParts = explode('@', $email);
        $domain = array_pop($emailParts);

        return in_array($domain, $domains);
    }
}