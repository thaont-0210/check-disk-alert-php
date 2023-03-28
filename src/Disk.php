<?php

class Disk {
    public $diskOver = 70; // percent
    public $dfCmdResult = array();
    public $diskResult = array();
    public $dfCmdOverResult = array();
    public $diskOverResult = array();

    public function getOverResult()
    {
        $this->runDiskHumanReadableOverCmd();
        $this->getDiskHumanReadableOver();
    }

    public function getReportResult()
    {
        $this->runDiskHumanReadableCmd();
        $this->getDiskHumanReadable();
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
            $j = 0;
            for ($i = count($this->dfCmdOverResult['result']) - 1; $i >= 0; $i--) {
                $result[$j] = processStringToArrayHelper($this->dfCmdOverResult['result'][$i]);
                $j++;
            }
        }

        $this->diskOverResult = $result;

        return 0;
    }
}

function processStringToArrayHelper($string, $regex = '!\s+!')
{
    $string = preg_replace($regex, ' ', $string);

    return explode(' ', $string);
}
