<?php

class SearchManager
{
	const ERROR_NO_CONTENT = 1;
	const ERROR_RUN_FAILURE = 2;
	const ERROR_TOO_MANY_TASKS = 3;
	const ERROR_LOCK_OPEN_FAILURE = 4;
	const ERROR_LOCK_OBTAIN_FAILURE = 5;
	const ERROR_TOO_QUICK_SEARCH = 6;

	const MIN_SEARCH_INTEVAL = 600;
	const MAX_TASKS_NUM = 5;

	private $errorCode = 0;
	private $result;

	public function __construct($configFile) {
		$this->configFile = $configFile;
	}

	public function error($errorCode) {
		$this->errorCode = $errorCode;
		return $this->errorCode;
	}

	public function getErrorResult() {

		switch ($this->errorCode) {

		case self::ERROR_NO_CONTENT:
			$msg = 'No content';
			break;

		case self::ERROR_RUN_FAILURE:
		case self::ERROR_TOO_MANY_TASKS:
		case self::ERROR_LOCK_OPEN_FAILURE:
		case self::ERROR_LOCK_OBTAIN_FAILURE:
			$msg = 'Inner error';
			break;

		case self::ERROR_TOO_QUICK_SEARCH:
			$msg = 'Too quick';
			break;

		default:
			$msg = '';
			break;
		}

		return $msg;
	}

	public function lock() {

		$path = sys_get_temp_dir() . '/'. 'search-lock.dat';
		$fp = fopen($path, 'ab') and fclose($fp);

		$fp = fopen($path, 'r+b');
		if (! $fp) {
			return $this->error(self::ERROR_LOCK_OPEN_FAILURE);
		}

		if(! flock($fp, LOCK_EX)) {
			return $this->error(self::ERROR_LOCK_OBTAIN_FAILURE);
		}

		fseek($fp, 0, SEEK_SET);
		$len = filesize($path);

		if ($len > 0) {
			$buf = fread($fp, $len);
		} else {
			$buf = NULL;
		}

		$num = 0;
		if (! empty($buf)) {
			$num = (int)$buf;

			if ($num > self::MAX_TASKS_NUM) {
				fclose($fp);
				return $this->error(self::ERROR_TOO_MANY_TASKS);
			}
		}

		$num ++;

		fseek($fp, 0, SEEK_SET);
		fwrite($fp, '' . $num);

		fclose($fp);

		return 0;
	}

	public function unlock() {

		$path = sys_get_temp_dir() . '/'. 'search-lock.dat';

		$fp = fopen($path, 'r+b');
		if (! $fp) {
			return $this->error(self::ERROR_LOCK_OPEN_FAILURE);
		}

		if(! flock($fp, LOCK_EX)) {
			return $this->error(self::ERROR_LOCK_OBTAIN_FAILURE);
		}

		fseek($fp, 0, SEEK_SET);
		$len = filesize($path);

		if ($len > 0) {
			$buf = fread($fp, $len);
		} else {
			$buf = NULL;
		}

		$num = 0;
		if (! empty($buf)) {
			$num = (int)$buf;
			$num --;
		}

		if ($num !== 0) {
			fseek($fp, 0, SEEK_SET);
			fwrite($fp, '' . $num);
		}

		fclose($fp);

		if (0 === $num) {
			unlink($path);
		}

		return 0;
	}

	public function startSession() {

		session_start();

		$now = time();

		if (isset($_SESSION['lastTimeStamp']) and $now < ($_SESSION['lastTimeStamp'] + self::MIN_SEARCH_INTEVAL)) {
			return $this->error(self::ERROR_TOO_QUICK_SEARCH);
		}

		$_SESSION['lastTimeStamp'] = $now;

		return 0;
	}
	
	public function endSession() {
	}

	public function search() {

		if (! empty($_POST)) {
			$content = $_POST['content'];
		} elseif (! empty($_GET)) {
			$content = $_GET['content'];
		} else {
			return $this->error(self::ERROR_NO_CONTENT);
		}

		$content = trim($content);

		if (empty($content)) {
			return $this->error(self::ERROR_NO_CONTENT);
		}

		$content = urlencode($content);

		$config = parse_ini_file($this->configFile);

		$configFile = $config['searcher-config-path'];
		$scriptFile = $config['searcher-script-path'];

		$tempFile = tempnam(sys_get_temp_dir(), 'search-result-');

		$cmd = '/bin/bash ' . $scriptFile . ' ' . $configFile . ' "' . $content . '" ' . $tempFile;

		$output = system($cmd, $retval);

		if (0 === $retval) {
			$this->result = file_get_contents($tempFile);
		} else {
		   	$this->error(self::ERROR_RUN_FAILURE);
		}

		unlink($tempFile);

		return $self->errorCode;
	}

	public function run() {
		do {
			if (0 != $this->startSession()) {
				break;
			}

			if (0 != $this->lock()) {
				break;
			}

			$this->search();

			$this->unlock();

		} while (False);

		if (0 === $this->errorCode) {
			return $this->result;
		} else {
			return '{"error" : {"code": ' . $this->errorCode . ', "message": "' . $this->getErrorResult() .'"}}';
		}
	}

}

?>
 
