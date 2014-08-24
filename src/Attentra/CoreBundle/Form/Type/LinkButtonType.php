<?php

namespace Attentra\CoreBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ButtonTypeInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class LinkButtonType extends AbstractType implements ButtonTypeInterface
{
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'link' => null,
                'label' => 'Go back to the list',
                'icon' => 'list-alt'
            )
        );
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['link'] = $options['link'];
        $view->vars['icon'] = $options['icon'];
    }

    public function getParent()
    {
        return 'button';
    }

    public function getName()
    {
        return 'linkButton';
    }
}
