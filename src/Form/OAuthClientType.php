<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\OAuthClient;
use League\Bundle\OAuth2ServerBundle\ValueObject\RedirectUri;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Url;

class OAuthClientType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add('identifier', TextType::class, [
                'label' => 'label.client_id.label',
                'disabled' => true
            ])
            ->add('scopeName', TextType::class, [
                'label' => 'label.scope_name.label',
                'help' => 'label.scope_name.help',
            ])
            ->add('redirectUris', CollectionType::class, [
                'label' => 'label.redirect_uri.label',
                'help' => 'label.redirect_uri.help',
                'entry_type' => TextCollectionEntryType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'entry_options' => [
                    'constraints' => new Url()
                ],
                'setter' => function(OAuthClient $client, array $redirectUris, FormInterface $form): void {
                    $client->setredirectUris(...$redirectUris);
                }
            ]);

        $builder
            ->get('redirectUris')
            ->addModelTransformer(new CallbackTransformer(
                function(array|null $redirectUris): array {
                    if($redirectUris === null) {
                        return [ ];
                    }

                    return array_map(fn(RedirectUri $redirectUri) => (string)$redirectUri, $redirectUris);
                },
                function(array|null $redirectUris): array {
                    if($redirectUris === null) {
                        return [ ];
                    }

                    return array_map(fn(string $redirectUri) => new RedirectUri($redirectUri), $redirectUris);
                }
            ));

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event): void {
            $client = $event->getData();
            $form = $event->getForm();

            if(!$client instanceof OAuthClient) {
                return;
            }

            if($client->getServiceProvider()->getId() === null) {
                $form->remove('identifier');
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver): void {
        $resolver->setDefault('data_class', OAuthClient::class);
    }
}
