<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class PageType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('parent', EntityType::class, array(
                'class' => 'AppBundle:Page',
                'choice_label' => 'title',
                'label' => 'Parent'
            ))
            ->add('title')
            ->add('text', CKEditorType::class, ['config_name' => 'custom'])
            ->add('enabled')

            ->add('fimage', FileType::class, array(
                'required'  => false,
                'label' => 'Image (Image file)'
                ))
            ->add('position')

            ->add('slug')
            ->add('metaT', TextType::class, array('label' => 'Meta Title', 'required'  => false))
            ->add('metaD', TextType::class, array('label' => 'Meta Desription', 'required'  => false))
            ->add('metaK', TextType::class, array('label' => 'Meta Keywords', 'required'  => false))

            ->add('save', SubmitType::class, array('label' => 'Save'))
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Page'
        ));
    }
}
