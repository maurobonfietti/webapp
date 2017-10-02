<?php

namespace Tests\AppBundle;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class BaseTest extends WebTestCase
{
    protected static $id;

    protected static $bearer = '';

    protected function getAuthToken()
    {
        return self::$bearer;
    }
}
