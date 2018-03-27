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
//require_once '../../3rdparty/kodi_com.php';


 //  MAIN
 
try {
    require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';
	
    include_file('core', 'authentification', 'php');
/*
    if (!isConnect('admin')) {
        throw new Exception(__('401 - Accès non autorisé', __FILE__));
    }
*/
	if (init('action') == 'testServer') {
		/*
		$ip = init('ip');
		$login = init('login');
		$pwd = init('pwd');
		$uid = init('uid');
		$port = init('port');
		*/
		
		$eqid = init('eqid');
        $eqLogic = eqLogic::byId($eqid);		
		
		$callargs = 'test';
		$reply = $eqLogic->callAvatar($callargs);
		
		
		log::add('avatar', 'info', 'testServer return : '.$reply);
        ajax::success($reply);
    }		
	


	if (init('action') == 'getVoices') {

		$eqid = init('eqid');
        $eqLogic = eqLogic::byId($eqid);		
		
		$callargs = 'voices';
		$reply = $eqLogic->callAvatar($callargs);
		
		$voicelist = split(',',$reply);
		
		//log::add('avatar', 'info', 'testServer return : '.$reply);
        ajax::success($voicelist);
    }

    if (init('action') == 'getScriptContent') {
        $path = init('path');
		
        if (!file_exists($path)) {
            throw new Exception(__('Aucun fichier trouvé : ', __FILE__) . $path);
        }
        if (!is_readable($path)) {
            throw new Exception(__('Impossible de lire : ', __FILE__) . $path);
        }
        if (is_dir($path)) {
            throw new Exception(__('Impossible de lire un dossier : ', __FILE__) . $path);
        }
        $pathinfo = pathinfo($path);
        $return = array(
            'content' => file_get_contents($path),
            'extension' => $pathinfo['extension']
        );
        ajax::success($return);
    }

	  if (init('action') == 'saveScriptContent') {
        $path = init('path');
        if (!file_exists($path)) {
            throw new Exception(__('Aucun fichier trouvé : ', __FILE__) . $path);
        }
        if (!is_writable($path)) {
            throw new Exception(__('Impossible d\'écrire dans : ', __FILE__) . $path);
        }
        if (is_dir($path)) {
            throw new Exception(__('Impossible d\'écrire un dossier : ', __FILE__) . $path);
        }
        file_put_contents($path, init('content'));
        chmod($path, 0770);
        ajax::success();
    }

    if (init('action') == 'removeScript') {
        $path = init('path');
        if (!file_exists($path)) {
            throw new Exception(__('Aucun fichier trouvé : ', __FILE__) . $path);
        }
        if (!is_writable($path)) {
            throw new Exception(__('Impossible d\'écrire dans : ', __FILE__) . $path);
        }
        if (is_dir($path)) {
            throw new Exception(__('Impossible de supprimer un dossier : ', __FILE__) . $path);
        }
        if(!unlink($path)){
            throw new Exception(__('Impossible de supprimer le fichier : ', __FILE__) . $path);
        }
        ajax::success();
    }

    if (init('action') == 'addUserScript') {
        $path = calculPath(config::byKey('userScriptDir', 'avatar') . '/' . init('name'));
		
        if (!touch($path)) {
            throw new Exception(__('Impossible d\'écrire dans : ', __FILE__) . $path);
        }
        ajax::success($path);
    }
	

	
   // throw new Exception(__('Aucune methode correspondante à : ', __FILE__) . init('action'));
    /*     * *********Catch exeption*************** */
} catch (Exception $e) {
    ajax::error(displayExeption($e), $e->getCode());
}



?>
