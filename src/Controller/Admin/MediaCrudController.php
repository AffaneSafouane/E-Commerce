<?php

namespace App\Controller\Admin;

use App\Entity\Media;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Vich\UploaderBundle\Form\Type\VichImageType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;

class MediaCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Media::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('alt', 'Texte alternatif'),

            TextField::new('imageFile', 'Charger un fichier')
                ->setFormType(VichImageType::class)
                ->onlyOnForms(),

            ImageField::new('path', 'Aperçu')
                ->setBasePath('/uploads/media/') 
                ->onlyOnIndex(),
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            // This ensures the "New" action is allowed and visible to the system
            ->setPermission(Action::NEW , 'ROLE_ADMIN')
            ->add(Action::INDEX, Action::DETAIL);
    }
}
