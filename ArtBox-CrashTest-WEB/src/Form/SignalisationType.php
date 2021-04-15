<?php

namespace App\Form;

use App\Entity\Signalisation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SignalisationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('contenuSignal')
            ->add('typeSignal')
            ->add('etatSignal')
            ->add('dateSignal')
            ->add('idPost')
            ->add('idUser')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Signalisation::class,
        ]);
    }
}
