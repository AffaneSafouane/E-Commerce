<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use App\Form\MediaFormType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;

class ProductCrudController extends AbstractCrudController
{
    public function __construct(private AdminUrlGenerator $adminUrlGenerator)
    {
    }

    public function delete(AdminContext $context): KeyValueStore|RedirectResponse|Response
    {
        $product = $context->getEntity()->getInstance();
        $em = $this->container->get('doctrine')->getManager();

        foreach ($product->getMedia() as $media) {
            $media->setProduct(null);
        }
        $em->flush();

        return parent::delete($context);
    }

    public static function getEntityFqcn(): string
    {
        return Product::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield TextField::new('name', 'Nom');
        yield TextEditorField::new('description', 'Description')->hideOnIndex();
        yield MoneyField::new('priceHT', 'Prix HT')
            ->setCurrency('EUR')
            ->setStoredAsCents(false);

        yield BooleanField::new('available', 'Disponible')
            ->renderAsSwitch(false);

        yield AssociationField::new('category', 'Catégorie')
            ->setCrudController(CategoryCrudController::class)
            ->setFormTypeOption('choice_label', 'name')
            ->setFormTypeOption('by_reference', true);

        yield CollectionField::new('media', 'Ressources Multimédias')
            ->setEntryType(MediaFormType::class)
            ->setFormTypeOption('by_reference', false)
            ->setHelp('Chargez ou séléctionner des images, des vidéos (MP4) ou des fichiers audio (MP3) pour ce produit.')
            ->onlyOnForms();

        yield ImageField::new('firstPhotoPath', 'Aperçu')
            ->setBasePath('')
            ->onlyOnIndex();
    }
}
