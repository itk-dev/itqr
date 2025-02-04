<?php

namespace App\Controller\Admin;

use App\Entity\Qr;
use App\Form\Type\UrlsType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
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
    public function __construct()
    {
    }

    public static function getEntityFqcn(): string
    {
        return Qr::class;
    }

    public function createEntity(string $entityFqcn): Qr
    {
        $qr = new Qr();
        $user = $this->getUser();
        if ($user) {
            $qr->setAuthor((string)$user->getId());
        } else {
            $qr->setAuthor('anonymous');
        }

        return $qr;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setDefaultSort(['updatedAt' => 'DESC']);
    }


    public function configureFields(string $pageName): iterable
    {
        if ($pageName === Crud::PAGE_INDEX) {
            yield TextField::new('title', new TranslatableMessage('Title'));
            yield TextEditorField::new('description', new TranslatableMessage('Description'));
            yield CollectionField::new('urls', new TranslatableMessage('URLs'))
                ->setFormTypeOption('entry_type', UrlsType::class)
                ->allowAdd()
                ->allowDelete()
                ->renderExpanded();
            yield ChoiceField::new('mode', new TranslatableMessage('Mode'))
                ->renderAsNativeWidget();
            yield TextField::new('author', new TranslatableMessage('Author'))
                ->setDisabled();
        }

        if ($pageName === Crud::PAGE_EDIT || $pageName === Crud::PAGE_NEW) {
            yield IdField::new('id', 'ID')
                ->setDisabled()
                ->hideOnForm();
            yield TextField::new('title', new TranslatableMessage('Title'));
            yield ChoiceField::new('mode', new TranslatableMessage('Mode'))
                ->renderAsNativeWidget();
            yield TextEditorField::new('description', new TranslatableMessage('Description'));
            yield CollectionField::new('urls', new TranslatableMessage('URLs'))
                ->setFormTypeOption('entry_type', UrlsType::class)
                ->allowAdd()
                ->allowDelete()
                ->renderExpanded();
            yield TextField::new('author', new TranslatableMessage('Author'))
                ->setDisabled()
                ->hideOnForm();
        }
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
