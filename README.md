# Check disk alert
This tool will do:

- Check disk usage space, then send notify to Slack if usage percent is greater than some value
- Keep sending report about disk usage space to Slack

# Setup

## Slack app
- Go to [apps manager](https://api.slack.com/apps) to create new app
- Select **OAuth & Permissions** to add **Scopes**

Required scope for *Bot Token Scopes* is [chat:write](https://api.slack.com/scopes/chat:write)
- After that go to **Install App** to install your app to your Slack workspace
- Go back **OAuth & Permissions** to see *OAuth Tokens for Your Workspace* section

Copy *Bot User OAuth Token* for using later

## Setting env
- Clone project to your directory
- Go to project folder, make `.env` file by using command
```
cp .env.example .env
```
- Fill values for env
```
APP_ENV=develop # environment develop - staging - product - etc
ALERT_AFTER_OVERCOME=70 # disk usage space percent - if over 70% => alert message will be send to Slack
SLACK_CHANNEL_ID=C04Uxxxxx # Slack channel ID - for receive message
SLACK_MENTION_USERS=nguyen.the.thao,haha.hihi # user name will be mentioned in message
SLACK_TOKEN=xoxb-xxxx # That bot user oauth token above
```

## Setting cron job
- Open crontab
```
crontab -e
```
- Add new job for checking disk and alert, for example:
```
* * * * * /usr/bin/php /mnt/d/Projects/check-disk-alert-php/check.php
```
That mean we will check every minutes.

- Add new job for reporting, for example:
```
1 6 * * * /usr/bin/php /mnt/d/Projects/check-disk-alert-php/report.php
```
That mean report will be sent at 6:01 every day.
