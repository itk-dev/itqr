<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
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
        $builder->add('size', TextType::class, [
            'label' => new TranslatableMessage('Størrelse (px)'),
            'data' => '400',
        ]);
        $builder->add('margin', TextType::class, [
            'label' => new TranslatableMessage('Margin (px)'),
            'data' => '0',
        ]);
        $builder->add('backgroundColor', ColorType::class, [
            'label' => new TranslatableMessage('Kode baggrund'),
            'data' => '#ffffff',
        ]);
        $builder->add('foregroundColor', ColorType::class, [
            'label' => new TranslatableMessage('Kode farve'),
            'data' => '#000000',
        ]);

        $builder->add('labelText', TextType::class, [
            'label' => new TranslatableMessage('Tekst'),
        ]);

        $builder->add('labelMargin', IntegerType::class, [
            'label' => new TranslatableMessage('Tekst margin (top og bund)'),
            'data' => 15
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
