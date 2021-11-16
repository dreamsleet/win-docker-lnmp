
<?php
//kafka低级消费者
//(只从指定的partition消费)
$conf = new RdKafka\Conf();
//$conf->set('security.protocol', 'SASL_PLAINTEXT');
//$conf->set('sasl.mechanism', 'PLAIN');
//$conf->set('sasl.username', 'admin');
//$conf->set('sasl.password', 'admin');
#$conf->set('bootstrap.servers', '172.17.36.105:9092,172.17.36.106:9092');      #集群配置  

#$conf->set('group.id', 'sms-consumer-group');
$rk = new RdKafka\Consumer($conf);
$rk->addBrokers("docker-kafka:9092");
$topicConf = new RdKafka\TopicConf();
$topicConf->set('auto.commit.interval.ms', 100);        //自动提交，可能导致重复消费  $topicConf->set('enable.auto.commit', false);
$topicConf->set('offset.store.method', 'file');
$topicConf->set('offset.store.path', sys_get_temp_dir());
$topicConf->set('auto.offset.reset', 'smallest');
$topic = $rk->newTopic("smsTest", $topicConf);
$topic->consumeStart(0, RD_KAFKA_OFFSET_STORED);
while(true) {
    $message = $topic->consume(0, 5000);
    if ($message) {
        echo "读取到消息\n\r";
        #var_dump($message);
        switch($message->err)
        {
            case RD_KAFKA_RESP_ERR_NO_ERROR:
                echo "读取消息成功:\n\r";
                var_dump($message->offset.' : '.$message->payload);
                break;
            case RD_KAFKA_RESP_ERR__PARTITION_EOF:
                echo "读取消息失败\n\r";
                break;
            case RD_KAFKA_RESP_ERR__TIMED_OUT:
                echo "请求超时\n\r";
                break;
            default:
                throw new \Exception($message->errstr(), $message->err);
            break;
        }
    } else {
        echo "未读取到消息\n\r";
    }
}
