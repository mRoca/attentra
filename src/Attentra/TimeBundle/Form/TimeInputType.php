<?php

namespace Attentra\TimeBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints;

class TimeInputType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            //One input for API, two for html (calendar + time) added in the controller => move in another place ?
            ->add('datetime', 'datetime', array(
                'widget'      => 'single_text',
                'constraints' => array(
                    new Constraints\NotBlank(),
                    new Constraints\DateTime(),
                )
            ))
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
