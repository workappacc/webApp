<?php
declare(strict_types=1);

namespace FrontBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use FrontBundle\Entity\FeedBack;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Symfony\Component\DependencyInjection\ContainerInterface;

class QueueManager
{
    private $host;
    private $user;
    private $password;
    private $port;
    private $connection;
    private $em;
    
    public function __construct(
        EntityManagerInterface $entityManager,
        string $host,
        int $port,
        string $user,
        string $password
    ) {
        $this->em = $entityManager;
        $this->host = $host;
        $this->port = $port;
        $this->user = $user;
        $this->password = $password;
        $this->connection = new AMQPStreamConnection($this->host, $this->port, $this->user, $this->password);
    }

    public function addToQueues(FeedBack $feedBack)
    {
        $channel = $this->connection->channel();
        $channel->queue_declare('feedback', false, false, false, false);
        $data = [
            'name' => $feedBack->getName(),
            'email' => $feedBack->getEmail(),
            'content' => $feedBack->getContent(),
        ];
        
        $msg = new AMQPMessage(json_encode($data));
        $channel->basic_publish($msg, '', 'feedback');
        
        $channel->close();
        $this->connection->close();
    }

    public function getFromQueue()
    {
        $channel = $this->connection->channel();
        $channel->queue_declare('feedback', false, false, false, false);
        $count = 0;
        $callback = function ($msg) use (&$count, &$output) {
            $feedBackData = json_decode($msg->getBody(), true);

            if (isset($feedBackData['name']) && isset($feedBackData['email']) && isset($feedBackData['content'])) {

                $feedBack = new FeedBack();
                $feedBack->setName($feedBackData['name']);
                $feedBack->setEmail($feedBackData['email']);
                $feedBack->setContent($feedBackData['content']);
                
                $this->em->persist($feedBack);
                $this->em->flush();

                echo 'New feedback is saved' . PHP_EOL;
            }

        };
        $channel->basic_consume('feedback', '', false, true, false, false, $callback);
        while (count($channel->callbacks)) {
            $channel->wait();
        }
        $channel->close();
        $this->connection->close();
    }
}