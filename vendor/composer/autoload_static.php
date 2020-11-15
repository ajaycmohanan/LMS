<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit0d6b76cb7d442036ad9ad03202460f6e
{
    public static $prefixLengthsPsr4 = array (
        'P' => 
        array (
            'PHPMailer\\PHPMailer\\' => 20,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'PHPMailer\\PHPMailer\\' => 
        array (
            0 => __DIR__ . '/..' . '/phpmailer/phpmailer/src',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit0d6b76cb7d442036ad9ad03202460f6e::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit0d6b76cb7d442036ad9ad03202460f6e::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
