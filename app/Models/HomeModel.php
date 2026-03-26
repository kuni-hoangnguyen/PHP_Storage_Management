<?php

declare(strict_types=1);

namespace App\Models;

final class HomeModel
{
    public function getWelcomeText(): string
    {
        return 'This is a sample model for your project.';
    }
}