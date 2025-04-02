<?php

namespace App\Controller\Admin\Embed;

use App\Controller\Admin\AbstractTenantAwareCrudController;
use App\Entity\Tenant\Url;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;

/**
 * Class UrlCrudController.
 *
 * Note: This class is not exposed directly in the amin menu but
 * used "embedded" in the QrCrudController
 */
class UrlCrudController extends AbstractTenantAwareCrudController
{
    public static function getEntityFqcn(): string
    {
        return Url::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            UrlField::new('url'),
        ];
    }
}
