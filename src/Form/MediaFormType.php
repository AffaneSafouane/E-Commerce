<?php

namespace App\Form;

use App\Entity\Media;
use App\Repository\MediaRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Vich\UploaderBundle\Form\Type\VichImageType;

class MediaFormType extends AbstractType
{

    public function __construct(private MediaRepository $mediaRepository)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $existingMedia = $this->mediaRepository->findAll();
        $choices = ['— Upload a new file —' => '__new__'];
        foreach ($existingMedia as $media) {
            $choices[(string) $media] = $media->getId();
        }

        $builder
            ->add('existing_media_id', ChoiceType::class, [
                'label' => 'Select existing media',
                'choices' => $choices,
                'required' => false,
                'mapped' => false,
                'attr' => ['class' => 'media-selector'],
            ])
            ->add('imageFile', VichImageType::class, [
                'label' => 'Fichier Média (Image, Vidéo, Audio)',
                'required' => false,
                'allow_delete' => true,
                'download_uri' => true,
                'download_label' => 'Voir le fichier actuel',
                'image_uri' => true,
                'asset_helper' => true,
            ])
            ->add('alt', TextType::class, [
                'label' => 'Texte alternatif (Alt)',
                'required' => false,
                'attr' => ['placeholder' => 'Description pour l\'accessibilité']
            ])
        ;

        $builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
            $data = $event->getData();
            $form = $event->getForm();

            $existingId = $form->get('existing_media_id')->getData();

            if ($existingId && $existingId !== '__new__') {
                $existing = $this->mediaRepository->find($existingId);
                if ($existing) {
                    $event->setData($existing);
                }
            }
        });

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            $data = $event->getData(); 

            $existingId = $data['existing_media_id'] ?? null;

            if ($existingId && $existingId !== '__new__') {
                unset($data['imageFile']);
                unset($data['alt']);
                $event->setData($data);
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Media::class,
        ]);
    }
}
