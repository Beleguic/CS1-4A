<?php

namespace App\Form\Front;

use App\Entity\Contact;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('lastname', TextType::class, [
                'attr' => [
                    'maxlength' => 255
                ],
                'constraints' => [
                    new Length([
                        'max' => 255,
                        'maxMessage' => 'Your lastname should not be longer than {{ limit }} characters.',
                    ]),
                ],
            ])
            ->add('firstname', TextType::class, [
                'attr' => [
                    'maxlength' => 255
                ],
                'constraints' => [
                    new Length([
                        'max' => 255,
                        'maxMessage' => 'Your firstname should not be longer than {{ limit }} characters.',
                    ]),
                ],
            ])
            ->add('email', EmailType::class, [
                'attr' => [
                    'maxlength' => 255,
                    'placeholder' => 'contact@plumbpay.fr',
                    'type' => 'email'
                ],
                'constraints' => [
                    new Length([
                        'max' => 255,
                        'maxMessage' => 'Your email should not be longer than {{ limit }} characters.',
                    ]),
                ],            ])
            ->add('phone', TelType::class, [
                'label' => 'Phone Number',
                'attr' => [
                    'type' => 'tel'
                ],
            ])
            ->add('company', TextType::class, [
                'attr' => [
                    'maxlength' => 255
                ],
                'constraints' => [
                    new Length([
                        'max' => 255,
                        'maxMessage' => 'Your company should not be longer than {{ limit }} characters.',
                    ]),
                ],
            ])
            ->add('subject', TextType::class, [
                'attr' => [
                    'maxlength' => 255
                ],
                'constraints' => [
                    new Length([
                        'max' => 255,
                        'maxMessage' => 'Your subject should not be longer than {{ limit }} characters.',
                    ]),
                ],
            ])
            ->add('message')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contact::class,
        ]);
    }
}
