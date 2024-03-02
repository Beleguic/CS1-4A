<?php

namespace App\Form\Front;

use App\Entity\RequestNewCompanyUser;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RequestNewCompanyUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class)
            ->add('role', ChoiceType::class, [
                'choices' => [
                    'Dirigeant' => 'ROLE_ADMIN',
                    'Plombier' => 'ROLE_PLUMBER',
                    'Comptable' => 'ROLE_ACCOUNTANT',
                ],
                'placeholder' => 'Select a role',
                'label' => 'Rôle',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RequestNewCompanyUser::class,
        ]);
    }
}
