<?php
namespace App;

use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;

class Game implements MessageComponentInterface
{

    protected $clients;
    private $gameStarted = false;
    private $gestures = [];

    public function __construct() {
        $this->clients = new \SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn) {
        $this->clients->attach($conn);
        $this->startGame();
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        if($msg == 'play'){
            $this->gameStarted = true;
            $this->startGame();
            $this->send($msg);
        }
        if(in_array($msg, ['rock', 'paper', 'scissors'])){
            $this->gestures[] = $msg;
            if(count($this->gestures)==2){
                $this->send($this->getWinner());
                $this->gameStarted = false;
            }
        }
        if($msg == 'timeOut'){
            $this->gameStarted = false;
            $this->send($msg);
        }
    }

    public function onClose(ConnectionInterface $conn) {
        $this->clients->detach($conn);
        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "An error has occurred: {$e->getMessage()}\n";
        $conn->close();
    }

    private function startGame(){
        if($this->gameStarted && count($this->clients)==2){
            $this->send('start');
            $this->gestures = [];
            $this->finishTime = time() + $this->seconds;
        }
    }

    private function send($msg){
        $x=0;
        foreach ($this->clients as $client) {
            if($x++>2) break;
            $client->send($msg);
        }
    }

    private function getWinner(){
        sort($this->gestures);
        if($this->gestures[0]=='paper' && $this->gestures[1]=='rock')
            return 'paper';
        elseif($this->gestures[0]=='paper' && $this->gestures[1]=='scissors')
            return 'scissors';
        elseif($this->gestures[0]=='rock' && $this->gestures[1]=='scissors')
            return 'rock';
        else return 'draw';
    }
}