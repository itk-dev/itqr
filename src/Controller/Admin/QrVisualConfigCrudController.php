<?php

namespace App\Controller\Admin;

use App\Entity\Tenant\QrVisualConfig;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use Endroid\QrCode\ErrorCorrectionLevel;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Translation\TranslatableMessage;

class QrVisualConfigCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return QrVisualConfig::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle('index', new TranslatableMessage('QR Designs'))
            ->setPageTitle('new', new TranslatableMessage('Create QR Design'))
            ->setPageTitle('edit', new TranslatableMessage('Edit QR Design'))
            ->setEntityLabelInSingular(new TranslatableMessage('QR Design'));
    }
    public function new(AdminContext $context)
    {
        return parent::new($context);
    }

    public function configureFields(string $pageName): iterable
    {
        if (Crud::PAGE_INDEX === $pageName) {
            return [
                TextField::new('name')->setLabel(new TranslatableMessage('Name')),
                TextField::new('size')->setLabel(new TranslatableMessage('Size (px)')),
                Field::new('customUrlButton', new TranslatableMessage('Preview Design'))
                    ->setTemplatePath('fields/link/linkExample.html.twig')
                    ->hideOnForm(),
            ];
        }
        if (Crud::PAGE_EDIT === $pageName || Crud::PAGE_NEW === $pageName) {
            return [
                TextField::new('name')
                    ->setLabel(new TranslatableMessage('Name'))
                    ->setHelp(new TranslatableMessage('Name of the QR design.')),
                Field::new('size')
                    ->setLabel(new TranslatableMessage('Size'))
                    ->setHelp(new TranslatableMessage('Size of the QR code in pixels.')),
                Field::new('margin')
                    ->setLabel(new TranslatableMessage('Margin'))
                    ->setHelp(new TranslatableMessage('Margin is the whitespace around the QR code in pixels.')),
                Field::new('backgroundColor')
                    ->setFormType(ColorType::class)
                    ->setLabel(new TranslatableMessage('Background color')),
                Field::new('foregroundColor')
                    ->setFormType(ColorType::class)
                    ->setLabel(new TranslatableMessage('Code color')),
                TextField::new('labelText')
                    ->setLabel(new TranslatableMessage('Label'))
                    ->setHelp(new TranslatableMessage('Label is a text that is displayed below the QR code.'))
                    ->setRequired(false),
                Field::new('labelSize')
                    ->setLabel(new TranslatableMessage('Text size'))
                    ->setHelp(new TranslatableMessage('Text size is the size of the label in pixels.')),
                Field::new('labelTextColor')
                    ->setFormType(ColorType::class)
                    ->setLabel(new TranslatableMessage('Text color')),
                Field::new('labelMarginTop')
                    ->setLabel(new TranslatableMessage('Text margin (top)')),
                Field::new('labelMarginBottom')
                    ->setLabel(new TranslatableMessage('Text margin (bund)')),
                ImageField::new('logo', 'Logo')
                    ->setBasePath('uploads')
                    ->setUploadDir('public/uploads')
                    ->setUploadedFileNamePattern('[randomhash].[extension]'),
                ChoiceField::new('errorCorrectionLevel')
                    ->setLabel(new TranslatableMessage('Error correction level'))
                    ->allowMultipleChoices(false)
                    ->setChoices([
                        'Low' => ErrorCorrectionLevel::Low,
                        'Medium' => ErrorCorrectionLevel::Medium,
                        'Quartile' => ErrorCorrectionLevel::Quartile,
                        'High' => ErrorCorrectionLevel::High
                    ])
            ];
        }

        return [];
    }

    public function configureAssets(Assets $assets): Assets
    {
        return $assets
            ->addWebpackEncoreEntry('app');
    }
}
