<?php

namespace AppBundle\Form;

use AppBundle\Entity\Category;
use AppBundle\Repository\CategoryRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BudgetPositionType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (isset($options['categoryObj'])) {
            $obj = $options['categoryObj'];
        }

        //TODO: Try to change category to choiceType not EntityType and make choices from entityType
        $builder->add('title', TextType::class, ['label' => 'Tytuł'])
                ->add('description', TextType::class, ['label' => 'Opis', 'required' => false])
                ->add('price', NumberType::class, ['label' => 'Kwota', 'scale' => 2])
                ->add('date', DateType::class, ['label' => 'Data', 'html5' => true,
                    'widget' => 'choice', 'years' => [$options['year']], 'months' => [$options['month']]])
                ->add('category',  EntityType::class,
                    ['class' =>Category::class,
                        'label' => 'Kategoria', 'choice_label' => 'name', 'placeholder' => '--Wybierz kategorię--']);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\BudgetPosition',
            'year' => null,
            'month' => null,
            'categoryObj' => null
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_budgetposition';
    }


}
