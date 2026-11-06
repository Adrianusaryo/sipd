<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    title: 'SIPD API Documentation',
    description: 'Dokumentasi API untuk aplikasi SIPD',
    contact: new OA\Contact(email: 'admin@sipd_new.test')
)]
#[OA\Server(
    url: 'http://be-sipd.test/api/',
    description: 'Local Server'
)]
#[OA\Tag(
    name: 'Authentication',
    description: false
)]

#[OA\Tag(
    name: 'User',
    description: false
)]

#[OA\Tag(
    name: 'Admin',
    description: false
)]

abstract class Controller
{
    //
}
