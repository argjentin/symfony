<?php

namespace App\Form;

use App\Entity\Event;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Positive;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\NotBlank;

class EventFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', null, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Le titre ne peut pas être vide.'
                    ]),
                ],
            ])
            ->add('description', null, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'La description ne peut pas être vide.'
                    ]),
                ],
            ])
            ->add('datetime', DateTimeType::class, [
                'widget' => 'single_text',
                'constraints' => [
                    new NotBlank([
                        'message' => 'La date et l\'heure ne peuvent pas être vides.'
                    ]),
                    new GreaterThan([
                        'value' => 'today',
                        'message' => 'La date doit être dans le futur.',
                    ]),
                ],
            ])
            ->add('maxParticipants', IntegerType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Le nombre de participants ne peut pas être vide.'
                    ]),
                    new Positive([
                        'message' => 'Le nombre de participants doit être un nombre positif.'
                    ]),
                ],
            ])
            ->add('public', CheckboxType::class, [
                'required' => false,
                'label' => 'Événement public ?',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}
