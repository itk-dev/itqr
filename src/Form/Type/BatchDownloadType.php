<?php

namespace App\Form\Type;

use App\Entity\Tenant\QrVisualConfig;
use Endroid\QrCode\ErrorCorrectionLevel;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatableMessage;

class BatchDownloadType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('design', EntityType::class, [
                'class' => QrVisualConfig::class,
                'choice_label' => 'name',
                'placeholder' => new TranslatableMessage('qr.select_design'),
                'required' => false,
            ])
            ->add('size', IntegerType::class, [
                'label' => new TranslatableMessage('qr.size.label'),
                'data' => 400,
                'attr' => ['data-controller' => 'advanced-settings'],
                'help' => new TranslatableMessage('qr.size.help'),
            ])
            ->add('margin', IntegerType::class, [
                'label' => new TranslatableMessage('qr.margin.label'),
                'data' => '0',
                'attr' => ['data-controller' => 'advanced-settings'],
                'help' => new TranslatableMessage('qr.margin.help'),
            ])
            ->add('backgroundColor', ColorType::class, [
                'label' => new TranslatableMessage('qr.code_background'),
                'data' => '#ffffff',
                'attr' => ['data-controller' => 'advanced-settings'],
            ])
            ->add('foregroundColor', ColorType::class, [
                'label' => new TranslatableMessage('qr.code_color'),
                'data' => '#000000',
                'attr' => ['data-controller' => 'advanced-settings'],
            ])
            ->add('labelText', TextType::class, [
                'label' => new TranslatableMessage('text.label'),
                'required' => false,
                'attr' => ['data-controller' => 'advanced-settings'],
            ])
            ->add('labelSize', IntegerType::class, [
                'label' => new TranslatableMessage('text.size'),
                'data' => 15,
                'attr' => ['data-controller' => 'advanced-settings'],
            ])
            ->add('labelTextColor', ColorType::class, [
                'label' => new TranslatableMessage('text.color'),
                'attr' => ['data-controller' => 'advanced-settings'],
            ])
            ->add('labelMarginTop', IntegerType::class, [
                'label' => new TranslatableMessage('text.margin.top.label'),
                'data' => 15,
                'attr' => ['data-controller' => 'advanced-settings'],
                'help' => new TranslatableMessage('text.margin.top.help'),
            ])
            ->add('labelMarginBottom', IntegerType::class, [
                'label' => new TranslatableMessage('text.margin.bottom.label'),
                'data' => 15,
                'attr' => ['data-controller' => 'advanced-settings'],
                'help' => new TranslatableMessage('text.margin.bottom.help'),
            ])
            ->add('logo', FileType::class, [
                'label' => new TranslatableMessage('logo.label'),
                'required' => false,
                'attr' => ['data-controller' => 'advanced-settings'],
            ])
            ->add('logoPath', HiddenType::class, [
                'label' => false,
                'required' => false,
                'attr' => ['data-controller' => 'advanced-settings'],
            ])
            ->add('errorCorrectionLevel', ChoiceType::class, [
                'label' => new TranslatableMessage('error_correction.label'),
                'choices' => [
                    'error_correction.Low' => ErrorCorrectionLevel::Low->value,
                    'error_correction.Medium' => ErrorCorrectionLevel::Medium->value,
                    'error_correction.Quartile' => ErrorCorrectionLevel::Quartile->value,
                    'error_correction.High' => ErrorCorrectionLevel::High->value,
                ],
                'choice_translation_domain' => true,
                'attr' => ['data-controller' => 'advanced-settings'],
                'help' => new TranslatableMessage('error_correction.help'),
            ])
            ->add('download', SubmitType::class, [
                'label' => new TranslatableMessage('qr.download'),
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
