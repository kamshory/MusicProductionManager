<?php

namespace MusicProductionManager;

use MagicObject\Database\PicoDatabase;

class ShutdownManager
{
   /**
    * Database
    *
    * @var PicoDatabase
    */
   private $database;
   /**
    * Constructor
    *
    * @param PicoDatabase $database
    */
   public function __construct($database)
   {
      $this->database = $database;
   }

   /**
    * Shutdown
    *
    * @param PicoDatabase $database
    */
   public function shutdown($database)
   {
      $database->disconnect();
   }

   /**
    * Register shutdown
    *
    * @return void
    */
   public function registerShutdown()
   {
      register_shutdown_function([$this, "shutdown"], $this->database);
   }
}
