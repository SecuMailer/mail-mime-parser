<?php

namespace ZBateson\MailMimeParser;

use GuzzleHttp\Psr7;
use PHPUnit\Framework\TestCase;

/**
 * Description of MailMimeParserTest
 *
 * @group MailMimeParser
 * @group Base
 * @covers ZBateson\MailMimeParser\MailMimeParser
 * @author Zaahid Bateson
 */
class MailMimeParserTest extends TestCase
{
    // @phpstan-ignore-next-line
    private $mockDi;

    protected function setUp() : void
    {
        $this->mockDi = $this->getMockBuilder(\ZBateson\MailMimeParser\Container::class)
            ->disableOriginalConstructor()
            ->setMethods(['offsetGet', 'offsetExists'])
            ->getMock();
    }

    protected function tearDown() : void
    {
        MailMimeParser::setDependencyContainer(null);
    }

    public function testConstructMailMimeParser() : void
    {
        MailMimeParser::setDependencyContainer($this->mockDi);
        $mmp = new MailMimeParser();
        $this->assertNotNull($mmp);
    }

    public function testParseFromHandle() : void
    {
        $handle = \fopen('php://memory', 'r+');
        \fwrite($handle, 'This is a test');
        \rewind($handle);

        $mockParser = $this->getMockBuilder(\ZBateson\MailMimeParser\Parser\MessageParser::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->mockDi
            ->expects($this->once())
            ->method('offsetGet')
            ->with(\ZBateson\MailMimeParser\Parser\MessageParser::class)
            ->willReturn($mockParser);
        $mockParser
            ->expects($this->once())
            ->method('parse')
            ->willReturn('test');

        MailMimeParser::setDependencyContainer($this->mockDi);
        $mmp = new MailMimeParser();

        $ret = $mmp->parse($handle, true);
        $this->assertEquals('test', $ret);
    }

    public function testParseFromStream() : void
    {
        $handle = \fopen('php://memory', 'r+');
        \fwrite($handle, 'This is a test');
        \rewind($handle);

        $mockParser = $this->getMockBuilder(\ZBateson\MailMimeParser\Parser\MessageParser::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->mockDi
            ->expects($this->once())
            ->method('offsetGet')
            ->with(\ZBateson\MailMimeParser\Parser\MessageParser::class)
            ->willReturn($mockParser);
        $mockParser
            ->expects($this->once())
            ->method('parse')
            ->willReturn('test');

        MailMimeParser::setDependencyContainer($this->mockDi);
        $mmp = new MailMimeParser();

        $ret = $mmp->parse(Psr7\Utils::streamFor($handle), true);
        $this->assertEquals('test', $ret);
    }

    public function testParseFromString() : void
    {
        $mockParser = $this->getMockBuilder(\ZBateson\MailMimeParser\Parser\MessageParser::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->mockDi
            ->expects($this->once())
            ->method('offsetGet')
            ->with(\ZBateson\MailMimeParser\Parser\MessageParser::class)
            ->willReturn($mockParser);
        $mockParser
            ->expects($this->once())
            ->method('parse')
            ->willReturn('test');

        MailMimeParser::setDependencyContainer($this->mockDi);
        $mmp = new MailMimeParser();

        $ret = $mmp->parse('This is a test', false);
        $this->assertEquals('test', $ret);
    }
}
