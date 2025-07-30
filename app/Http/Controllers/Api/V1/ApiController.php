<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use OpenApi\Attributes as OA;

#[OA\Info(version: '1.0', title: 'Patoughi Driver API')]
abstract class ApiController extends Controller {}
