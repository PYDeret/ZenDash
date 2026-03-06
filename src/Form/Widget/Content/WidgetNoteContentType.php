<?php

declare(strict_types=1);

namespace App\Form\Widget\Content;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @extends AbstractType<array>
 */
class WidgetNoteContentType extends AbstractType
{
    /** {@inheritDoc} */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(child: 'text', type: TextType::class, options: [
                'label' => 'label.note',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(defaults: [
            'data_class' => null,
            'translation_domain' => 'widget',
        ]);
    }
}
