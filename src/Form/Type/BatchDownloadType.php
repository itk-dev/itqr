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
                'placeholder' => '-- Select a design --',
                'required' => false,
            ])
            ->add('size', IntegerType::class, [
                'label' => new TranslatableMessage('Size (px)'),
                'data' => 400,
                'attr' => ['data-controller' => 'advanced-settings'],
            ])
            ->add('margin', IntegerType::class, [
                'label' => new TranslatableMessage('Margin (px)'),
                'data' => '0',
                'attr' => ['data-controller' => 'advanced-settings'],
            ])
            ->add('backgroundColor', ColorType::class, [
                'label' => new TranslatableMessage('Code background'),
                'data' => '#ffffff',
                'attr' => ['data-controller' => 'advanced-settings'],
            ])
            ->add('foregroundColor', ColorType::class, [
                'label' => new TranslatableMessage('Code color'),
                'data' => '#000000',
                'attr' => ['data-controller' => 'advanced-settings'],
            ])
            ->add('labelText', TextType::class, [
                'label' => new TranslatableMessage('Text'),
                'required' => false,
                'attr' => ['data-controller' => 'advanced-settings'],
            ])
            ->add('labelSize', IntegerType::class, [
                'label' => new TranslatableMessage('Text size'),
                'data' => 15,
                'attr' => ['data-controller' => 'advanced-settings'],
            ])
            ->add('labelTextColor', ColorType::class, [
                'label' => new TranslatableMessage('Text color'),
                'attr' => ['data-controller' => 'advanced-settings'],
            ])
            ->add('labelMarginTop', IntegerType::class, [
                'label' => new TranslatableMessage('Text margin (top)'),
                'data' => 15,
                'attr' => ['data-controller' => 'advanced-settings'],
            ])
            ->add('labelMarginBottom', IntegerType::class, [
                'label' => new TranslatableMessage('Text margin (bottom)'),
                'data' => 15,
                'attr' => ['data-controller' => 'advanced-settings'],
            ])
            ->add('logo', FileType::class, [
                'label' => new TranslatableMessage('Logo'),
                'required' => false,
                'attr' => ['data-controller' => 'advanced-settings'],
            ])
            ->add('logoPath', HiddenType::class, [
                'label' => false,
                'required' => false,
                'attr' => ['data-controller' => 'advanced-settings'],
            ])
            ->add('errorCorrectionLevel', ChoiceType::class, [
                'label' => new TranslatableMessage('Error correction level'),
                'choices' => [
                    ErrorCorrectionLevel::Low->name => ErrorCorrectionLevel::Low->value,
                    ErrorCorrectionLevel::Medium->name => ErrorCorrectionLevel::Medium->value,
                    ErrorCorrectionLevel::Quartile->name => ErrorCorrectionLevel::Quartile->value,
                    ErrorCorrectionLevel::High->name => ErrorCorrectionLevel::High->value,
                ],
                'attr' => ['data-controller' => 'advanced-settings'],
            ])
            ->add('download', SubmitType::class);
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
