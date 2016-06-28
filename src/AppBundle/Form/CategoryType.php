<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class CategoryType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('text', CKEditorType::class, ['config_name' => 'custom'])
            ->add('enabled')

            // not mapped field
            ->add('order', ChoiceType::class, array(
                'choices'  => array(
                    'In Category' => 'in_category',
                    'Before' => 'before',
                    'After' => 'after',
                ),
                'mapped' => false,
                'label' => 'Order'
            ))

            ->add('parent', EntityType::class, array(
                'class' => 'AppBundle:Category',
                // 'placeholder' => 'ROOT',
                // 'empty_data'  => null,
                'query_builder' => function($er) {
                    return $er->createQueryBuilder('c')
                        ->orderBy('c.root', 'ASC')
                        ->addOrderBy('c.lft', 'ASC');
                },
                'choice_label' => 'indentedTitle',
                'label' => 'Parent',
                // 'required' => false
            ))

            ->add('save', SubmitType::class, array('label' => 'Save'))
            ->add('saveAndExit', SubmitType::class, array('label' => 'Save and exit'))
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Category'
        ));
    }
}
