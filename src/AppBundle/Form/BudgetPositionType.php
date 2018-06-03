<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BudgetPositionType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
//        var_dump($options);
        $builder->add('title')
                ->add('description')
                ->add('price')
                ->add('date', DateType::class, ['html5' => true,
                    'widget' => 'choice', 'years' => [$options['year']], 'months' => [$options['monthId']]])
                ->add('category', null, ['choice_label' => 'name']);
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\BudgetPosition',
            'year' => null,
            'monthId' => null
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
