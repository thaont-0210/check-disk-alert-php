<?php

require "src/Disk.php";
require "src/Slack.php";
require "src/DotEnv.php";

(new DotEnv(__DIR__ . '/.env'))->load();

$disk = new Disk();
$disk->setDiskOverPercent(getenv('ALERT_AFTER_OVERCOME'));
$disk->run();

if (count($disk->diskOverResult) > 0) {
    $slack = new Slack();
    $slack->setToken(getenv('SLACK_TOKEN'));
    $slack->setChannel(getenv('SLACK_CHANEL_ID'));
    $slack->setMessage('Alert overcome!!!');
    $slack->sendMessage();
}
