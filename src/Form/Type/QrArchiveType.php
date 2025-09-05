<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatableMessage;

class QrArchiveType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('alternativeUrl', UrlType::class, [
                'label' => new TranslatableMessage('qr.alternativeUrl.label'),
                'attr' => ['data-controller' => 'advanced-settings'],
                'help' => new TranslatableMessage('qr.alternativeUrl.help'),
                'required' => false,
            ])
            ->add('archive', SubmitType::class, [
                'label' => new TranslatableMessage('qr.archive.do'),
            ])
            ->add('Cancel', ButtonType::class, [
                'label' => new TranslatableMessage('qr.archive.cancel'),
                'attr' => [
                    'class' => 'btn btn-default',
                    'onclick' => 'window.location.href="/"',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // enable/disable CSRF protection for this form
            'csrf_protection' => true,
            // the name of the hidden HTML field that stores the token
            'csrf_field_name' => '_token',
            // an arbitrary string used to generate the value of the token
            // using a different string for each form improves its security
            'csrf_token_id' => 'task_item',
        ]);
    }
}
