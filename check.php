<?php

require "src/Disk.php";
require "src/Slack.php";
require "src/DotEnv.php";

(new DotEnv(__DIR__ . '/.env'))->load();

$disk = new Disk();
$disk->setDiskOverPercent(getenv('ALERT_AFTER_OVERCOME'));
$disk->getOverResult();

if (count($disk->diskOverResult) > 0) {
    $slack = new Slack();
    $slack->setToken(getenv('SLACK_TOKEN'));
    $slack->setChannel(getenv('SLACK_CHANEL_ID'));
    $slack->setTitle('Alert: disk space in ' . getenv('APP_ENV') . ' is over!!!');
    $slack->setMessage(prepareMessage($disk->diskOverResult));
    $slack->sendMessage();
}

function prepareMessage($data)
{
    $mentionUsers = explode(',', getenv('SLACK_MENTION_USERS'));
    $mention = '';
    foreach ($mentionUsers as $value) {
        $mention .= "@{$value} ";
    }

    $fields = [
        [
            'type' => 'mrkdwn',
            'text' => '*Filesystem*'
        ],
        [
            'type' => 'mrkdwn',
            'text' => '*Used*'
        ]
    ];

    $maxFields = 4;

    for ($i = 0; $i < $maxFields; $i++) {
        if (isset($data[$i])) {
            $fields[] = [
                'type' => 'plain_text',
                'text' => $data[$i][0] . '[' . $data[$i][5] . ']',
                'emoji' => true
            ];

            $fields[] = [
                'type' => 'mrkdwn',
                'text' => $data[$i][4] . '(' . $data[$i][2] . '/' . $data[$i][1] . ')',
            ];
        }
    }

    return [
        [
            'type' => 'header',
            'text' => [
                'type' => 'plain_text',
                'text' => 'This is alert for over disk space in ' . getenv('APP_ENV'),
                'emoji' => true
            ]
        ],
        [
            'type' => 'section',
            'text' => [
                'type' => 'mrkdwn',
                'text' => "{$mention} You received this message because disk space in *" . getenv('APP_ENV') . '* server has been used over ' . getenv('ALERT_AFTER_OVERCOME') . '%',
            ],
            'fields' => $fields,
        ]
    ];
}
