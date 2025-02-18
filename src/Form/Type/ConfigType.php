<?php

namespace App\Form\Type;

use App\QrConfig\Config;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

final class ConfigType extends AbstractType implements DataMapperInterface
{
  public function buildForm(FormBuilderInterface $builder, array $options): void
  {
    $builder
      ->add('size', TextType::class, [
        'required' => false,
        'empty_data' => '',
      ])
      ->add('margin', TextType::class, [
        'required' => false,
        'empty_data' => '',
      ])
      ->add('code_background', TextType::class, [
        'required' => false,
        'empty_data' => '',
      ])
      ->add('code_color', TextType::class, [
        'required' => false,
        'empty_data' => '',
      ])
      ->add('text', TextType::class, [
        'required' => false,
        'empty_data' => '',
      ])
      ->add('text_color', TextType::class, [
        'required' => false,
        'empty_data' => '',
      ])
      ->add('text_margin_top', TextType::class, [
        'required' => false,
        'empty_data' => '',
      ])
      ->add('text_margin_bottom', TextType::class, [
        'required' => false,
        'empty_data' => '',
      ])
      ->add('error_correction_level', TextType::class, [
        'required' => false,
        'empty_data' => '',
      ])
      ->setDataMapper($this)
    ;
  }

  public function configureOptions(OptionsResolver $resolver): void
  {
    $resolver->setDefault('empty_data', null);
  }

  public function mapDataToForms(mixed $viewData, \Traversable $forms): void {
    // there is no data yet, so nothing to prepopulate
    if (null === $viewData) {
      return;
    }

    // invalid data type
    if (!$viewData instanceof Config) {
      throw new UnexpectedTypeException($viewData, Config::class);
    }

    /** @var FormInterface[] $forms */
    $forms = iterator_to_array($forms);

    // initialize form field values
    $forms['size']->setData($viewData->getSize());
    $forms['margin']->setData($viewData->getMargin());
    $forms['code_background']->setData($viewData->getCodeBackground());
    $forms['code_color']->setData($viewData->getCodeColor());
    $forms['text']->setData($viewData->getText());
    $forms['text_color']->setData($viewData->getTextColor());
    $forms['text_margin_top']->setData($viewData->getTextMarginTop());
    $forms['text_margin_bottom']->setData($viewData->getTextMarginBottom());
    $forms['error_correction_level']->setData($viewData->getErrorCorrectionLevel());
  }

  public function mapFormsToData(\Traversable $forms, mixed &$viewData): void {
    /** @var FormInterface[] $forms */
    $forms = iterator_to_array($forms);

    $viewData = new Config(
      $forms['size']->getData(),
      $forms['margin']->getData(),
      $forms['code_background']->getData(),
      $forms['code_color']->getData(),
      $forms['text']->getData(),
      $forms['text_color']->getData(),
      $forms['text_margin_top']->getData(),
      $forms['text_margin_bottom']->getData(),
      $forms['error_correction_level']->getData(),
    );
  }

}
