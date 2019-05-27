# 阿里云mns消息服务 


## 使用composer安装
` composer require zdz/alimns-helper-php `

## 用法

### 初始化客户端
```
    use use AliMNS\MnsHandle;
    
    $endPoint = ''; // your endPoint
    $accessId = ''; // your accessId
    $accessKey = ''; // your accessKey
    
    $client = MnsHandle::client($endPoint, $accessId, $accessKey);
```

### 创建队列

```
    try {
        $client->createQueue($queueName, $attributes);
    } catch(\Exception $e) {
        echo 'falid'. $e->getMessage();
    }
```

* queueName: 队列名称

* attributes: \AliyunMNS\Model\QueueAttributes对象

### 发送消息

```
    try {
        $client->sendMessage($queueName, $messageBody, $delaySeconds, $priority, $base64);
    } catch(\Exception $e) {
        echo 'falid'. $e->getMessage();
    }
```

* queueName: 队列名称

* messageBody: 消息内容

* delaySeconds: 指定的秒数延后可被消费，单位为秒

* priority: 指定消息的优先级权值，优先级越高的消息，越容易更早被消费, 取值范围1~16（其中1为最高优先级），默认优先级为8

* base64：base64加密，默认true

### 消费消息

```
    try {
        $messageResult = $client->receiveMessage($queueName, $autoDelete, $waitSeconds);
    } catch(\Exception $e) {
        echo 'falid'. $e->getMessage();
    }
```

* queueName: 队列名称

* autoDelete: 是否自动删除，默认false

* waitSeconds: 本次 ReceiveMessage 请求最长的Polling等待时间，单位为秒。默认30，30是最大值

### 删除消息

```
    $receiptHandle = $messageResult->getReceiptHandle();
    try {
        $client->deleteMessage($queueName, $receiptHandle);
    } catch(\Exception $e) {
        echo 'falid'. $e->getMessage();
    }
```

* queueName: 队列名称

* receiptHandle: 通过消费消息的返回值获取的临时句柄,

### 删除队列

```
    try {
        $client->deleteQueue($queueName);
    } catch(\Exception $e) {
        echo 'falid'. $e->getMessage();
    }
```

* queueName: 队列名称



