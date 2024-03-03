<?php

namespace App\Form;

use App\Entity\Client;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ClientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Nom', TextType::class, [
                'label' => 'Nom',
            ])
            ->add('Prenom', TextType::class, [
                'label' => 'Prenom',
            ])
            ->add('Email', EmailType::class, [
                'label' => 'Adresse Email',
            ])
            ->add('NumeroTelephone', TelType::class, [
                'label' => 'Numero de Telephone',
            ])
            ->add('address_number', TextType::class, [
                'label' => 'Numero de voie',
            ])
            ->add('address_type', TextType::class, [
                'label' => 'Type de voie',
            ])
            ->add('address_name', TextType::class, [
                'label' => 'Nom de voie',
            ])
            ->add('address_zip_code', NumberType::class, [
                'label' => 'Code postal',
            ])
            ->add('address_city', TextType::class, [
                'label' => 'Ville',
            ])
            ->add('address_country', TextType::class, [
                'label' => 'Pays',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Client::class,
        ]);
    }
}
