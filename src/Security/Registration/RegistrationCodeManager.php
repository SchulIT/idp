<?php

namespace App\Security\Registration;

use App\Converter\UserStringConverter;
use App\Entity\User;
use App\Entity\RegistrationCode;
use App\Repository\RegistrationCodeRepositoryInterface;
use App\Repository\UserRepositoryInterface;
use App\Service\AttributePersister;
use App\Service\AttributeResolver;
use DateTime;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;
use function Symfony\Component\String\u;

class RegistrationCodeManager {

    private const DefaultTokenLifetime = '2 hours';

    private const RegistrationSessionKey = 'registration_code';

    private $domainBlocklist;

    private $codeRepository;
    private $userRepository;
    private $attributePersister;
    private $attributeResolver;
    private $passwordEncoder;
    private $session;
    private $translator;
    private $mailer;
    private $userConverter;

    public function __construct(string $domainBlocklist, RegistrationCodeRepositoryInterface $codeRepository,
                                UserRepositoryInterface $userRepository, AttributePersister $attributePersister, AttributeResolver $attributeResolver,
                                UserPasswordEncoderInterface $passwordEncoder, SessionInterface $session,
                                TranslatorInterface $translator, MailerInterface $mailer, UserStringConverter $userConverter) {
        $this->domainBlocklist = $domainBlocklist;
        $this->codeRepository = $codeRepository;
        $this->userRepository = $userRepository;
        $this->attributePersister = $attributePersister;
        $this->attributeResolver = $attributeResolver;
        $this->passwordEncoder = $passwordEncoder;
        $this->session = $session;
        $this->translator = $translator;
        $this->mailer = $mailer;
        $this->userConverter = $userConverter;
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
     * @return RegistrationCode
     */
    public function getLastRedeemedCode(): ?RegistrationCode {
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

    public function mustComplete(RegistrationCode $code): bool {
        return empty($code->getUsername()) || empty($code->getFirstname()) || empty($code->getLastname()) || empty($code->getEmail());
    }

    /**
     * @param RegistrationCode $code
     * @param User $user
     * @param string $password
     * @throws EmailAlreadyExistsException
     * @throws EmailDomainNotAllowedException
     */
    public function complete(RegistrationCode $code, User $user, string $password): void {
        // First check: is domain blacklisted?
        if($user->getEmail() !== null && $this->isDomainBlocked($user->getEmail())) {
            throw new EmailDomainNotAllowedException();
        }

        // Second check: is address already in use?
        if($user->getEmail() !== null && $this->userRepository->findOneByEmail($user->getEmail()) !== null) {
            throw new EmailAlreadyExistsException();
        }

        if($code->getUsername() !== null) {
            $user->setUsername($code->getUsername());
        }

        $user
            ->setType($code->getType())
            ->setGrade($code->getGrade())
            ->setExternalId($code->getExternalId())
            ->setIsEmailConfirmationPending($user->getEmail() !== null);
        $user->setPassword($this->passwordEncoder->encodePassword($user, $password));

        $code->setRedeemingUser($user);

        if($user->getEmail() === null) {
            $code->setConfirmedAt(new DateTime());
        }

        $this->userRepository->persist($user);
        $this->codeRepository->persist($code);
        $this->attributePersister->persistUserAttributes($this->attributeResolver->getAttributesForRegistrationCode($code), $user);

        if($user->getEmail() !== null) {
            $email = (new TemplatedEmail())
                ->to(new Address($user->getEmail(), $this->userConverter->convert($user)))
                ->subject($this->translator->trans('registration.title', [], 'mail'))
                ->textTemplate('mail/registration.txt.twig')
                ->htmlTemplate('mail/registration.html.twig')
                ->context([
                    'username' => $user->getUsername(),
                    'token' => $code->getToken(),
                    'expiry_date' => (new \DateTime())->modify(sprintf('+%s', static::DefaultTokenLifetime))
                ]);

            $this->mailer->send($email);
        }
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
            $user->setIsEmailConfirmationPending(false);

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

    private function isDomainBlocked(string $email) {
        // Assume we got a valid address because Symfony has already validated the email address for us earlier in the form
        $domains = explode(';', $this->domainBlocklist);
        $emailParts = explode('@', $email);
        $domain = array_pop($emailParts);

        return in_array($domain, $domains);
    }
}