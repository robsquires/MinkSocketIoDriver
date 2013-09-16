<?php

namespace spec\Squirrel\Mink\SocketIo;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ClientSpec extends ObjectBehavior
{
    /**
     * @param Squirrel\Mink\SocketIo\Server $server
     * @param Behat\Mink\Session $session
     */
    function let($server, $session)
    {
        $this->beConstructedWith($server, $session);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Squirrel\Mink\SocketIo\Client');
    }

    function it_should_tell_the_server_to_create_a_client_listening_on_an_address_and_open_the_client($server, $session)
    {
        $host = "127.0.0.1";

        $server
            ->setClientHost($host)
            ->shouldBeCalled()
            ->willReturn(true)
        ;

        $session
            ->visit('/')
            ->shouldBeCalled()
        ;


        $this
            ->open($host)
            ->shouldReturn(true)
        ;
    }
}
