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
            ->add('datetime', 'datetime', ['date_widget' => 'single_text', 'time_widget' => 'single_text', 'date_format' => 'yyyy-MM-dd', 'with_seconds' => true, 'required' => true, 'attr' => ['class' => 'datepicker-container']])
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
