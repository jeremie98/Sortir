<?php

namespace App\Form;

use App\Entity\Sortie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom')
            ->add('dateSortie')
            ->add('dateLimiteInscription')
            ->add('nbPlace')
            ->add('duree')
            ->add('description')
            ->add('ville')
            ->add('villeOrganisatrice')
            ->add('lieu')
            ->add('rue')
            ->add('codePostal')
            ->add('laptitude')
            ->add('longitude')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
