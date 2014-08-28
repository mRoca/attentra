<?php

namespace Attentra\TimeBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TimeInputType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('datetime', null, ['widget' => 'single_text']) //One input for API, two for html (calendar + time). Options added in the controller => move that ?
            ->add('identifier')
            ->add('type')
            ->add('description');
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'Attentra\TimeBundle\Entity\TimeInput'
            )
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'attentra_timebundle_timeinput';
    }
}
