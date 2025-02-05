<?php

namespace App\Controller\Admin;

use App\Entity\Qr;
use App\Form\Type\UrlsType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Controller\CrudControllerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use Symfony\Component\Translation\TranslatableMessage;

/**
 * @template TData of CrudControllerInterface
 */
class QrCrudController extends AbstractCrudController
{
    public function __construct(
    ) {
    }

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

        yield CollectionField::new('urls', new TranslatableMessage('URLs'))
            ->setFormTypeOption('entry_type', UrlsType::class)
            ->allowAdd()
            ->allowDelete()
            ->renderExpanded();
    }

    /**
     * @todo get department choices from somewhere.
     */
    public function configureFilters(Filters $filters): Filters
    {
        return parent::configureFilters($filters)
          ->add(ChoiceFilter::new('department')
            ->setChoices(['a', 'b'])
          )
          ->add('title')
          ->add('description');
    }
}
