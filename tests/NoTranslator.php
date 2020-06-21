<?php

namespace App\Tests;

use Symfony\Component\Translation\Exception\InvalidArgumentException;
use Symfony\Component\Translation\MessageCatalogue;
use Symfony\Component\Translation\MessageCatalogueInterface;
use Symfony\Component\Translation\TranslatorBagInterface;
use Symfony\Contracts\Translation\LocaleAwareInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class NoTranslator implements TranslatorInterface, TranslatorBagInterface, LocaleAwareInterface {

    /** @var TranslatorInterface|TranslatorBagInterface */
    private $translator;

    public function __construct(TranslatorInterface $translator) {
        $this->translator = $translator;
    }

    /**
     * @inheritDoc
     */
    public function trans(string $id, array $parameters = [], string $domain = null, string $locale = null) {
        return $id;
    }

    /**
     * @inheritDoc
     */
    public function setLocale(string $locale) {
        // TODO: Implement setLocale() method.
    }

    /**
     * @inheritDoc
     */
    public function getLocale() {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function getCatalogue($locale = null) {
        return $this->translator->getCatalogue($locale);
    }
}