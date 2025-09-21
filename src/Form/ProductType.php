<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Client;
use App\Entity\Product;
use Faker\Core\Number;
use phpDocumentor\Reflection\Types\Float_;
use phpDocumentor\Reflection\Types\Integer;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PercentType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;
use App\Repository\CategoryRepository;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom',
            ])
            ->add('description', TextType::class, [
                'label' => 'Description',
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'label' => 'Categorie',
                'choice_label' => 'name',
                'query_builder' => function (CategoryRepository $er) use ($options) {
                    // Vérifier si data existe et a une méthode getCompanyId
                    $company_id = null;
                    if (isset($options['data']) && is_object($options['data']) && method_exists($options['data'], 'getCompanyId')) {
                        $company_id = $options['data']->getCompanyId();
                    }
                    
                    // Si pas de company_id, récupérer toutes les catégories
                    if ($company_id === null) {
                        return $er->createQueryBuilder('c')
                            ->orderBy('c.name', 'ASC');
                    }

                    return $er->createQueryBuilder('c')
                        ->where('c.company_id = :company_id')
                        ->setParameter('company_id', $company_id)
                        ->orderBy('c.name', 'ASC');
                },
            ])

            ->add('price', NumberType::class, [
                'label' => 'Prix',
            ])
            ->add('tva', NumberType::class, [
                'label' => 'TVA',
            ])
            ->add('quantite', HiddenType::class, [
                'label' => false
            ])
            ->add('prix_totale', HiddenType::class, [
                'label' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
