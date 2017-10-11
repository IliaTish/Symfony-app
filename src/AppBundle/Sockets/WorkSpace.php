<?php
// myapp\src\yourBundle\Sockets\Chat.php;

// Change the namespace according to your bundle, and that's all !
namespace AppBundle\Sockets;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class WorkSpace implements MessageComponentInterface {
    protected $clients;
    protected $subscribers;

    public function __construct() {
        $this->clients = array();
        $this->subscribers = array();
    }

    public function onOpen(ConnectionInterface $conn) {
        // Store the new connection to send messages to later
        $this->clients[$conn->resourceId] = $conn;
        echo "New connection! ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        $numRecv = count($this->clients) - 1;
        echo sprintf('Connection %d sending message "%s" to %d other connection%s' . "\n"
            , $from->resourceId, $msg, $numRecv, $numRecv == 1 ? '' : 's');
        $data = json_decode($msg,true);
        if($data['command'] == 'subscribe'){
            $this->subscribers[$from->resourceId] = $data['workspace_id'];
        }
        if($data['command'] == 'message') {
            foreach ($this->clients as $key => $client) {
                if ($from !== $client && $this->subscribers[$client->resourceId] == $data['workspace_id']) {
                    // The sender is not the receiver, send to each client connected
                    $client->send($msg);
                }
            }
        }
    }

    public function onClose(ConnectionInterface $conn) {
        // The connection is closed, remove it, as we can no longer send it messages
        unset($this->clients[$conn->resourceId]);

        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "An error has occurred: {$e->getMessage()}\n";

        $conn->close();
    }
}