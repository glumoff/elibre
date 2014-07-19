<?php

namespace Big\ElibreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ThemeType extends AbstractType {

  public function buildForm(FormBuilderInterface $builder, array $options) {
    $builder->add('id', 'hidden');
  }

  public function setDefaultOptions(OptionsResolverInterface $resolver) {
    $resolver->setDefaults(array(
        'data_class' => 'Big\ElibreBundle\Entity\Theme',
        'cascade_validation' => true,
    ));
  }

  public function getName() {
    return 'theme';
  }

}
