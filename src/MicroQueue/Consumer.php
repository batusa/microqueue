<?php

namespace MicroQueue;

class Consumer
{
    const CONSUMER_DEFAULT_MESSAGE_TYPE = 1;

    public function consume(Queue $queue, callable $callback)
    {
        $numberOfUnreadMessages = $queue->getNumberUnreadMessages();

        $queueResource = $queue->getResource();
        $receivedMessageType = null;
        $messageMaxSize = $queue->getMessageAllowedSize();
        $receivedMessage = null;
        $unserializeMessage = true;
        $flags = 0;
        $errorCode = 0;

        $result = @msg_receive(
            $queueResource,
            self::CONSUMER_DEFAULT_MESSAGE_TYPE,
            $receivedMessageType,
            $messageMaxSize,
            $receivedMessage,
            $unserializeMessage,
            $flags,
            $errorCode
        );

        if (false === $result) throw new Exception\MessageBufferSizeOverflowException;

        if (self::CONSUMER_DEFAULT_MESSAGE_TYPE != $receivedMessageType) continue;

        call_user_func_array($callback, array($receivedMessage, $this));
    }
}
