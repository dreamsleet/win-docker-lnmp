<?php
//生产者  kafka设置了认证，需要配置认证信息，这里使用了简单的sasl用户名和密码认证方式
$conf = new RdKafka\Conf();
//$conf->set('security.protocol', 'SASL_PLAINTEXT');
//$conf->set('sasl.mechanism', 'PLAIN');
//$conf->set('sasl.username', 'admin');
//$conf->set('sasl.password', 'admin');

$rk = new Rdkafka\Producer($conf);
$rk->setLogLevel(LOG_DEBUG);


// 链接kafka集群
//$rk->addBrokers("localhost:9092,192.168.20.6:9093");
$rk->addBrokers("docker-kafka:9092");


// 创建topic
$topic = $rk->newTopic("smsTest");

while (true) {
    $message = "hello kafka " . date("Y-m-d H:i:s");
    echo "hello kafka " . date("Y-m-d H:i:s") . PHP_EOL;

    try {
        $topic->produce(RD_KAFKA_PARTITION_UA, 0, $message);
        sleep(3);
    } catch (\Exception $e) {
        echo $e->getMessage() . PHP_EOL;
    }
}
