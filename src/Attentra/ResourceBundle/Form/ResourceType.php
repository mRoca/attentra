<?php

namespace Attentra\ResourceBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ResourceType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('identifier')
            ->add('group', null, ['required' => false, 'attr' => ['class' => 'chosen']])
            ->add('description');
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'Attentra\ResourceBundle\Entity\Resource'
            )
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'attentra_resourcebundle_resource';
    }
}
