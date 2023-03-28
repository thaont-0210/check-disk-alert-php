<?php

class Disk {
	public $diskOver = 70; // percent
	public $dfCmdResult = array();
	public $diskResult = array();
	public $dfCmdOverResult = array();
	public $diskOverResult = array();

	public function run()
	{
		// $this->runDiskHumanReadableCmd();
		// $this->getDiskHumanReadable();
		$this->runDiskHumanReadableOverCmd();
		$this->getDiskHumanReadableOver();
	}

	public function setDiskOverPercent($percent)
	{
		$this->diskOver = $percent;
	}

	public function runDiskHumanReadableCmd()
	{
		exec('df -h', $output, $retval);

		$this->dfCmdResult = array(
			'code' => $retval,
			'result' => $output,
		);

		return 0;
	}

	public function runDiskHumanReadableOverCmd()
	{
		exec("df -h | awk '$5 > {$this->diskOver}' | { read -r line; sort -k5; }", $output, $retval);

		$this->dfCmdOverResult = array(
			'code' => $retval,
			'result' => $output,
		);

		return 0;
	}

	public function getDiskHumanReadable()
	{
		$result = array();
		if ($this->dfCmdResult['code'] == 0) {
			for ($i = 0; $i < count($this->dfCmdResult['result']); $i++) {
				$result[$i] = processStringToArrayHelper($this->dfCmdResult['result'][$i]);
			}
		}

		$this->diskResult = $result;

		return 0;
	}

	public function getDiskHumanReadableOver()
	{
		$result = array();
		if ($this->dfCmdOverResult['code'] == 0 && count($this->dfCmdOverResult['result']) > 0) {
			for ($i = count($this->dfCmdOverResult['result']) - 1; $i >= 0; $i--) {
				$result[$i] = processStringToArrayHelper($this->dfCmdOverResult['result'][$i]);
			}
		}

		$this->diskOverResult = $result;

		return 0;
	}
}


class Slack
{
	public $postMessageUrl = "https://slack.com/api/chat.postMessage";

	function sendMessage($message, $channel)
    {
        $ch = curl_init($this->postMessageUrl);
	    $data = http_build_query([
	        "token" => "xxx",
	    	"channel" => $channel,
	    	"text" => $message,
	    ]);

	    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
	    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	    $result = curl_exec($ch);
	    curl_close($ch);
	    
	    return $result;
    }
}

function processStringToArrayHelper($string, $regex = '!\s+!')
{
	$string = preg_replace($regex, ' ', $string);

	return explode(' ', $string);
}

class DotEnv
{
    protected $path;

    public function __construct($path)
    {
        if(!file_exists($path)) {
            throw new \InvalidArgumentException(sprintf('%s does not exist', $path));
        }

        $this->path = $path;
    }

    public function load()
    {
        if (!is_readable($this->path)) {
            throw new \RuntimeException(sprintf('%s file is not readable', $this->path));
        }

        $lines = file($this->path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0) {
                continue;
            }

            list($name, $value) = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value);

            if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
                putenv(sprintf('%s=%s', $name, $value));
                $_ENV[$name] = $value;
                $_SERVER[$name] = $value;
            }
        }
    }
}
