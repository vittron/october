<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit3ee363e61af949a2fa58f4e7a463b362
{
    public static $prefixLengthsPsr4 = array (
        'K' => 
        array (
            'Kibo\\Phast\\' => 11,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Kibo\\Phast\\' => 
        array (
            0 => __DIR__ . '/..' . '/kiboit/phast/src',
        ),
    );

    public static $prefixesPsr0 = array (
        'J' => 
        array (
            'JSMin\\' => 
            array (
                0 => __DIR__ . '/..' . '/mrclay/jsmin-php/src',
            ),
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit3ee363e61af949a2fa58f4e7a463b362::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit3ee363e61af949a2fa58f4e7a463b362::$prefixDirsPsr4;
            $loader->prefixesPsr0 = ComposerStaticInit3ee363e61af949a2fa58f4e7a463b362::$prefixesPsr0;

        }, null, ClassLoader::class);
    }
}
