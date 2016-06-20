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

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class PageType extends AbstractType
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

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $page = $event->getData();
            $form = $event->getForm();

            $form
                ->add('parent', EntityType::class, array(
                    'class' => 'AppBundle:Page',
                    'choice_label' => 'title',
                    'label' => 'Parent'
                ))
            ;

            // exist record
            if ($page AND $page->getId() > 0 ) {
                $form
                    ->add('parent', EntityType::class, array(
                        'class' => 'AppBundle:Page',
                        'query_builder' => function (EntityRepository $er) use ($page) {
                            return $er->createQueryBuilder('t')
                                ->where('t.id != :id')
                                ->setParameter('id', $page->getId());
                        },
                        'choice_label' => 'title',
                        'label' => 'Parent'
                    ))
                ;
            }
        });
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
