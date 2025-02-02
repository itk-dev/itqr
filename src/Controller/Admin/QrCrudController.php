<?php

namespace App\Controller\Admin;

use App\Entity\Qr;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Grpc\Channel;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Translation\TranslatableMessage;

class QrCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Qr::class;
    }

    public function configureFields(string $pageName): iterable
    {
      yield IdField::new('id', 'ID')
        ->setDisabled();

      yield TextField::new('department', new TranslatableMessage('Department'))
        ->setDisabled();
      yield TextField::new('title', new TranslatableMessage('Title'));

      yield TextEditorField::new('description', new TranslatableMessage('Description'));

      yield ChoiceField::new('mode', new TranslatableMessage('Mode'))
        ->renderAsNativeWidget();

      yield AssociationField::new('urls')
        ->setFormTypeOptions(['by_reference' => false])
        ->setTemplatePath('fields/url/urls.html.twig');

      yield AssociationField::new('urls', 'Urls')
        ->hideOnIndex()
        ->addCssClass('field-channels')
        ->setFormTypeOption('multiple', 'true')
        ->setFormTypeOption('attr.data-ea-autocomplete-render-items-as-html', 'true')
        ->setFormTypeOption('attr.data-ea-autocomplete-allow-item-create', 'true')
        ->setFormTypeOption('attr.data-ea-widget', 'ea-autocomplete');
    }

  public function createEditForm(EntityDto $entityDto, KeyValueStore $formOptions, AdminContext $context): FormInterface {
    return $this->modifyFormBuilder($this->createEditFormBuilder($entityDto, $formOptions, $context), Crud::PAGE_EDIT)->getForm();
  }

  public function createNewForm(EntityDto $entityDto, KeyValueStore $formOptions, AdminContext $context): FormInterface
  {
    return $this->modifyFormBuilder($this->createNewFormBuilder($entityDto, $formOptions, $context), Crud::PAGE_NEW)->getForm();
  }

  private function modifyFormBuilder(FormBuilderInterface $builder, string $pageName): FormBuilderInterface
  {
    $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
      $data = $event->getData();
      $dirty = false;
      $channels = $data['channels'] ?? [];
      foreach ($channels as $pos => $channelId) {
        if (!empty(trim($channelId)) && 0 === (int) $channelId) {
          $channel = new Channel(trim($channelId));
          $this->em->persist($channel);
          $this->em->flush();
          $data['channels'][$pos] = $channel->getId();
          $dirty = true;
        }
      }
      if ($dirty) {
        $event->setData($data);
      }
    });

    return $builder;
  }
}
