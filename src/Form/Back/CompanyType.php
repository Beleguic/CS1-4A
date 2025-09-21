<?php

namespace App\Form\Back;

use App\Entity\Company;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
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
                'label' => 'Nom',
            ])
            ->add('email', EmailType::class, [
                'attr' => [
                    'placeholder' => 'contact@plumbpay.fr',
                ],
                'label' => 'Email',
            ]);

        $getId = $options['data']->getId();
        if($getId !== null){
            $builder
                ->add('imageFile', VichImageType::class, [
                    'label' => 'Logo',
                    'required' => true,
                    'allow_delete' => false,
                    'download_uri' => false,
                ])
            ;
        }

        $builder
            ->add('invoice_email', EmailType::class, [
                'attr' => [
                    'placeholder' => 'invoices@plumbpay.fr',
                ],
                'label' => 'Email de facturation',
                'required' => false,
            ])
            ->add('address_number', TextType::class, [
                'label' => 'Numéro de voie',
                'help' => '10, 20 Bis, 154 Ter etc...',
                'required' => false,
            ])
            ->add('address_type', TextType::class, [
                'label' => 'Type de voie',
                'help' => 'rue, boulevard, avenue etc...',
                'required' => false,
            ])
            ->add('address_name', TextType::class, [
                'label' => 'Nom de voie',
                'help' => 'victor hugo, de la mairie etc...',
                'required' => false,
            ])
            ->add('address_zip_code', TextType::class, [
                'label' => 'Code postal',
                'required' => false,
            ])
            ->add('address_city', TextType::class, [
                'label' => 'Ville',
                'required' => false,
            ])
            ->add('address_country', TextType::class, [
                'label' => 'Pays',
                'required' => false,
            ])
            ->add('company_number', TextType::class, [
                'label' => 'Numéro d\'entreprise',
                'help' => 'SIRET, numéro d\'entreprise (max 20 caractères)',
                'required' => false,
                'attr' => [
                    'placeholder' => '1234567890',
                    'maxlength' => 20,
                ],
                'constraints' => [
                    new Assert\Length([
                        'max' => 20,
                        'maxMessage' => 'Le numéro d\'entreprise ne peut pas dépasser {{ limit }} caractères.',
                    ]),
                    new Assert\Regex([
                        'pattern' => '/^[0-9A-Za-z\s\-\.]*$/',
                        'message' => 'Le numéro d\'entreprise ne peut contenir que des chiffres, lettres, espaces, tirets et points.',
                    ]),
                ],
            ])
            ->add('iban', TextType::class, [
                'label' => 'IBAN (International Bank Account Number)',
                'required' => false,
                'attr' => [
                    'placeholder' => 'FR14 2000 6 0100 1234567890',
                    'maxlength' => 34,
                ],
                'constraints' => [
                    new Assert\Length([
                        'max' => 34,
                        'maxMessage' => 'L\'IBAN ne peut pas dépasser {{ limit }} caractères.',
                    ]),
                    new Assert\Regex([
                        'pattern' => '/^[A-Z]{2}[0-9]{2}[A-Z0-9\s]*$/',
                        'message' => 'L\'IBAN doit commencer par 2 lettres suivies de 2 chiffres, puis des caractères alphanumériques.',
                    ]),
                ],
            ])
            ->add('bic', TextType::class, [
                'label' => 'BIC (Bank Identifier Code)',
                'required' => false,
                'attr' => [
                    'placeholder' => 'BNPAFRPPXXX',
                    'maxlength' => 11,
                ],
                'constraints' => [
                    new Assert\Length([
                        'max' => 11,
                        'maxMessage' => 'Le BIC ne peut pas dépasser {{ limit }} caractères.',
                    ]),
                    new Assert\Regex([
                        'pattern' => '/^[A-Z]{4}[A-Z]{2}[A-Z0-9]{2}([A-Z0-9]{3})?$/',
                        'message' => 'Le BIC doit avoir le format : 4 lettres + 2 lettres + 2 caractères + optionnellement 3 caractères.',
                    ]),
                ],
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
