<?php declare(strict_types=1);

use Math280h\PhpRouter\Route;
use PHPUnit\Framework\TestCase;

final class RouteTest extends TestCase
{

    public function testCannotBeCreatedFromInvalidData(): void
    {
        $this->expectException(ArgumentCountError::class);

        $this->assertInstanceOf(
            Route::class,
            new Route() /* @phpstan-ignore-line */
        );
    }

    public function testCanBeCreatedWithValidData(): void
    {
        $this->assertInstanceOf(
            Route::class,
            new Route(
                "home/test",
                "GET",
                function (){return null;},
                []
            )
        );
    }
}
