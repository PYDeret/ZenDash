<?php

declare(strict_types=1);

namespace App\Form\Widget;

use App\Entity\Widget;
use App\Enum\Widget\WidgetTypeEnum;
use App\Form\Widget\Content\WidgetNoteContentType;
use App\Form\Widget\Content\WidgetTodoContentType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @extends AbstractType<Widget>
 */
class WidgetFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(child: 'title', type: TextType::class, options: [
                'label' => 'label.title',
            ])
            ->add(child: 'type', type: EnumType::class, options: [
                'label' => 'label.type',
                'class' => WidgetTypeEnum::class,
                'placeholder' => 'label.choose_type',
                'choice_label' => fn (WidgetTypeEnum $choice) => 'widget_type.'.$choice->value,
            ])
        ;

        match ($options['widget_type']) {
            WidgetTypeEnum::NOTE => $builder->add('content', WidgetNoteContentType::class),
            WidgetTypeEnum::TODO => $builder->add('content', WidgetTodoContentType::class),
            default => null,
        };
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(defaults: [
            'data_class' => Widget::class,
            'translation_domain' => 'widget',
            'widget_type' => null,
        ]);

        $resolver->setAllowedTypes(
            option: 'widget_type',
            allowedTypes: ['null', WidgetTypeEnum::class]
        );
    }
}
