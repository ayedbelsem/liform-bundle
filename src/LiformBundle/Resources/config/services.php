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

use Limenius\Liform\Form\Extension\AddLiformExtension;
use Limenius\Liform\Guesser\ValidatorGuesser;
use Limenius\Liform\Liform;
use Limenius\Liform\Resolver;
use Limenius\Liform\Serializer\Normalizer\FormErrorNormalizer;
use Limenius\Liform\Serializer\Normalizer\InitialValuesNormalizer;
use Symfony\Component\Form\Extension\Core\Type\FormType;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set(AddLiformExtension::class)
        ->public()
        ->tag('form.type_extension', ['extended_type' => FormType::class]);
    $services->alias('liform.add_schema_extension', AddLiformExtension::class)->public();

    // Normalizes FormInterface when using the symfony serializer
    $services->set(FormErrorNormalizer::class)
        ->public()
        ->args([service('translator')])
        ->tag('serializer.normalizer', ['priority' => -10]);
    $services->alias('liform.serializer.form_error_normalizer', FormErrorNormalizer::class)->public();

    $services->set(InitialValuesNormalizer::class)
        ->public()
        ->tag('serializer.normalizer', ['priority' => -10]);
    $services->alias('liform.serializer.initial_values_normalizer', InitialValuesNormalizer::class);

    $services->set(Resolver::class)->public();
    $services->alias('liform.resolver', Resolver::class)->public();

    $services->set(Liform::class)
        ->public()
        ->args([service(Resolver::class)]);
    $services->alias('liform', Liform::class)->public();

    $services->set('liform.guesser.validator', ValidatorGuesser::class)
        ->public()
        ->args([service('validator.mapping.class_metadata_factory')]);
};
