<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[OA\Info(
    title: "CineHall API",
    version: "1.0.0",
    description: "API documentation for CineHall Cinema Management System",
    contact: new OA\Contact(email: "support@cinehall.com")
)]
#[OA\Server(
    url: "http://localhost:8000/api/v1",
    description: "CineHall API Server V1"
)]
#[OA\SecurityScheme(
    securityScheme: "bearerAuth",
    type: "http",
    scheme: "bearer",
    bearerFormat: "JWT",
    description: "Enter JWT token to access protected endpoints"
)]
abstract class Controller
{
    //
}
