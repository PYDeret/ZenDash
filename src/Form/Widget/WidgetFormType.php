<?php

declare(strict_types=1);

namespace App\Form\Widget;

use App\Entity\Widget;
use App\Enum\Widget\WidgetTypeEnum;
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
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(defaults: [
            'data_class' => Widget::class,
            'translation_domain' => 'widget',
        ]);
    }
}
