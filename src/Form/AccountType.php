<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
class AccountType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')
            ->add('oldPassword', PasswordType::class, [
                'mapped' => false,
                'required' => false,
                'constraints' => [
                ],
                'label' => 'Mot de passe actuel',
            ])
            ->add('newPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'label' => 'Nouveau mot de passe',
                'mapped' => false,
                'required' => false,
                'first_options' => [
                    'constraints' => [
                        new Length([
                            'min' => 8,
                            'minMessage' => 'Nouveau mot de passe doit contenir au moins {{ limit }} caractères.',
                            'max' => 4096,
                            'maxMessage' => 'Nouveau mot de passe ne doit pas dépasser {{ limit }} caractères.',
                        ]),
                        new Regex([
                            'pattern' => '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]+$/',
                            'message' => 'Votre mot de passe doit contenir au moins une majuscule, une minuscule, un chiffre et un caractère spécial.'
                        ]),
                    ],
                ],
                'second_options' => ['label' => 'Répéter le mot de passe'],
                'invalid_message' => 'Les mots de passe doivent correspondre.',
            ])
            ->add('lastname', TextType::class, [
                'mapped' => true,
                'constraints' => [
                    new Length([
                        'min' => 2,
                        'minMessage' => 'Votre nom doit contenir au moins {{ limit }} caractères.',
                        'max' => 255,
                        'maxMessage' => 'Votre nom ne doit pas dépasser {{ limit }} caractères.',
                    ]),
                    new Regex([
                        'pattern' => '/^[a-zA-ZÀ-ÖØ-öø-ÿ]+$/',
                        'message' => 'Votre nom ne peut contenir que des majuscules, des minuscules et des lettres accentuées.'
                    ]),
                ],
            ])
            ->add('firstname', TextType::class, [
                'mapped' => true,
                'constraints' => [
                    new Length([
                        'min' => 2,
                        'minMessage' => 'Votre prénom doit contenir au moins {{ limit }} caractères.',
                        'max' => 255,
                        'maxMessage' => 'Votre prénom ne doit pas dépasser {{ limit }} caractères.',
                    ]),
                    new Regex([
                        'pattern' => '/^[a-zA-ZÀ-ÖØ-öø-ÿ]+$/',
                        'message' => 'Votre prénom ne peut contenir que des majuscules, des minuscules et des lettres accentuées.'
                    ]),
                ],
            ])
            ->add('showPassword', CheckboxType::class, [
                'mapped' => false,
                'required' => false,
                'label' => 'Modifier le mot de passe',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'constraints' => [
                new Callback([$this, 'validatePasswordFields'])
            ]
        ]);
    }

    public function validatePasswordFields($data, ExecutionContextInterface $context): void
    {
        $form = $context->getRoot();
        $oldPassword = $form->get('oldPassword')->getData();
        $newPassword = $form->get('newPassword')->getData();

        // Si l'un des champs de mot de passe est rempli, l'autre doit l'être aussi
        if ((!empty($oldPassword) && empty($newPassword)) || (empty($oldPassword) && !empty($newPassword))) {
            $context->buildViolation('Si vous souhaitez changer votre mot de passe, veuillez remplir les deux champs.')
                ->addViolation();
        }
    }
}
