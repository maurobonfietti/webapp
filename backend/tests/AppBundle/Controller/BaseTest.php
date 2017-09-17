<?php

namespace Tests\AppBundle;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class BaseTest extends WebTestCase
{
    protected static $id;

    protected function getAuthToken()
    {
        return 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOjEzLCJlbWFpbCI6Im1AYi5jb20uYXIiLCJuYW1lIjoiTWF1Iiwic3VybmFtZSI6IkIiLCJpYXQiOjE1MDU2ODM4MzQsImV4cCI6MTUwNjI4ODYzNH0.-l0r61i2pyC8u-EdiKSHJ14MkVOeq2Qo2t5kbXmBEZo';
    }
}
