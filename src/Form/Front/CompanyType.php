<?php

namespace App\Form\Front;

use App\Entity\Company;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class CompanyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => [
                    'placeholder' => 'Plumbpay',
                ],
                'label' => 'Name',
            ])
            ->add('email', EmailType::class, [
                'attr' => [
                    'placeholder' => 'contact@plumbpay.fr',
                ],
                'label' => 'Email',
            ])
            ->add('imageFile', VichImageType::class, [
                'label' => 'Logo',
                'required' => false,
            ])
            ->add('invoice_email', EmailType::class, [
                'attr' => [
                    'placeholder' => 'invoices@plumbpay.fr',
                ],
                'label' => 'Invoice Email',
                'required' => false,
            ])
            ->add('address_number', TextType::class, [
                'label' => 'Address number',
                'help' => '10, 20 Bis, 154 Ter etc...',
                'required' => false,
            ])
            ->add('address_type', TextType::class, [
                'label' => 'Address type',
                'help' => 'rue, boulevard, avenue etc...',
                'required' => false,
            ])
            ->add('address_name', TextType::class, [
                'label' => 'Road name',
                'help' => 'victor hugo, de la mairie etc...',
                'required' => false,
            ])
            ->add('address_zip_code', TextType::class, [
                'label' => 'ZIP Code',
                'required' => false,
            ])
            ->add('address_city', TextType::class, [
                'label' => 'City',
                'required' => false,
            ])
            ->add('address_country', TextType::class, [
                'label' => 'Country',
                'required' => false,
            ])
            ->add('company_number', TextType::class, [
                'label' => 'Company Number',
                'help' => 'SIRET etc...',
                'required' => false,
            ])
            ->add('iban', TextType::class, [
                'label' => 'IBAN',
                'required' => false,
            ])
            ->add('bic', TextType::class, [
                'label' => 'BIC',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Company::class,
        ]);
    }
}
