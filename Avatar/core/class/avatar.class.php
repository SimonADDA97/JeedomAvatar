<?php

/* This file is part of Jeedom.
 *
 * Jeedom is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Jeedom is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
 */

/* * ***************************Includes********************************* */
require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';
//require_once '../../3rdparty/kodi_com.php';

class avatar extends eqLogic {
	/*     * *************************Attributs****************************** */

	/*     * ***********************Methode static*************************** */

	public function postSave() {

	/* Remove unused commands
		$cmd = cmd::byEqLogicIdCmdName($this->getId(),'Start');
		if (is_object($cmd))
			$cmd->remove();
	*/
	/*
	
		$cmd = cmd::byEqLogicIdCmdName($this->getId(),'Status');
		if (!is_object($cmd))
			$this->createCmdInfo('Status',$this->getId(),'Status'.$this->getId());	

		$cmd = cmd::byEqLogicIdCmdName($this->getId(),'GetStatus');
		if (!is_object($cmd))
			$this->createCmd('GetStatus',$this->getId(),'GetStatus'.$this->getId(),'other');	

		$cmd = cmd::byEqLogicIdCmdName($this->getId(),'SendCommand');
		if (!is_object($cmd))
			$this->createCmd('SendCommand',$this->getId(),'SendCommand'.$this->getId(),'message');	
		
		*/
	}

	public function preInsert() {
		$this->setConfiguration('UID',uniqid());
	}

	
	public function preRemove() {
	}	
		
	
	public function createCmdInfo($cmdname,$eqlogic,$cmdlogic) {
		log::add('avatar', 'debug', 'create Info Command '.$cmdlogic.' = '.$cmdname);
		$cmd = new avatarCmd();
		$cmd->setLogicalId($cmdlogic);
		$cmd->setName($cmdname);
		$cmd->setTemplate('dashboard', 'tile');
		$cmd->setEqLogic_id($eqlogic);
		$cmd->setType('info');
		$cmd->setSubType('string');
		$cmd->save();
	}	

	public function createCmd($cmdname,$eqlogic,$cmdlogic,$cmdsubtype) {
		log::add('avatar', 'debug', 'create Command '.$cmdlogic.' = '.$cmdname);
		$cmd = new avatarCmd();
		$cmd->setLogicalId($cmdlogic);
		$cmd->setName($cmdname);
		$cmd->setTemplate('dashboard', 'tile');
		$cmd->setEqLogic_id($eqlogic);
		$cmd->setType('action');
		$cmd->setSubType($cmdsubtype);
		$cmd->save();
	}	
	
	public function callAvatar($callargs)
	{
		
		$serverip = $this->getConfiguration('IP');
		$serverport = $this->getConfiguration('Port');
				
		$requestHeader = 'http://'.$serverip.':'.$serverport;			
		
		$url = $requestHeader . "/".$callargs;
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_TIMEOUT, 3);
		$response = curl_exec($ch);

		if ($response == false) 
			return 'ERROR : No Response';
		
		
		return ($response);
		
	}
	

	
	/*     * **********************Getteur Setteur*************************** */
}

class avatarCmd extends cmd {
	/*     * *************************Attributs****************************** */

	/*     * ***********************Methode static*************************** */

	/*     * *********************Methode d'instance************************* */

	public function dontRemoveCmd() {
		return true;
	}

	
	public function execute($_options = array()) {
		$result="";
		
		//log::add('avatar', 'info', 'execute avatar cmd');
		
			if ($this->getType()=='action') {
			
/*
				switch ($this->getName()) {
					case 'GetStatus':

						$callargs['function'] = $this->getName();

						$eqLogic = $this->getEqLogic(); 	
						$result = $eqLogic->callKodi($callargs);
						$this->setStatus($eqLogic,$result);
						log::add('avatar', 'info', $result);


						
					break;
					
					case 'SendCommand':
*/
						$callargs['grammar'] = $this->getName();
						$callargs['command'] = $_options['title'];
						$callargs['parameter'] = $_options['message'];
						
						log::add('avatar', 'info', 'callAvatarSpeech '.$callargs['command'].' '.$callargs['grammar']);
						
						$eqLogic = $this->getEqLogic(); 	
					//	$result = $eqLogic->callAvatarSpeech($callargs);
					//	$this->setStatus($eqLogic,$result);
					//	log::add('avatar', 'info', $result);
						
		//			break;
					
		//		}
			}
		return $result;
	}

	/*     * **********************Getteur Setteur*************************** */
}

?>
