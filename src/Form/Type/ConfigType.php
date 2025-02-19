<?php

namespace App\Form\Type;

use App\Entity\QrConfig;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

final class ConfigType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options): void
  {
    $builder
      ->add('size', TextType::class, [
        'required' => false,
      ])
      ->add('margin', TextType::class, [
        'required' => false,
      ])
      ->add('code_background', TextType::class, [
        'required' => false,
      ])
      ->add('code_color', TextType::class, [
        'required' => false,
      ])
      ->add('text', TextType::class, [
        'required' => false,
      ])
      ->add('text_color', TextType::class, [
        'required' => false,
      ])
      ->add('text_margin_top', TextType::class, [
        'required' => false,
      ])
      ->add('text_margin_bottom', TextType::class, [
        'required' => false,
      ])
      ->add('error_correction_level', TextType::class, [
        'required' => false,
      ])
    ;
  }

  public function configureOptions(OptionsResolver $resolver): void
  {
    $resolver->setDefaults([
      'data_class' => QrConfig::class,
    ]);
  }

}
