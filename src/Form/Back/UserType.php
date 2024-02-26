<?php
namespace App\Form\Back;

use App\Entity\Company;
use App\Entity\User;
use App\Service\RoleService;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class UserType extends AbstractType
{
    private $roleService;

    public function __construct(RoleService $roleService)
    {
        $this->roleService = $roleService;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $roles = $this->roleService->getRoles();

        $rolesChoices = array_combine($roles, $roles);

        $builder
            ->add('email')
            ->add('roles', ChoiceType::class, [
                'multiple' => true,
                'choices'  => $rolesChoices,
                'expanded' => true,
            ])
            ->add('lastname')
            ->add('firstname')
            ->add('companyId', EntityType::class, [
                'class' => Company::class,
                'query_builder' => function (EntityRepository $er): QueryBuilder {
                    return $er->createQueryBuilder('c')
                        ->orderBy('c.name', 'ASC');
                },
                'choice_label' => function ($company) {
                    return $company->getName() . ' - ID : ' . $company->getId();
                },
                'choice_value' => 'id',
                'required' => false,
                'placeholder' => 'Choose a company',
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