<?php

namespace App\Controller\Admin;

use App\Entity\Qr;
use App\Form\Type\UrlsType;
use App\Helper\DownloadHelper;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Controller\CrudControllerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\BatchActionDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Translation\TranslatableMessage;

/**
 * @template TData of CrudControllerInterface
 */
class QrCrudController extends AbstractCrudController
{
    public function __construct(
        private readonly DownloadHelper $downloadHelper,
    )
    {
    }

    public static function getEntityFqcn(): string
    {
        return Qr::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')
            ->setDisabled();

        yield TextField::new('title', new TranslatableMessage('Title'));

        yield TextareaField::new('description', new TranslatableMessage('Description'))
            ->renderAsHtml();

        yield ChoiceField::new('mode', new TranslatableMessage('Mode'))
            ->renderAsNativeWidget();

        yield CollectionField::new('urls', new TranslatableMessage('URLs'))
            ->setFormTypeOption('entry_type', UrlsType::class)
            ->allowAdd()
            ->allowDelete()
            ->renderExpanded();

        yield Field::new('customUrlButton', new TranslatableMessage('Open Resource'))
            ->setTemplatePath('fields/link/link.html.twig')
            ->hideOnForm();
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
        // Define batch download action
        $batchDownloadAction = Action::new('download', new TranslatableMessage('Download'))
            ->linkToCrudAction('batchDownload')
            ->addCssClass('btn btn-success')
            ->setIcon('fa fa-download');

        // Define single download action
        $singleDownloadAction = Action::new('quickDownload', new TranslatableMessage('Quick download'))
            ->linkToCrudAction('quickDownload');

        // Define batch url change action
        $setUrlAction = Action::new('setUrl', new TranslatableMessage('Set URL'))
            ->linkToCrudAction('setUrl')
            ->addCssClass('btn btn-primary')
            ->setIcon('fa fa-link');

        // Set actions
        return $actions
            ->addBatchAction($batchDownloadAction)
            ->addBatchAction($setUrlAction)
            ->add(Crud::PAGE_INDEX, $singleDownloadAction);
    }

    public function setUrl(BatchActionDto $batchActionDto): RedirectResponse
    {
        return $this->redirectToRoute('app_set_url', $batchActionDto->getEntityIds());
    }

    /**
     * Handles the quick download functionality by generating QR codes for a given entity.
     *
     * @param AdminContext $context The context containing the entity data.
     *
     * @return StreamedResponse The response containing the generated QR codes.
     */
    public function quickDownload(AdminContext $context): StreamedResponse
    {
        $qrEntity = $context->getEntity()->getInstance();
        return $this->downloadHelper->generateQrCodes([$qrEntity], []);
    }

    /**
     * Handles batch download action, redirecting to the batch download route
     * with the provided entity IDs from the BatchActionDto object.
     *
     * @param BatchActionDto $batchActionDto Contains the data for batch action processing.
     *
     * @return RedirectResponse Redirects to the appropriate route for batch download.
     */
    public function batchDownload(BatchActionDto $batchActionDto): RedirectResponse
    {
        return $this->redirectToRoute('app_batch_download', $batchActionDto->getEntityIds());
    }
}
