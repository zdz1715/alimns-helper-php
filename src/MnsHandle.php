<?php
namespace AliMNS;

use AliyunMNS\Client;
use AliyunMNS\Config;
use AliyunMNS\Model\QueueAttributes;
use AliyunMNS\Requests\SendMessageRequest;
use AliyunMNS\Requests\CreateQueueRequest;
use AliyunMNS\Exception\MnsException;

class MnsHandle
{
    /**
     * @var MnsHandle client 对象
     */
    protected static $client;

    /**
     * @var object 对象实例
     */
    protected static $instance;


    /**
     * 初始化
     * @param $endPoint
     * @param $accessId
     * @param $accessKey
     * @param null $securityToken
     * @param Config|NULL $config
     * @return MnsHandle|object
     */
    public static function client($endPoint, $accessId, $accessKey, $securityToken = NULL, Config $config = NULL)
    {
        if (is_null(self::$instance)) {
            /**
             * @var AliyunMNS\Client self::$client
             */
            self::$instance = new static($endPoint, $accessId, $accessKey, $securityToken = NULL, $config = NULL);
        }
        return self::$instance;
    }

    private function __construct($endPoint, $accessId, $accessKey, $securityToken = NULL, $config = NULL) {
        /**
         * @var Client self::$client
         */
        self::$client = new Client($endPoint, $accessId, $accessKey, $securityToken = NULL, $config = NULL);
    }

    private function __clone()
    {
        // TODO: Implement __clone() method.
    }


    /**
     * 创建队列
     * @param $queueName
     * @param QueueAttributes|null $attributes
     */
    public function createQueue($queueName, QueueAttributes $attributes = null) {
        $request = new CreateQueueRequest($queueName, $attributes);
        try {
            self::$client->createQueue($request);
        }
        catch (MnsException $e)
        {
            throw new \LogicException($e, $e->getCode());
        }
    }

    /**
     * 删除队列
     * @param $queueName
     */
    public function deleteQueue($queueName) {
        try {
            self::$client->deleteQueue($queueName);
        }
        catch (MnsException $e)
        {
            throw new \LogicException($e, $e->getCode());
        }
    }

    /**
     * 发送消息
     * @param $queueName
     * @param $messageBody
     * @param null $delaySeconds
     * @param null $priority
     * @param bool $base64
     */
    public function sendMessage($queueName, $messageBody, $delaySeconds = NULL, $priority = NULL, $base64 = TRUE) {
        $queue = self::$client->getQueueRef($queueName, $base64);
        $request = new SendMessageRequest($messageBody, $delaySeconds, $priority, $base64);
        try {
            $queue->sendMessage($request);
        }
        catch (MnsException $e)
        {
            throw new \LogicException($e, $e->getCode());
        }
    }




    /**
     * 消费消息
     * @param $queueName
     * @param bool $autoDelete
     * @param int $waitSeconds
     * @return |null
     */
    public function receiveMessage($queueName, $autoDelete = false, $waitSeconds = 30) {
        $receiptHandle = NULL;
        $queue = self::$client->getQueueRef($queueName);
        try {
            $messageResult = $queue->receiveMessage($waitSeconds);
        }
        catch (MnsException $e)
        {
            throw new \LogicException($e, $e->getCode());
        }
        $receiptHandle = $messageResult->getReceiptHandle();
        if ($autoDelete) {
            $queue->deleteMessage($receiptHandle);
        }
        return $messageResult;
    }

    /**
     * 删除消息
     * @param $queueName
     * @param $receiptHandle
     */
    public function deleteMessage($queueName, $receiptHandle) {
        $queue = self::$client->getQueueRef($queueName);
        try {
            $res = $queue->deleteMessage($receiptHandle);
        }
        catch (MnsException $e)
        {
            throw new \LogicException($e, $e->getCode());
        }
    }
}

