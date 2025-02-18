<?php

namespace App\Controller\Admin\Field;

use App\Form\Type\ConfigType;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\FieldTrait;
use Symfony\Contracts\Translation\TranslatableInterface;

final class ConfigField implements FieldInterface
{
  use FieldTrait;

  /**
   * @param TranslatableInterface|string|false|null $label
   */
  public static function new(string $propertyName, $label = null): self
  {
    return (new self())
      ->setProperty($propertyName)
      ->setLabel($label)

      ->setTemplatePath('fields/config/config.html.twig')

      ->setFormType(ConfigType::class)
      ;
  }
}