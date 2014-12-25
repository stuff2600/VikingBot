<?php

/**
 * Plugin that returns the N last row of the bot`s log file
 */
class botLogPlugin extends basePlugin {

	/**
	 * @return array[]
	 */
	public function help() {
		return array(
			array(
				'command'     => 'botlog [adminPassword] [limit=10]',
				'description' => 'Responds with the last N rows of the bot\'s log file.'
			)
		);
	}

	public function onMessage($from, $channel, $msg) {
		//Only trigger on !botlog
		if(stringStartsWith($msg, "{$this->config['trigger']}botlog")) {
			//Get hold of number of rows to show and possible password
			$tmp = explode(" ", $msg);
			$pass = '';
			if(count($tmp) == 3) {
				$pass = $tmp[1];
				$limit = $tmp[2];
			} else if(count($tmp) == 2) {
				$limit = $tmp[1];
			}

			if(!is_numeric($limit)){
				$limit = 10;
			}

			if(strlen($this->config['adminPass']) > 0 && $pass != $this->config['adminPass']) {
				sendMessage($this->socket, $channel, "{$from}: Wrong password");
			} else {
				//Password auth ok, display log data
				sendMessage($this->socket, $channel, "{$from}: Last {$limit} entries from bot log:");
				$logdata = file('logs/vikingbot.log');
				$rows = count($logdata);
				for($i=$rows - $limit; $i<$rows; $i++){
					sendMessage($this->socket, $channel, "{$logdata[$i]}");
					//Avoid Excess flood kick from server
					usleep(500000);
				}
				sendMessage($this->socket, $channel, "------------");
			}
		}
	}

}
