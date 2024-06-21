<?php

namespace App\Form;

use App\Entity\Event;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\NotBlank;

class EditEventFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Le titre ne peut pas être vide.',
                    ]),
                ],
            ])
            ->add('description', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'La description ne peut pas être vide.',
                    ]),
                ],
            ])
            ->add('datetime', null, [
                'widget' => 'single_text',
                'constraints' => [
                    new NotBlank([
                        'message' => 'La date et l\'heure ne peuvent pas être vides.',
                    ]),
                ],
            ])
            ->add('maxParticipants', IntegerType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Le nombre maximum de participants ne peut pas être vide.',
                    ]),
                ],
            ])
            ->add('public')
        ;
    }
}