<?php

namespace App\Form;

use App\Entity\Devis;
use App\Entity\Client;
use App\Entity\Product;
use App\Repository\ClientRepository;
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
            ->add('client', EntityType::class, [
                'class' => Client::class,
                'label' => 'Sélectionnez un client',
                'choice_label' => function (Client $client) {
                    $adresse = '';
                    if ($client->getAddressNumber() && $client->getAddressName()) {
                        $adresse = ' - ' . $client->getAddressNumber() . ' ' . $client->getAddressName();
                        if ($client->getAddressZipCode() && $client->getAddressCity()) {
                            $adresse .= ', ' . $client->getAddressZipCode() . ' ' . $client->getAddressCity();
                        }
                    }
                    return $client->getNom() . ' ' . $client->getPrenom() . $adresse;
                },
                'query_builder' => function (ClientRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->orderBy('c.Nom', 'ASC')
                        ->addOrderBy('c.Prenom', 'ASC');
                },
            ])
            ->add('produits', CollectionType::class, [
                'entry_type' => ProductType::class,
                'entry_options' => [
                    'company_id' => $options['company_id'] ?? null,
                ],
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
            'company_id' => null,
        ]);
    }
}
