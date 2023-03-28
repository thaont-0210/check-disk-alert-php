<?php

require "src/Disk.php";
require "src/Slack.php";
require "src/DotEnv.php";

(new DotEnv(__DIR__ . '/.env'))->load();

$disk = new Disk();
$disk->runDiskHumanReadableCmd();

if ($disk->dfCmdResult['code'] == 0) {
    $slack = new Slack();
    $slack->setToken(getenv('SLACK_TOKEN'));
    $slack->setChannel(getenv('SLACK_CHANEL_ID'));
    $slack->setTitle('Report: disk space usage in ' . getenv('APP_ENV'));
    $slack->setMessage(prepareMessage($disk->dfCmdResult['result']));
    $slack->sendMessage();
}

function prepareMessage($data)
{
    $mentionUsers = explode(',', getenv('SLACK_MENTION_USERS'));
    $mention = '';
    foreach ($mentionUsers as $value) {
        $mention .= "@{$value} ";
    }

    $text = "{$mention} You received this message because you are chosen one to view disk space in *" . getenv('APP_ENV') . "* server.\n";
    $text .= "*Disk Usage Detail*\n";
    $text .= "```";

    for ($i = 0; $i < count($data); $i++) {
        $text .= $data[$i] . "\n";
    }

    $text .= "```";

    return [
        [
            'type' => 'header',
            'text' => [
                'type' => 'plain_text',
                'text' => 'This is reporty for disk space in ' . getenv('APP_ENV'),
                'emoji' => true
            ]
        ],
        [
            'type' => 'section',
            'text' => [
                'type' => 'mrkdwn',
                'text' => $text,
            ],
        ]
    ];
}
