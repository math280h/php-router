<?php declare(strict_types=1);

use Math280h\PhpRouter\Request;
use PHPUnit\Framework\TestCase;

final class RequestTest extends TestCase
{
    # It's important this test runs before `testCanBeCreatedWithValidData`
    # Since that test sets some values that interfere with what we are testing
    public function testCannotBeCreatedFromInvalidData(): void
    {

        $this->expectException(InvalidArgumentException::class);

        $this->assertInstanceOf(
            Request::class,
            new Request()
        );
    }

    public function testCanBeCreatedWithValidData(): void
    {
        # These two would normally be set by sending an HTTP Request
        # In this case we are just testing the class itself
        $_SERVER['REQUEST_URI'] = "/test";
        $_SERVER['REQUEST_METHOD'] = "GET";

        $this->assertInstanceOf(
            Request::class,
            new Request()
        );
    }

    public function testCanAttachErrors(): void
    {
        $request = new Request();
        $request->withErrors("Something went wrong", 2);

        $this->assertInstanceOf(
            Error::class,
            $_SESSION["errors"][0]
        );
    }
}
