<?php

declare(strict_types=1);

namespace App\Form\Widget\Content;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @extends AbstractType<array>
 */
class WidgetTodoContentType extends AbstractType
{
    /** {@inheritDoc} */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(child: 'task', type: TextType::class, options: [
                'label' => 'label.reminder',
                'constraints' => [new NotBlank(message: 'widget.task_text')],
            ])
        ;

        $builder->addEventListener(
            eventName: FormEvents::POST_SUBMIT,
            listener: function (FormEvent $event) {
                $data = $event->getData();
                if (is_array($data) && !isset($data['done'])) {
                    $data['done'] = false;
                    $event->setData(data: $data);
                }
            }
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(defaults: [
            'data_class' => null,
            'translation_domain' => 'widget',
        ]);
    }
}
