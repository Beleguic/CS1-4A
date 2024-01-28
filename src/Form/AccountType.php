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
class AccountType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')
            ->add('oldPassword', PasswordType::class, [
                'mapped' => false,
                'constraints' => [
                ],
            ])
            ->add('newPassword', repeatedType::class, [
                'type' => PasswordType::class,
                'label' => 'New Password',
                'mapped' => false,
                'first_options' => [
                    'constraints' => [
                        new Length([
                            'min' => 8,
                            'minMessage' => 'New password should have at least {{ limit }} caracters.',
                            'max' => 4096,
                            'maxMessage' => 'New password should have not exceed {{ limit }} caracters.',
                        ]),
                        new Regex([
                            'pattern' => '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]+$/',
                            'message' => 'Your password must contain at least one uppercase letter, one lowercase letter, one number, and one special character.'
                        ]),
                    ],
                ],
                'second_options' => ['label' => 'Repeat Password'],
                'invalid_message' => 'The password fields must match.',
            ])
            ->add('lastname', TextType::class, [
                'mapped' => true,
                'constraints' => [
                    new Length([
                        'min' => 2,
                        'minMessage' => 'Your lastname should have at least {{ limit }} caracters.',
                        'max' => 255,
                        'maxMessage' => 'Your lastname should have not exceed {{ limit }} caracters.',
                    ]),
                    new Regex([
                        'pattern' => '/^[a-zA-ZÀ-ÖØ-öø-ÿ]+$/',
                        'message' => 'Your lastname can only contain uppercase letters, lowercase letters and accented letters.'
                    ]),
                ],
            ])
            ->add('firstname', textType::class, [
                'mapped' => true,
                'constraints' => [
                    new Length([
                        'min' => 2,
                        'minMessage' => 'Your firstname should have at least {{ limit }} caracters.',
                        'max' => 255,
                        'maxMessage' => 'Your firstname should have not exceed {{ limit }} caracters.',
                    ]),
                    new Regex([
                        'pattern' => '/^[a-zA-ZÀ-ÖØ-öø-ÿ]+$/',
                        'message' => 'Your firstname can only contain uppercase letters, lowercase letters and accented letters.'
                    ]),
                ],
            ])
            ->add('showPassword', CheckboxType::class, [
                'mapped' => false,
                'required' => false,
                'label' => 'Edit password',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
