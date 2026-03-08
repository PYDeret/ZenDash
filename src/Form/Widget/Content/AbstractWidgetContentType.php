<?php

declare(strict_types=1);

namespace App\Form\Widget\Content;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @extends AbstractType<array>
 */
abstract class AbstractWidgetContentType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(defaults: [
            'data_class' => null,
            'translation_domain' => 'widget',
        ]);
    }
}
