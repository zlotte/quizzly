<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Answer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class AnswerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('title', TextType::class, [
                    'label' => 'answer',
                    'translation_domain' => 'messages',
                    'constraints' => [
                        new NotBlank(),
                        new Length(['min'=> 1, 'max'=> 255]),
                    ]
                ])
                ->add('correct', CheckboxType::class, [
                    'label' => 'correct',
                    'translation_domain' => 'messages',
                ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Answer::class,
        ]);
    }
}