<?php

namespace App\Form\Type;

use App\Entity\Tenant\QrVisualConfig;
use Endroid\QrCode\ErrorCorrectionLevel;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatableMessage;

/**
 * @extends AbstractType<BatchDownloadType>
 */
class BatchDownloadType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('design', EntityType::class, [
            'class' => QrVisualConfig::class,
            'choice_label' => 'name',
            'placeholder' => '-- Select a design --',
            'required' => false
        ]);
        $builder->add('size', TextType::class, [
            'label' => new TranslatableMessage('Size (px)'),
            'data' => '400',
        ]);
        $builder->add('margin', TextType::class, [
            'label' => new TranslatableMessage('Margin (px)'),
            'data' => '0',
        ]);
        $builder->add('backgroundColor', ColorType::class, [
            'label' => new TranslatableMessage('Code background'),
            'data' => '#ffffff',
        ]);
        $builder->add('foregroundColor', ColorType::class, [
            'label' => new TranslatableMessage('Code color'),
            'data' => '#000000',
        ]);

        $builder->add('labelText', TextType::class, [
            'label' => new TranslatableMessage('Text'),
            'required' => false,
        ]);
        $builder->add('labelSize', IntegerType::class, [
            'label' => new TranslatableMessage('Text size')
        ]);
        $builder->add('labelTextColor', ColorType::class, [
            'label' => new TranslatableMessage('Text color'),
        ]);

        $builder->add('labelMarginTop', IntegerType::class, [
            'label' => new TranslatableMessage('Text margin (top)'),
            'data' => 15,
        ]);

        $builder->add('labelMarginBottom', IntegerType::class, [
            'label' => new TranslatableMessage('Text margin (bund)'),
            'data' => 15,
        ]);
        $builder->add('logo', FileType::class, [
            'label' => new TranslatableMessage('Logo'),
            'required' => false,
        ]);
        $builder->add('errorCorrectionLevel', ChoiceType::class, [
            'label' => new TranslatableMessage('Error correction level'),
            'choices' => [
                'Low' => ErrorCorrectionLevel::Low->value,
                'Medium' => ErrorCorrectionLevel::Medium->value,
                'Quartile' => ErrorCorrectionLevel::Quartile->value,
                'High' => ErrorCorrectionLevel::High->value,
            ],
        ]);
        $builder->add('download', SubmitType::class);
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
