<?php

return [

    /*
    |--------------------------------------------------------------------------
    | QR Code Default Driver
    |--------------------------------------------------------------------------
    |
    | Setting the default driver to 'gd' to resolve the "imagick extension" error.
    |
    */

    'default' => 'gd', // <-- FORCES GD USAGE

    /*
    |--------------------------------------------------------------------------
    | QR Code Backends
    |--------------------------------------------------------------------------
    |
    | These are the available backends.
    |
    */
    'backends' => [
        'gd' => [
            'driver' => 'gd',
        ],
        'imagick' => [
            'driver' => 'imagick',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | QR Code Error Correction Levels
    |--------------------------------------------------------------------------
    |
    | These are the error correction levels used for the QR Code.
    |
    */
    'error_correction_levels' => [
        'L' => 'l',
        'M' => 'm',
        'Q' => 'q',
        'H' => 'h',
    ],
];
