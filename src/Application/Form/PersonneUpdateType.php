<?php

namespace App\Application\Form;

use App\Application\DTO\PersonneCreateDTO;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Formulaire pour modifier les données d'une personne.
 *
 * @see \App\Application\Controller\PersonneController::updatePerson()
 *
 * @author Vlad Riabchenko <vriabchenko@webnet.fr>
 */
class PersonneUpdateType extends AbstractType
{
    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Email',
            ])
            ->add('nom', TextType::class, [
                'label' => 'Nom',
            ])
        ;
    }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        return [
            'data_class' => PersonneCreateDTO::class,
        ];
    }
}
