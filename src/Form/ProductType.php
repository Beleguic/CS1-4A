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
                    $company_id = $options['data']->getCompanyId() ?? null;
                    // Récupérer le company_id depuis la requête ou d'où vous le tenez
                    // Par exemple, si vous l'avez dans votre contrôleur, passez-le ici en option

                    return $er->createQueryBuilder('c')
                        ->where('c.company_id = :company_id')
                        ->setParameter('company_id', $company_id)
                        ->orderBy('c.name', 'ASC'); // Vous pouvez ajuster l'ordre de tri ici
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
