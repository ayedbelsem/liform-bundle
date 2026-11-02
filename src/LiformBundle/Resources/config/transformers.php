<?php

/*
 * This file is part of the Limenius\LiformBundle package.
 *
 * (c) Limenius <https://github.com/Limenius/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Limenius\Liform\Resolver;
use Limenius\Liform\Transformer\AbstractTransformer;
use Limenius\Liform\Transformer\ArrayTransformer;
use Limenius\Liform\Transformer\BooleanTransformer;
use Limenius\Liform\Transformer\ChoiceTransformer;
use Limenius\Liform\Transformer\CompoundTransformer;
use Limenius\Liform\Transformer\IntegerTransformer;
use Limenius\Liform\Transformer\NumberTransformer;
use Limenius\Liform\Transformer\StringTransformer;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set(AbstractTransformer::class)
        ->abstract()
        ->args([service('translator'), service('liform.guesser.validator')]);

    $services->set('liform.transformer.integer', IntegerTransformer::class)
        ->parent(AbstractTransformer::class)
        ->tag('liform.transformer', ['form_type' => 'integer']);

    $services->set('liform.transformer.array', ArrayTransformer::class)
        ->args([service('translator'), service(Resolver::class), service('liform.guesser.validator')])
        ->tag('liform.transformer', ['form_type' => 'collection']);

    $services->set('liform.transformer.compound', CompoundTransformer::class)
        ->args([service('translator'), service(Resolver::class), service('liform.guesser.validator')])
        ->tag('liform.transformer', ['form_type' => 'compound']);

    $services->set('liform.transformer.choice', ChoiceTransformer::class)
        ->parent(AbstractTransformer::class)
        ->tag('liform.transformer', ['form_type' => 'choice']);

    $string = $services->set('liform.transformer.string', StringTransformer::class)
        ->parent(AbstractTransformer::class)
        ->tag('liform.transformer', ['form_type' => 'text']);
    foreach (['url', 'search', 'money', 'password', 'textarea', 'time', 'percent', 'email'] as $type) {
        $string->tag('liform.transformer', ['form_type' => $type, 'widget' => $type]);
    }

    $services->set('liform.transformer.number', NumberTransformer::class)
        ->parent(AbstractTransformer::class)
        ->tag('liform.transformer', ['form_type' => 'number']);

    $services->set('liform.transformer.boolean', BooleanTransformer::class)
        ->parent(AbstractTransformer::class)
        ->tag('liform.transformer', ['form_type' => 'checkbox', 'widget' => 'checkbox']);
};
