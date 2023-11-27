<?php

namespace App\Form;

use App\Document\User;
use App\Form\DataMapper\DateOfBirthType;
use DateTime;
use Doctrine\ODM\MongoDB\Types\DateType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Validator\Constraints\Regex;

use Symfony\Component\Form\Extension\Core\Type\IntegerType;

use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;



use Symfony\Component\Form\Extension\Core\Type\RadioType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('gender', ChoiceType::class, [
            'label' => 'Genre',
            'choices' => [
                'Homme' => true,
                'Femme' => false,
            ],
            
            'attr' => [
                'class' => 'form-check-input',
                'name' => 'inlineRadioOptions',
                
            ],
            'expanded' => true
        ])

        ->add('firstname', TextType::class, [
            'attr' => [
                'class' => 'form-control'
            ]
        ])
        ->add('lastname', TextType::class, [
            'attr' => [
                'class' => 'form-control'
            ]
        ])
        ->add('email', TextType::class, [
            'attr' => [
                'class' => 'form-control'
            ]
        ])

        ->add('city', TextType::class, [
            'label' => 'Ville',
            'attr' => [
                'class' => 'form-control'
            ]
        ])
        ->add('address', TextType::class, [
            'label' => 'Votre adresse',
            'attr' => [
                'class' => 'form-control'
            ]
        ])
        ->add('phone', TextType::class, [
            'label' => 'Téléphone',
            'attr' => [
                'class' => 'form-control'
            ]
        ])
        ->add('postalCode', IntegerType::class, [
            'label' => 'code postale',
            'attr' => [
                'class' => 'form-control'
            ]
        ])

        ->add('dateOfBirth', BirthdayType::class, [
            'widget' => 'single_text',
            'attr' => [
                'class' => 'form-control'
            ],
            
            'format' => 'yyyy-MM-dd',
        ])

        ->add('valid', SubmitType::class, [
            'label' => 'Validez',
            'attr' => [
                'class' => 'btn btn-success form-control'
            ]
        ])
        ->add('annuler', ButtonType::class,[
            
            'label' => 'Annuler',
            'attr' => [
                'class' => 'btn btn-danger form-control'
            ]
            ]);
    }
    
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}