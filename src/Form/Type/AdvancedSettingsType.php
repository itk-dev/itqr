<?php

namespace App\Form\Type;

use Endroid\QrCode\ErrorCorrectionLevel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Translation\TranslatableMessage;

class AdvancedSettingsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
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
        $builder->add('logoPath', HiddenType::class, [
            'label' => false,
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
    }
}
