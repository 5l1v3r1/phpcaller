<?php

require_once 'Api.php';
require_once 'Output.php';
require_once 'Helper.php';

class PHPCaller {

	private $_sapi, $_argv;
	public $newLineCharacter = "\n";
	public $tabCharacter = "\t";

	public function __construct($sapi = 'cli', $argv = []){
		$this->_sapi = $sapi;
		$this->_argv = $argv;
	}

	public function run($secureMode = true){
		Output::setNewLineCharacter($this->newLineCharacter);
		if($secureMode && php_sapi_name() !== $this->_sapi){
			echo "Unauthorized access";
			return;
		}

		if(count($this->_argv) < 2){
			Output::write(Output::colorString("ILLEGAL COMMAND!", "error").$this->newLineCharacter.$this->newLineCharacter.Output::colorString("Type ", "success").Output::colorString("php truecaller list ").Output::colorString("to show a list of all available commands", "success"));
			return;
		}


		$helper = new Helper($this->newLineCharacter, $this->tabCharacter);
		$api = new Api();
		switch ($this->_argv[1]) {
			case 'list':
				echo $this->newLineCharacter.$this->newLineCharacter.str_repeat("-", 150).$this->newLineCharacter.$this->newLineCharacter;
				$helper->showHeader();
				echo $this->newLineCharacter;
				Output::write($this->newLineCharacter.$this->tabCharacter."LIST OF AVAILABLE COMMANDS");
				$helper->listCommands();
				echo str_repeat("-", 150).$this->newLineCharacter.$this->newLineCharacter;
				return;
				break;
			case 'help':
				echo $this->newLineCharacter.$this->newLineCharacter.str_repeat("-", 150).$this->newLineCharacter.$this->newLineCharacter;
				$helper->displayManPage();
				echo $this->newLineCharacter.str_repeat("-", 150).$this->newLineCharacter.$this->newLineCharacter;
				return;
				break;
			case 'setBearer':
				if(!isset($this->_argv[2])){
					Output::write(Output::colorString("Bearer ID not included"));
					return;
				}
				$helper->setBearer($this->_argv[2]);
				return;
				break;
			case 'search':
				if(!isset($this->_argv[2]) || !is_numeric($this->_argv[2])){
					Output::write(Output::colorString("Phone number not provided or is invalid"));
					return;
				}
				$bearer = $helper->getBearer();
				$api->setHeaders(array(
					"authorization"	=>	"Bearer $bearer"
				));
				$r = $api->fetch($this->_argv[2]);
				Output::write(Output::colorString($r['body'], "success"));
				break;
			default:
				Output::write(Output::colorString($this->tabCharacter."ERROR: ", "error").Output::colorString("Front ").Output::colorString("\"".$this->_argv[1]."\"", "error").Output::colorString(" not found"));
				echo $this->tabCharacter."LIST OF AVAILABLE COMMANDS".$this->newLineCharacter.$this->newLineCharacter;
				$helper->listCommands();
				return;
		}


	}
}