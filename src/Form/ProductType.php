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
use App\Repository\ProductRepository;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder
            ->add('existingProduct', EntityType::class, [
                'class' => Product::class,
                'label' => 'Produit existant',
                'choice_label' => function (Product $product) {
                    return $product->getName() . ' - ' . $product->getCategory()->getName() . ' (' . $product->getPrice() . '€)';
                },
                'query_builder' => function (ProductRepository $er) use ($options) {
                    $company_id = null;
                    
                    if (isset($options['company_id'])) {
                        $company_id = $options['company_id'];
                    } elseif (isset($options['data']) && is_object($options['data']) && method_exists($options['data'], 'getCompanyId')) {
                        $company_id = $options['data']->getCompanyId();
                    }
                    
                    if ($company_id === null) {
                        return $er->createQueryBuilder('p')
                            ->orderBy('p.name', 'ASC');
                    }

                    return $er->createQueryBuilder('p')
                        ->where('p.company_id = :company_id')
                        ->setParameter('company_id', $company_id)
                        ->orderBy('p.name', 'ASC');
                },
                'placeholder' => '-- Sélectionner un produit existant --',
                'required' => false,
                'mapped' => false, // Ce champ n'est pas mappé à l'entité
            ])
            ->add('name', TextType::class, [
                'label' => 'Nom',
            ])
            ->add('description', TextType::class, [
                'label' => 'Description',
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'label' => 'Catégorie',
                'choice_label' => 'name',
                'query_builder' => function (CategoryRepository $er) use ($options) {
                    // Récupérer le company_id depuis les options du formulaire parent
                    $company_id = null;
                    
                    // Vérifier si le company_id est passé depuis le formulaire parent
                    if (isset($options['company_id'])) {
                        $company_id = $options['company_id'];
                    }
                    // Sinon, vérifier si data existe et a une méthode getCompanyId
                    elseif (isset($options['data']) && is_object($options['data']) && method_exists($options['data'], 'getCompanyId')) {
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
                'label' => 'Prix (€)',
            ])
            ->add('tva', NumberType::class, [
                'label' => 'TVA (%)',
            ])
            ->add('quantite', NumberType::class, [
                'label' => 'Quantité',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
            'company_id' => null,
        ]);
    }
}
