<?php
namespace MauticPlugin\MauticVocativeBundle\Tests;

class FOMTestWithMockery extends \PHPUnit_Framework_TestCase
{
    protected function tearDown()
    {
        \Mockery::close();
    }

    protected function mockery($className)
    {
        return \Mockery::mock($className);
    }
}