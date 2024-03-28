<?php

namespace WS\PicoWebSocket;

interface WSInterface
{
   public function onOpen($clientChat);
   public function onClientLogin($clientChat);
   public function onClose($clientChat);
   public function onMessage($clientChat, $receivedText);
}
