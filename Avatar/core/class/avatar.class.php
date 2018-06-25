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
	
		$cmd_init = $this->getCmd(null, 'say');
		if (!is_object($cmd_init)) {
			$cmd_init = new avatarCmd();
			$cmd_init->setLogicalId('say');
			$cmd_init->setIsVisible(1);
			$cmd_init->setName(__('Dire', __FILE__));
			$cmd_init->setType('action');
			$cmd_init->setSubType('message');
			$cmd_init->setEqLogic_id($this->getId());
			$cmd_init->save();
		}
		$cmd_init = $this->getCmd(null, 'setstatus');
		if (!is_object($cmd_init)) {
			$cmd_init = new avatarCmd();
			$cmd_init->setLogicalId('setstatus');
			$cmd_init->setIsVisible(1);
			$cmd_init->setName(__('SetStatus', __FILE__));
			$cmd_init->setType('action');
			$cmd_init->setSubType('message');
			$cmd_init->setEqLogic_id($this->getId());
			$cmd_init->save();
		}
		$cmd_init = $this->getCmd(null, 'getstatus');
		if (!is_object($cmd_init)) {
			$cmd_init = new avatarCmd();
			$cmd_init->setLogicalId('getstatus');
			$cmd_init->setIsVisible(1);
			$cmd_init->setName(__('GetStatus', __FILE__));
			$cmd_init->setType('info');
			$cmd_init->setSubType('numeric');
			$cmd_init->setEqLogic_id($this->getId());
			$cmd_init->save();
		}
		$cmd_init = $this->getCmd(null, 'hide');
		if (!is_object($cmd_init)) {
			$cmd_init = new avatarCmd();
			$cmd_init->setLogicalId('hide');
			$cmd_init->setIsVisible(1);
			$cmd_init->setName(__('Cacher', __FILE__));
			$cmd_init->setType('action');
			$cmd_init->setSubType('other');
			$cmd_init->setEqLogic_id($this->getId());
			$cmd_init->save();
		}
		$cmd_init = $this->getCmd(null, 'show');
		if (!is_object($cmd_init)) {
			$cmd_init = new avatarCmd();
			$cmd_init->setLogicalId('show');
			$cmd_init->setIsVisible(1);
			$cmd_init->setName(__('Montrer', __FILE__));
			$cmd_init->setType('action');
			$cmd_init->setSubType('other');
			$cmd_init->setEqLogic_id($this->getId());
			$cmd_init->save();
		}		
		//$this->callAvatarReloadConfig();
		
	}

	public function preInsert() {
		$this->setConfiguration('UID',uniqid());
	}

	public function callAvatarSpeech($callargs)
	{
		log::add('avatar', 'info', 'callAvatarSpeech : '.$callargs);
		$serverip = $this->getConfiguration('IP');
		$serverport = $this->getConfiguration('VoicePort');
				
		$requestHeader = 'http://'.$serverip.':'.$serverport;			
		
		$url = $requestHeader . "/".$callargs;
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_TIMEOUT, 2);
		$response = curl_exec($ch);

		if ($response == false) 
			return 'ERROR : No Response';
		
		return ($response);
	}
	
	
	public function callAvatarReloadConfig()
	{
		$serverip = $this->getConfiguration('IP');
		$response = "OK";
		log::add('avatar', 'info', 'callAvatarReloadConfig');

		if ($this->getConfiguration('animenabled') == '1')
		{
			$serverport = $this->getConfiguration('AnimPort');
				
			$requestHeader = 'http://'.$serverip.':'.$serverport;			

			$url = $requestHeader . "/reloadconfig";

			$header = array("Accept: application/json");
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
			curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1 );
			curl_setopt($ch, CURLOPT_ENCODING, "gzip");
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_TIMEOUT, 5);
			curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.0.3705; .NET CLR 1.1.4322)');

			$response = curl_exec($ch);

			curl_close ($ch); 

			if ($response != '"OK"' ) 
				return 'ERROR : No Response';
		}

		if ($this->getConfiguration('recoenabled') == '1')
		{
			$serverport = $this->getConfiguration('VoicePort');
				
			$requestHeader = 'http://'.$serverip.':'.$serverport;			
		
			$url = $requestHeader . "/reloadconfig";
		
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_TIMEOUT, 1);
			$response = curl_exec($ch);

			curl_close ($ch); 

			if ($response == false) 
				return 'ERROR : No Response';
		
		}

		return ($response);
		
	}


	public function callAvatarAnim($animename)
	{
		$serverip = $this->getConfiguration('IP');
		$serverport = $this->getConfiguration('AnimPort');
				
		$requestHeader = 'http://'.$serverip.':'.$serverport;			

		$url = $requestHeader . "/play/". urlencode($animename);

		$header = array("Accept: application/json");
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1 );
		
		curl_setopt($ch, CURLOPT_ENCODING, "gzip");
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 2);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.0.3705; .NET CLR 1.1.4322)');

		$response = curl_exec($ch);

		if ($response != '"OK"' ) 
			return 'ERROR : No Response';
		
		
		return ($response);
		
	}
	
	
	/*     * **********************Getteur Setteur*************************** */
}

class avatarCmd extends cmd 
{
	/*     * *************************Attributs****************************** */

	/*     * ***********************Methode static*************************** */

	/*     * *********************Methode d'instance************************* */

	public function dontRemoveCmd() {
		if ($this->getLogicalId() == 'refresh') {
			return true;
		}
		return false;
	}

	public function preSave() {
		if ($this->getLogicalId() == 'refresh') {
			return;

			}
	}
	
	public function execute($_options = array()) {
		$result="";
		
			if ($this->getType()=='action') 
			{

				if ( $this->getConfiguration('grammar') == 'yes' )
				{
						$callargs['grammar'] = $this->getName();
						$callargs['command'] = $_options['title'];
						$callargs['message'] = $_options['message'];
						
						if ( $callargs['command'] == 'Activate' )
						{
						log::add('avatar', 'debug', 'execute avatar cmd');
						
						//$eqLogic = $this->getEqLogic(); 	
						}
						else if ( $callargs['command'] == 'Activate' )
						{
						log::add('avatar', 'debug', 'execute avatar cmd');

						}
						else
							log::add('avatar', 'info', 'grammar ( '.$callargs['grammar'].') Command non gérée : '.$callargs['command'].' - '.$callargs['message']);
				}

				if ( $this->getConfiguration('anim') == 'yes' )
				{
				
						$callargs['anim'] = $this->getName();
						$callargs['command'] = $_options['title'];
						$callargs['message'] = $_options['message'];
						
						if (( $callargs['command'] == 'Play' ) | ( $callargs['command'] == '[Jeedom] Message de test' ) )
						{
						
							$animation = $this->getConfiguration('animation');

							log::add('avatar', 'info', 'Play Anim ( '.$callargs['anim'].' ) ');

							$eqLogic = $this->getEqLogic(); 	
							$result = $eqLogic->callAvatarAnim($callargs['anim']);
						}
						else
							log::add('avatar', 'info', 'anim ( '.$callargs['anim'].' ) Command non gérée : '.$callargs['command'].' - '.$callargs['message']);
						
						//$eqLogic = $this->getEqLogic(); 	

				}
					//	$result = $eqLogic->callAvatarSpeech($callargs);
					//	$this->setStatus($eqLogic,$result);
					//	log::add('avatar', 'info', $result);
						
			}
		return ($result);
		
	}
}