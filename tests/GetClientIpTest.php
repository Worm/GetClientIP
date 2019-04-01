<?php

class GetClientIpTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var GetClientIp
     */
    protected $getClientIp;

    public function testClassExists()
    {
        $this->assertTrue(class_exists('GetClientIp'));
    }

    public function setUp()
    {
        $this->getClientIp = new GetClientIp();
    }

    protected function tearDown()
    {
        $this->getClientIp = null;
    }

    /**
     * @covers GetClientIp::getScriptVersion
     * @covers GetClientIp::setServerHeaders
     * @covers GetClientIp::getServerHeaders
     */
    public function testBasicMethods()
    {
        $this->assertNotEmpty($this->getClientIp->getScriptVersion());

        $this->getClientIp->setServerHeaders(['QUERY_STRING'             => 'h=examplequery',
                                                 'REQUEST_METHOD'        => 'GET',
                                                 'SCRIPT_NAME'           => '/index.php',
                                                 'SERVER_PROTOCOL'       => 'HTTP/1.1',
                                                 'GATEWAY_INTERFACE'     => 'CGI/1.1',
                                                 'REMOTE_ADDR'           => '1.2.3.4',
                                                 'REMOTE_PORT'           => '',
                                                 'SERVER_ADDR'           => '1.1.1.1',
                                                 'SERVER_PORT'           => '80',
                                                 'X_FORWARDED_FOR'       => '2.3.4.5,1.2.3.4, 1.2.3.4',
                                                 'HTTP_X_FORWARDED_FOR'  => '2.3.4.5,1.2.3.4',
                                                 'HTTP_USER_AGENT'       => 'Opera/9.80 (J2ME/MIDP; Opera Mini/4.1.13906/37.7886; U; ru) Presto/2.12.423 Version/12.16',
                                                 'HTTP_FORWARDED'        => 'for="2.3.4.5:20931"',
                                                 'HTTP_CF_CONNECTING_IP' => '1.2.3.4',
        ]);

        //12 because only have IP
        $this->assertCount(4, $this->getClientIp->getServerHeaders());
        $this->assertNotEmpty($this->getClientIp->getClientIp());
    }

    public function validIpProvider()
    {
        return [
            ['127.0.0.1', false],
            ['10.0.0.1', false],
            ['255.255.255.255', false],
            ['test', false],
            ['8.8.8.8', true],
            ['2001:0db8:85a3:08d3:1319:8a2e:0370:7334', false],
        ];
    }

    /**
     * @dataProvider validIpProvider
     * @covers GetClientIp::validate_ip
     */
    public function testValidIp($ip, $expectedVal)
    {
        $md = new GetClientIp();
        $this->assertSame($expectedVal, $md->validate_ip($ip));
    }

    public function ipProvider()
    {
        return [
            [[
                      'HTTP_X_FORWARDED_FOR' => '8.8.8.8,8.8.4.4',
                  ], '8.8.8.8'],
            [[
                      'REMOTE_ADDR'           => '8.8.4.4',
                      'X_FORWARDED_FOR'       => '8.8.8.8,8.8.4.4, 8.8.4.4',
                  ], '8.8.8.8'],
            [[
                      'REMOTE_ADDR'           => '8.8.4.4',
                      'X_FORWARDED_FOR'       => '127.0.0.1,8.8.4.4, 8.8.4.4',
                  ], '8.8.4.4'],
            [[
                      'REMOTE_ADDR'           => '8.8.4.4',
                      'X_FORWARDED_FOR'       => '127.0.0.1,2001:0db8:85a3:08d3:1319:8a2e:0370:7334, 8.8.8.8',
                  ], '8.8.8.8'],
            [[], false],
        ];
    }

    /**
     * @dataProvider ipProvider
     * @covers GetClientIp::setServerHeaders
     * @covers GetClientIp::getClientIp
     */
    public function testGetClientIp($headers, $expectedIp)
    {
        $md = new GetClientIp($headers);
        $this->assertSame($expectedIp, $md->getClientIp());
    }

    /**
     * @dataProvider ipProvider
     * @covers GetClientIp::setServerHeaders
     * @covers GetClientIp::getLongClientIp
     */
    public function testGetLongClientIp($headers, $expectedIp)
    {
        $md = new GetClientIp($headers);
        $this->assertSame(ip2long($expectedIp), $md->getLongClientIp());
    }
}
