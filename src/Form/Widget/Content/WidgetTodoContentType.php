<?php

declare(strict_types=1);

namespace App\Form\Widget\Content;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class WidgetTodoContentType extends AbstractWidgetContentType
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
    }
}
