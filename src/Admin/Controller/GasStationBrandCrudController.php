<?php

namespace App\Admin\Controller;

use App\Entity\GasStationBrand;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use Vich\UploaderBundle\Form\Type\VichImageType;

class GasStationBrandCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return GasStationBrand::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPaginatorPageSize(50);
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->disable(Action::DELETE);
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('reference')
            ->add('name')
            ->add(DateTimeFilter::new('createdAt'))
            ->add(DateTimeFilter::new('updatedAt'));
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnIndex()->setDisabled(),
            TextField::new('uuid')->setDisabled(),
            TextField::new('reference')->hideOnIndex(),
            TextField::new('name'),

            FormField::addPanel('Image'),
            TextField::new('imageFile', 'Upload')
                ->setFormType(VichImageType::class)
                ->onlyOnForms(),
            ImageField::new('image.name', 'Image')
                ->setRequired(true)
                ->setBasePath('/images/gas_stations_brand/')
                ->hideOnForm(),
            TextField::new('image.name', 'Name')->setDisabled()->hideOnIndex(),
            TextField::new('image.originalName', 'originalName')->setDisabled()->hideOnIndex(),
            NumberField::new('image.size', 'Size')->setDisabled()->hideOnIndex(),
            TextField::new('image.mimeType', 'mimeType')->setDisabled()->hideOnIndex(),
            ArrayField::new('image.dimensions', 'Dimensions')->setDisabled()->hideOnIndex(),

            FormField::addPanel('Image Low'),
            TextField::new('imageLowFile', 'Upload')
                ->setFormType(VichImageType::class)
                ->onlyOnForms(),
            ImageField::new('imageLow.name', 'Image Low')
                ->setRequired(true)
                ->setBasePath('/images/gas_stations_brand/')
                ->hideOnForm(),
            TextField::new('imageLow.name', 'Name')->setDisabled()->hideOnIndex(),
            TextField::new('imageLow.originalName', 'originalName')->setDisabled()->hideOnIndex(),
            NumberField::new('imageLow.size', 'Size')->setDisabled()->hideOnIndex(),
            TextField::new('imageLow.mimeType', 'mimeType')->setDisabled()->hideOnIndex(),
            ArrayField::new('imageLow.dimensions', 'Dimensions')->setDisabled()->hideOnIndex(),
        ];
    }
}
