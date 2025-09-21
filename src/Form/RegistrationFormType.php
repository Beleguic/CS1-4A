<?php


namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\IsTrue;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Email'
            ])
            ->add('firstname', TextType::class, [
                'label' => 'Prénom'
            ])
            ->add('lastname', TextType::class, [
                'label' => 'Nom'
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'mapped' => false, // ⚠️ ne pas mapper à l'entité
                'first_options' => [
                    'label' => 'Mot de passe',
                    'constraints' => [
                        new NotBlank(['message' => 'Veuillez entrer un mot de passe']),
                        new Length([
                            'min' => 8,
                            'minMessage' => 'Votre mot de passe doit contenir au moins {{ limit }} caractères',
                            'max' => 4096,
                        ]),
                        new Regex([
                            'pattern' => '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&\-_.])[A-Za-z\d@$!%*?&\-_.]+$/',
                            'message' => 'Votre mot de passe doit contenir au moins une majuscule, une minuscule, un chiffre et un caractère spécial.'
                        ]),
                    ],
                ],
                'second_options' => ['label' => 'Répéter le mot de passe'],
                'invalid_message' => 'Les mots de passe doivent correspondre.',
            ])
            // Champs pour l'entreprise
            ->add('companyName', TextType::class, [
                'label' => 'Nom de l\'entreprise',
                'mapped' => false,
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer le nom de votre entreprise']),
                ],
            ])
            ->add('companyEmail', EmailType::class, [
                'label' => 'Email de l\'entreprise',
                'mapped' => false,
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer l\'email de votre entreprise']),
                ],
            ])
            ->add('companyAddress', TextType::class, [
                'label' => 'Adresse de l\'entreprise',
                'mapped' => false,
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer l\'adresse de votre entreprise']),
                ],
            ])
            ->add('companyCity', TextType::class, [
                'label' => 'Ville',
                'mapped' => false,
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer la ville']),
                ],
            ])
            ->add('companyZipCode', TextType::class, [
                'label' => 'Code postal',
                'mapped' => false,
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer le code postal']),
                ],
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'label' => 'J\'accepte les Conditions d\'Utilisation et la Politique de Confidentialité',
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'Vous devez accepter les conditions d\'utilisation pour vous inscrire.',
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}