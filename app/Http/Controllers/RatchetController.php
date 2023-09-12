<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\ConfirmMessage;
use App\Models\User;
use Illuminate\Support\Facades\DB;

use Exception;
use React\EventLoop\LoopInterface;
use React\EventLoop\TimerInterface;
use SplObjectStorage;
use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;

class RatchetController extends Controller implements MessageComponentInterface
{
    private $loop;
    private $clients;

    /**
     * Store all the connected clients in php SplObjectStorage
     *
     * RatchetController constructor.
     */
    public function __construct(LoopInterface $loop)
    {
        $this->loop = $loop;
        $this->clients = new SplObjectStorage;
        $this->users = [];
    }

    /**
     * Store the connected client in SplObjectStorage
     * Notify all clients about total connection
     *
     * @param ConnectionInterface $conn
     */
    public function onOpen(ConnectionInterface $conn)
    {
        echo "Client connected " . $conn->resourceId . " \n";
        $this->clients->attach($conn);
    }

    /**
     * Remove disconnected client from SplObjectStorage
     * Notify all clients about total connection
     *
     * @param ConnectionInterface $conn
     */
    public function onClose(ConnectionInterface $conn)
    {
        echo "Client left onClose " . $conn->resourceId . " \n";
        $this->clients->detach($conn);
        unset($this->users[$conn->resourceId]);

        $user_ids = array_column(array_values($this->users), 'user_id');

        foreach ($this->clients as $client) {
            $client->send(json_encode([
                "type" => "users",
                "user_ids" => json_encode($user_ids)
            ]));
        }
    }

    /**
     * Receive message from connected client
     * Broadcast message to other clients
     *
     * @param ConnectionInterface $from
     * @param string $data
     */
    public function onMessage(ConnectionInterface $from, $data)
    {
        $resource_id            = $from->resourceId;
        $data                   = json_decode($data);
        $type                   = $data->type;
        switch ($type) {
            case 'chat':
                $user_id        = $data->user_id;
                $recv_id        = $data->recv_id;
                $user_name      = $data->user_name;
                $chat_msg       = $data->chat_msg;

                $response_from  = '
                <li class="flex justify-end">
                    <div class="group/item relative max-w-xl pl-3 pr-1 py-1 text-gray-700 bg-lime-100 rounded shadow text-lg" >
                        <span class="block flex items-end"><span class="pr-3">'.$chat_msg.'</span><span class="group/edit group-hover/item:visible text-xs text-green-600 flex justify-end">'.date('F j H:i').'</span></span>
                    </div>
                </li>';
                $response_to    = '
                <li class="flex justify-start">
                    <div class="group/item relative max-w-xl pl-3 pr-1 py-1 text-gray-700 bg-slate-200 rounded shadow text-lg" >
                        <span class="block flex items-end"><span class="pr-3">'.$chat_msg.'</span><span class="group/edit group-hover/item:visible text-xs text-green-600 flex justify-end">'.date('F j H:i').'</span></span>
                    </div>
                </li>';

                // Output
                $from->send(json_encode([
                    "type" => $type,
                    "msg" => $response_from,
                    "send_id" => $user_id
                ]));

                $to = $this->_getClientByUserId($recv_id);
                if ($to) {
                    $to->send(json_encode([
                        "type" => $type,
                        "msg" => $response_to,
                        "send_id" => $user_id
                    ]));
                } else {                    
                    $dObj                   = new \stdClass();
                    $dObj->user_id          = $recv_id;
                    $dObj->recv_id          = $user_id;
                    ConfirmMessage::updateConfirm($dObj);
                }

                // Save to database
                $message = new Message();
                $message->user_id = $user_id;
                $message->recv_id = $recv_id;
                $message->name = $user_name;
                $message->message = $chat_msg;
                $message->save();

                echo "Resource id $resource_id sent $chat_msg \n";
                break;
            case 'socket':
                if ($data->status == 'connect') {
                    $this->users[$resource_id] = array(
                        'user_id'   => $data->user_id,
                        'client'    => $from
                    );
                } else {
                    unset($this->users[$resource_id]);
                }

                $user_ids = array_column(array_values($this->users), 'user_id');
                foreach ($this->clients as $client) {
                    $client->send(json_encode([
                        "type" => "users",
                        "user_ids" => json_encode($user_ids)
                    ]));
                }        
                break;
        }
    }

    /**
     * Throw error and close connection
     *
     * @param ConnectionInterface $conn
     * @param Exception $e
     */
    public function onError(ConnectionInterface $conn, Exception $e)
    {
        echo "Client left onError " . $conn->resourceId . " \n";
        $conn->close();
    }

    private function _getClientByUserId($user_id) {
        foreach ($this->users as $key => $user) {
            if ($user['user_id'] == $user_id) {
                return $user['client'];
            }
        }
        return;
    }
}
