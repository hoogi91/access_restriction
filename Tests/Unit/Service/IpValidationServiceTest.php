<?php

namespace Hoogi91\AccessRestriction\Tests\Unit\Service;

use Hoogi91\AccessRestriction\Service\IpValidationService;
use Nimut\TestingFramework\TestCase\UnitTestCase;

/**
 * Class IpValidationServiceTest
 * @package Hoogi91\AccessRestriction\Tests\Unit\Service
 */
class IpValidationServiceTest extends UnitTestCase
{

    /**
     * @var IpValidationService
     */
    protected $ipValidationService;

    /**
     * @var IpValidationService
     */
    protected $ipValidationServiceWithRemoteAddress;

    protected function setUp()
    {
        parent::setUp();

        // set default remove address
        $_SERVER['REMOTE_ADDR'] = '192.168.178.15';

        // define ip validation services with default remote
        $this->ipValidationService = new IpValidationService();
        // and manual remote ip
        $this->ipValidationServiceWithRemoteAddress = new IpValidationService('192.168.0.15');
    }

    /**
     * @test
     */
    public function testEquals()
    {
        // check that remote IP is correctly set
        $this->assertTrue($this->ipValidationService->equals('192.168.178.15'));
        $this->assertTrue($this->ipValidationServiceWithRemoteAddress->equals('192.168.0.15'));

        // check that method returns false if ip to compare isn't same
        $this->assertFalse($this->ipValidationService->equals('192.168.178.1'));
        $this->assertFalse($this->ipValidationServiceWithRemoteAddress->equals('192.168.0.1'));

        // assert equals ignores default remote IP if compare IP is given
        $this->assertTrue($this->ipValidationService->equals('127.0.0.1', '127.0.0.1'));
        $this->assertFalse($this->ipValidationService->equals('127.0.0.1', '127.0.0.2'));
    }

    /**
     * @test
     */
    public function testValidationOfSubnet()
    {
        // check if remote IP os correctly set and can be found in a range
        $this->assertTrue($this->ipValidationService->validate('192.168.178.12/23'));
        $this->assertTrue($this->ipValidationServiceWithRemoteAddress->validate('192.168.0.12/23'));

        // check if edges of the range are correctly calculated and validated
        $this->assertTrue($this->ipValidationService->validate('192.168.178.1/28')); // upper broadcast should be true
        $this->assertTrue($this->ipValidationService->validate('192.168.178.1/27'));
        $this->assertTrue($this->ipValidationService->validate('192.168.178.15/31')); // broadcast and address are equal

        // check if a given compare ip is correctly used
        $this->assertFalse($this->ipValidationService->validate(
            '192.168.178.12/23',
            '192.168.177.255' // lower broadcast should be false
        ));
        $this->assertTrue($this->ipValidationService->validate(
            '192.168.178.12/23',
            '192.168.178.1' // hosts min should be true
        ));
        $this->assertTrue($this->ipValidationService->validate(
            '192.168.178.12/23',
            '192.168.179.254' // hosts max should be true
        ));
        $this->assertTrue($this->ipValidationService->validate(
            '192.168.178.12/23',
            '192.168.179.255' // upper broadcast should be true
        ));
    }

    /**
     * @test
     */
    public function testValidationOfRange()
    {
        // check if remote IP os correctly set and can be found in a range
        $this->assertTrue($this->ipValidationService->validate('192.168.178.12-192.168.178.15'));
        $this->assertTrue($this->ipValidationServiceWithRemoteAddress->validate('192.168.0.12-192.168.0.15'));

        // check if edges of the range are correctly calculated and validated
        $this->assertFalse($this->ipValidationService->validate('192.168.178.12-192.168.178.14'));
        $this->assertFalse($this->ipValidationService->validate('192.168.178.16-192.168.178.18'));
        $this->assertFalse($this->ipValidationServiceWithRemoteAddress->validate('192.168.0.12-192.168.0.14'));
        $this->assertFalse($this->ipValidationServiceWithRemoteAddress->validate('192.168.0.16-192.168.0.18'));

        // check if a given compare ip is correctly used
        $this->assertTrue($this->ipValidationService->validate('192.168.178.12-192.168.178.15', '192.168.178.12'));
        $this->assertFalse($this->ipValidationService->validate('192.168.178.12-192.168.178.15', '192.168.178.11'));
    }

    /**
     * @depends testEquals
     * @test
     */
    public function testValidationOfSingleAddress()
    {
        // check that remote IP is correctly set
        $this->assertTrue($this->ipValidationService->validate('192.168.178.15'));
        $this->assertTrue($this->ipValidationServiceWithRemoteAddress->validate('192.168.0.15'));

        // check that method returns false if ip to compare isn't same
        $this->assertFalse($this->ipValidationService->validate('192.168.178.1'));
        $this->assertFalse($this->ipValidationServiceWithRemoteAddress->validate('192.168.0.1'));

        // assert equals ignores default remote IP if compare IP is given
        $this->assertTrue($this->ipValidationService->validate('127.0.0.1', '127.0.0.1'));
        $this->assertFalse($this->ipValidationService->validate('127.0.0.1', '127.0.0.2'));
    }

    /**
     * @depends      testValidationOfSubnet
     * @depends      testValidationOfRange
     * @depends      testValidationOfSingleAddress
     * @dataProvider addressLists
     * @dataProvider addressArray
     * @test
     */
    public function testValidationOfList($list)
    {
        $this->assertTrue($this->ipValidationService->findInList($list));
        $this->assertFalse($this->ipValidationService->findInList($list, '192.168.178.16'));
    }

    public function addressLists()
    {
        return [
            // valid single ip first
            ['192.168.178.15' . PHP_EOL . '192.168.178.12-192.168.178.15' . PHP_EOL . '192.168.178.1/28'],
            // valid ip range first
            ['192.168.178.12-192.168.178.15' . PHP_EOL . '192.168.178.1/28' . PHP_EOL . '192.168.178.15'],
            // valid subnet range first
            ['192.168.178.1/28' . PHP_EOL . '192.168.178.12-192.168.178.15' . PHP_EOL . '192.168.178.15'],
        ];
    }

    public function addressArray()
    {
        return [
            // valid single ip first
            [['192.168.178.15', '192.168.178.12-192.168.178.15', '192.168.178.1/28']],
            // valid ip range first
            [['192.168.178.12-192.168.178.15', '192.168.178.1/28', '192.168.178.15']],
            // valid subnet range first
            [['192.168.178.1/28', '192.168.178.12-192.168.178.15', '192.168.178.15']],
        ];
    }
}