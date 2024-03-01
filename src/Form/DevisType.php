<?php

namespace App\Form;

use App\Entity\Devis;
use App\Entity\Client;
use App\Entity\Product;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DevisType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('numDevis', TextType::class, [
                'label' => 'Numero de Devis',
            ])
            ->add('entreprise', TextType::class, [
                'label' => 'Entreprise',
            ])
            ->add('client', EntityType::class, [
                'class' => Client::class,
                'label' => 'Sélectionnez un client',
                'choice_label' => 'Nom',
                /*'query_builder' => function (EntityRepository $er) use ($entrepriseId) {
                    return $er->createQueryBuilder('c')
                        ->andWhere('c.entreprise = :entrepriseId')
                        ->setParameter('entrepriseId', $entrepriseId);
                },*/
            ])
            ->add('produits', CollectionType::class, [
                'entry_type' => ProductType::class,
                'label' => "Produits",
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                "allow_extra_fields" => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Devis::class,
        ]);
    }
}
