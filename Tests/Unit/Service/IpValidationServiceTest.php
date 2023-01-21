<?php

namespace Hoogi91\AccessRestriction\Tests\Unit\Service;

use Hoogi91\AccessRestriction\Service\IpValidationService;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class IpValidationServiceTest extends UnitTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // set default remove address
        $_SERVER['REMOTE_ADDR'] = '192.168.178.15';
    }

    public function testEquals(): void
    {
        $ipValidationService = new IpValidationService();
        $ipValidationServiceWithRemoteAddress = new IpValidationService('192.168.0.15');

        // check that remote IP is correctly set
        $this->assertTrue($ipValidationService->equals('192.168.178.15'));
        $this->assertTrue($ipValidationServiceWithRemoteAddress->equals('192.168.0.15'));

        // check that method returns false if ip to compare isn't same
        $this->assertFalse($ipValidationService->equals('192.168.178.1'));
        $this->assertFalse($ipValidationServiceWithRemoteAddress->equals('192.168.0.1'));

        // assert equals ignores default remote IP if compare IP is given
        $this->assertTrue($ipValidationService->equals('127.0.0.1', '127.0.0.1'));
        $this->assertFalse($ipValidationService->equals('127.0.0.1', '127.0.0.2'));
    }

    public function testValidationOfSubnet(): void
    {
        $ipValidationService = new IpValidationService();
        $ipValidationServiceWithRemoteAddress = new IpValidationService('192.168.0.15');

        // check if remote IP os correctly set and can be found in a range
        $this->assertTrue($ipValidationService->validate('192.168.178.12/23'));
        $this->assertTrue($ipValidationServiceWithRemoteAddress->validate('192.168.0.12/23'));

        // check if edges of the range are correctly calculated and validated
        $this->assertTrue($ipValidationService->validate('192.168.178.1/28')); // upper broadcast should be true
        $this->assertTrue($ipValidationService->validate('192.168.178.1/27'));
        $this->assertTrue($ipValidationService->validate('192.168.178.15/31')); // broadcast and address are equal

        // check if a given compare ip is correctly used
        $this->assertFalse($ipValidationService->validate(
            '192.168.178.12/23',
            '192.168.177.255' // lower broadcast should be false
        ));
        $this->assertTrue($ipValidationService->validate(
            '192.168.178.12/23',
            '192.168.178.1' // hosts min should be true
        ));
        $this->assertTrue($ipValidationService->validate(
            '192.168.178.12/23',
            '192.168.179.254' // hosts max should be true
        ));
        $this->assertTrue($ipValidationService->validate(
            '192.168.178.12/23',
            '192.168.179.255' // upper broadcast should be true
        ));
    }

    public function testValidationOfRange(): void
    {
        $ipValidationService = new IpValidationService();
        $ipValidationServiceWithRemoteAddress = new IpValidationService('192.168.0.15');

        // check if remote IP os correctly set and can be found in a range
        $this->assertTrue($ipValidationService->validate('192.168.178.12-192.168.178.15'));
        $this->assertTrue($ipValidationServiceWithRemoteAddress->validate('192.168.0.12-192.168.0.15'));

        // check if edges of the range are correctly calculated and validated
        $this->assertFalse($ipValidationService->validate('192.168.178.12-192.168.178.14'));
        $this->assertFalse($ipValidationService->validate('192.168.178.16-192.168.178.18'));
        $this->assertFalse($ipValidationServiceWithRemoteAddress->validate('192.168.0.12-192.168.0.14'));
        $this->assertFalse($ipValidationServiceWithRemoteAddress->validate('192.168.0.16-192.168.0.18'));

        // check if a given compare ip is correctly used
        $this->assertTrue($ipValidationService->validate('192.168.178.12-192.168.178.15', '192.168.178.12'));
        $this->assertFalse($ipValidationService->validate('192.168.178.12-192.168.178.15', '192.168.178.11'));
    }

    /**
     * @depends testEquals
     */
    public function testValidationOfSingleAddress(): void
    {
        $ipValidationService = new IpValidationService();
        $ipValidationServiceWithRemoteAddress = new IpValidationService('192.168.0.15');

        // check that remote IP is correctly set
        $this->assertTrue($ipValidationService->validate('192.168.178.15'));
        $this->assertTrue($ipValidationServiceWithRemoteAddress->validate('192.168.0.15'));

        // check that method returns false if ip to compare isn't same
        $this->assertFalse($ipValidationService->validate('192.168.178.1'));
        $this->assertFalse($ipValidationServiceWithRemoteAddress->validate('192.168.0.1'));

        // assert equals ignores default remote IP if compare IP is given
        $this->assertTrue($ipValidationService->validate('127.0.0.1', '127.0.0.1'));
        $this->assertFalse($ipValidationService->validate('127.0.0.1', '127.0.0.2'));
    }

    /**
     * @depends      testValidationOfSubnet
     * @depends      testValidationOfRange
     * @depends      testValidationOfSingleAddress
     * @dataProvider addressLists
     * @dataProvider addressArray
     */
    public function testValidationOfList($list): void
    {
        $ipValidationService = new IpValidationService();
        $this->assertTrue($ipValidationService->findInList($list));
        $this->assertFalse($ipValidationService->findInList($list, '192.168.178.16'));
    }

    public function addressLists(): array
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

    public function addressArray(): array
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
