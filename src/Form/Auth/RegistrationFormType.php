<?php

declare(strict_types=1);

namespace App\Form\Auth;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @extends AbstractType<User>
 */
class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(child: 'email')
            ->add(child: 'nickname', type: TextType::class, options: [
                'label' => 'label.nickname',
            ])
            ->add(child: 'agreeTerms', type: CheckboxType::class, options: [
                'mapped' => false,
                'constraints' => [
                    new IsTrue(message: 'error.must_accept_cgu'),
                ],
            ])
            ->add(child: 'plainPassword', type: PasswordType::class, options: [
                'mapped' => false,
                'label' => 'label.password',
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank(message: 'error.mandatory_password'),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(defaults: [
            'data_class' => User::class,
            'translation_domain' => 'authentication',
        ]);
    }
}
