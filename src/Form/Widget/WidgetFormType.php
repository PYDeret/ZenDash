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
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
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

        $this->addContentField($builder, $options['widget_type']);
        $builder->addEventListener(
            eventName: FormEvents::PRE_SUBMIT,
            listener: function (FormEvent $event): void {
                $this->addContentField($event->getForm(), WidgetTypeEnum::tryFrom(value: $event->getData()['type'] ?? ''));
            }
        );
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

    /** @param FormInterface<Widget>|FormBuilderInterface<Widget|null> $form */
    private function addContentField(FormInterface|FormBuilderInterface $form, ?WidgetTypeEnum $type): void
    {
        match ($type) {
            WidgetTypeEnum::NOTE => $form->add('content', WidgetNoteContentType::class),
            WidgetTypeEnum::TODO => $form->add('content', WidgetTodoContentType::class),
            default => null,
        };
    }
}
