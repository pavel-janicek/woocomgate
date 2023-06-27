<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitb0b247846763eaaa5aba898e61459d23
{
    public static $files = array (
        '241d2b5b9c1e680c0770b006b0271156' => __DIR__ . '/..' . '/yahnis-elsts/plugin-update-checker/load-v4p9.php',
    );

    public static $classMap = array (
        'ondrs\\Comgate\\AgmoPaymentsSimpleDatabase' => __DIR__ . '/..' . '/ondrs/comgate/src/ondrs/Comgate/AgmoPaymentsSimpleDatabase.php',
        'ondrs\\Comgate\\AgmoPaymentsSimpleProtocol' => __DIR__ . '/..' . '/ondrs/comgate/src/ondrs/Comgate/AgmoPaymentsSimpleProtocol.php',
        'ondrs\\Comgate\\Exception' => __DIR__ . '/..' . '/ondrs/comgate/src/ondrs/Comgate/exceptions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->classMap = ComposerStaticInitb0b247846763eaaa5aba898e61459d23::$classMap;

        }, null, ClassLoader::class);
    }
}
