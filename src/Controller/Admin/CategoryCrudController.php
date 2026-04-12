<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Form\MediaFormType;
use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class CategoryCrudController extends AbstractCrudController
{
    public function __construct(private AdminUrlGenerator $adminUrlGenerator)
    {
    }

    public function delete(AdminContext $context): KeyValueStore|RedirectResponse|Response
    {
        $category = $context->getEntity()->getInstance();
        $em = $this->container->get('doctrine')->getManager();

        foreach ($category->getMedia() as $media) {
            $media->setCategory(null);
        }
        $em->flush();

        return parent::delete($context);
    }

    public static function getEntityFqcn(): string
    {
        return Category::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->onlyOnIndex();
        yield TextField::new("name");
        yield TextEditorField::new("description");

        yield CollectionField::new('media', 'Ressources Multimédias')
            ->setEntryType(MediaFormType::class)
            ->setFormTypeOption('by_reference', false)
            ->setHelp('Chargez ou séléctionner des images, des vidéos (MP4) ou des fichiers audio (MP3) pour cette catégorie.')
            ->onlyOnForms();

        yield ImageField::new('firstPhotoPath', 'Aperçu')
            ->setBasePath('')
            ->onlyOnIndex();
    }
}
