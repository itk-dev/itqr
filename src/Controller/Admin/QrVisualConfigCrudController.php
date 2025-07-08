<?php

namespace App\Controller\Admin;

use App\Entity\Tenant\QrVisualConfig;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\HiddenField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Endroid\QrCode\ErrorCorrectionLevel;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Translation\TranslatableMessage;

class QrVisualConfigCrudController extends AbstractTenantAwareCrudController
{
    public static function getEntityFqcn(): string
    {
        return QrVisualConfig::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle('index', new TranslatableMessage('visual.index'))
            ->setPageTitle('new', new TranslatableMessage('visual.new'))
            ->setPageTitle('edit', new TranslatableMessage('visual.edit'))
            ->setEntityLabelInSingular(new TranslatableMessage('visual.label_singular'))
            ->overrideTemplate('crud/edit', 'admin/qr_visual_config/edit.html.twig')
            ->overrideTemplate('crud/new', 'admin/qr_visual_config/new.html.twig');
    }

    public function new(AdminContext $context)
    {
        return parent::new($context);
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->update(Crud::PAGE_INDEX, Action::EDIT, fn (Action $action) => $action->setIcon('fa fa-pencil')->setLabel('visual.edit'))
            ->update(Crud::PAGE_INDEX, Action::DELETE, fn (Action $action) => $action->setIcon('fa fa-trash')->setLabel('visual.delete'));
    }

    public function configureFields(string $pageName): iterable
    {
        if (Crud::PAGE_INDEX === $pageName) {
            return [
                TextField::new('name')->setLabel(new TranslatableMessage('qr.title')),
                IntegerField::new('size')->setLabel(new TranslatableMessage('qr.size.label')),
                Field::new('customUrlButton', new TranslatableMessage('qr.preview'))
                    ->setTemplatePath('fields/link/linkExample.html.twig')
                    ->hideOnForm(),
            ];
        }
        if (Crud::PAGE_EDIT === $pageName || Crud::PAGE_NEW === $pageName) {
            return [
                // Id should not be mapped, but we still need the id for the preview generation
                HiddenField::new('id')
                    ->setFormTypeOption('mapped', false)
                    ->setFormTypeOption('data', $this->getContext()->getEntity()->getInstance()->getId()),
                TextField::new('name')
                    ->setLabel(new TranslatableMessage('qr.title'))
                    ->setHelp(new TranslatableMessage('Name of the theme.')),
                IntegerField::new('size')
                    ->setLabel(new TranslatableMessage('qr.size.label'))
                    ->setHelp(new TranslatableMessage('qr.size.help')),
                IntegerField::new('margin')
                    ->setLabel(new TranslatableMessage('qr.margin.label'))
                    ->setHelp(new TranslatableMessage('qr.margin.help')),
                Field::new('backgroundColor')
                    ->setFormType(ColorType::class)
                    ->setLabel(new TranslatableMessage('qr.code_background')),
                Field::new('foregroundColor')
                    ->setFormType(ColorType::class)
                    ->setLabel(new TranslatableMessage('qr.code_color')),
                TextField::new('labelText')
                    ->setLabel(new TranslatableMessage('qr.text.label'))
                    ->setHelp(new TranslatableMessage('Label is a text that is displayed below the QR code.'))
                    ->setRequired(false),
                Field::new('labelSize')
                    ->setLabel(new TranslatableMessage('qr.text.size'))
                    ->setHelp(new TranslatableMessage('Text size is the size of the label in pixels.')),
                Field::new('labelTextColor')
                    ->setFormType(ColorType::class)
                    ->setLabel(new TranslatableMessage('qr.text.color')),
                Field::new('labelMarginTop')
                    ->setLabel(new TranslatableMessage('qr.text.margin.top.label'))
                    ->setHelp(new TranslatableMessage('qr.text.margin.top.help')),
                Field::new('labelMarginBottom')
                    ->setLabel(new TranslatableMessage('qr.text.margin.bottom.label'))
                    ->setHelp(new TranslatableMessage('qr.text.margin.bottom.help')),
                ImageField::new('logo')
                    ->setBasePath('uploads/qr-logos')
                    ->setUploadedFileNamePattern('[ulid]-[slug].[extension]')
                    ->setUploadDir('public/uploads/qr-logos')
                    ->setFormTypeOptions([
                        'required' => false,
                    ]),
                ChoiceField::new('errorCorrectionLevel')
                    ->setLabel(new TranslatableMessage('error_correction.label'))
                    ->setHelp(new TranslatableMessage('error_correction.help'))
                    ->setFormType(ChoiceType::class)
                    ->setFormTypeOptions([
                        'class' => ErrorCorrectionLevel::class,
                        'choice_label' => function (ErrorCorrectionLevel $choice) {
                            return new TranslatableMessage('error_correction.'.$choice->name);
                        },
                        'choices' => ErrorCorrectionLevel::cases(),
                    ]),
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
