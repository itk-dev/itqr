<?php

namespace App\Controller\Admin;

use App\Entity\Qr;
use App\Entity\Url;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\BatchActionDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Translation\TranslatableMessage;

/**
 * @template TData of \EasyCorp\Bundle\EasyAdminBundle\Contracts\Controller\CrudControllerInterface
 */
class QrCrudController extends AbstractCrudController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
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

        yield AssociationField::new('urls')
          ->setFormTypeOptions(['by_reference' => false])
          ->setTemplatePath('fields/url/urls.html.twig');

        yield AssociationField::new('urls', 'Urls')
          ->hideOnIndex()
          ->addCssClass('field-channels')
          ->setFormTypeOption('multiple', 'true')
          ->setFormTypeOption('attr.data-ea-autocomplete-render-items-as-html', 'true')
          ->setFormTypeOption('attr.data-ea-autocomplete-allow-item-create', 'true');
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

    public function configureActions(Actions $actions): Actions
    {
        return $actions
          ->addBatchAction(Action::new('setUrl', 'Set url')
          ->linkToCrudAction('setUrl')
          ->addCssClass('btn btn-primary')
          ->setIcon('fa fa-link'));
    }

    /**
     * @return FormInterface<TData>
     */
    public function createEditForm(EntityDto $entityDto, KeyValueStore $formOptions, AdminContext $context): FormInterface
    {
        return $this->modifyFormBuilder($this->createEditFormBuilder($entityDto, $formOptions, $context), Crud::PAGE_EDIT)->getForm();
    }

    /**
     * @return FormInterface<TData>
     */
    public function createNewForm(EntityDto $entityDto, KeyValueStore $formOptions, AdminContext $context): FormInterface
    {
        return $this->modifyFormBuilder($this->createNewFormBuilder($entityDto, $formOptions, $context), Crud::PAGE_NEW)->getForm();
    }

    /**
     * @param FormBuilderInterface<TData> $builder
     *
     * @return FormBuilderInterface<TData>
     */
    private function modifyFormBuilder(FormBuilderInterface $builder, string $pageName): FormBuilderInterface
    {
        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            $data = $event->getData();
            $qr = $event->getForm()->getData();

            foreach ($data['urls'] as $url) {
                $entityFound = $this->entityManager->getRepository(Url::class)->find($url);
                if (!$entityFound) {
                    $urlEntity = new Url();
                    $urlEntity->setUrl($url);
                    $this->persistEntity($this->entityManager, $urlEntity);

                    $qr->addUrl($urlEntity);
                } else {
                    $qr->addUrl($entityFound);
                }

                $this->persistEntity($this->entityManager, $qr);
            }
        });

        return $builder;
    }

    public function setUrl(BatchActionDto $batchActionDto): RedirectResponse
    {
        return $this->redirectToRoute('app_set_url', $batchActionDto->getEntityIds());
    }
}
