<?php

namespace App\Form;

use App\Entity\Facture;
use App\Entity\Client;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class FactureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('client', EntityType::class, [
            'class' => Client::class,
            
        ])
        ->add('devis', DevisType::class, [
            'label' => 'Devis associé',
        ])
        ->add('date', DateTimeType::class, [
            'label' => 'Date',
        ])
        ->add('amount', TextType::class, [
            'label' => 'Montant',
        ])
        ->add('paid', CheckboxType::class, [
            'label' => 'Payée',
            'required' => false,
        ])
        ->add('sendEmail', SubmitType::class, [
            'label' => 'Envoyer l\'email',
        ]);;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Facture::class,
        ]);
    }
}
