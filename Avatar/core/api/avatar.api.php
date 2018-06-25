<?php
 /* * ***************************Includes********************************* */
 
 // Exemple
 //  http://192.168.0.20/plugins/avatar/core/api/avatar.api.php?func=listgrammar&uid=588874e94ed3e
require_once dirname(__FILE__) . "/../../../../core/php/core.inc.php";
//require_once '../../3rdparty/simple_html_dom.php';


function getVoiceConfig()
{
	global $eqLogic;
	$eqparams = "avatarID=". $eqLogic->getName().";";
	$eqparams .= "Room=". $eqLogic->getConfiguration('Room').";";
	$eqparams .= "Roomd=". $eqLogic->getConfiguration('Roomd').";";
	$eqparams .= "Level=". $eqLogic->getConfiguration('Level').";";
	$eqparams .= "Levela=". $eqLogic->getConfiguration('Levela').";";
	$eqparams .= "Leveld=". $eqLogic->getConfiguration('Leveld').";";
	$eqparams .= "Confidence=". $eqLogic->getConfiguration('Confidence').";";
	$eqparams .= "CultureInfo=". $eqLogic->getConfiguration('CultureInfo').";";
	$eqparams .= "Voice=". $eqLogic->getConfiguration('Voice').";";
	$eqparams .= "RecoEnabled=". $eqLogic->getConfiguration('recoenabled').";";
	$eqparams .= "VoiceEnabled=". $eqLogic->getConfiguration('voiceenabled').";";
	$eqparams .= "ETHPort=". $eqLogic->getConfiguration('VoicePort');
	
	return ($eqparams);
}

function getAnimConfig()
{
	global $eqLogic;
	$eqparams = "PosX=". $eqLogic->getConfiguration('PosX').";";
	$eqparams .= "PosY=". $eqLogic->getConfiguration('PosY').";";
	$eqparams .= "SizeX=". $eqLogic->getConfiguration('SizeX').";";
	$eqparams .= "SizeY=". $eqLogic->getConfiguration('SizeY').";";
	$eqparams .= "anim_start=". $eqLogic->getConfiguration('anim_start').";";
	$eqparams .= "anim_idle=". $eqLogic->getConfiguration('anim_idle').";";
	$eqparams .= "anim_say=". $eqLogic->getConfiguration('anim_say').";";
	$eqparams .= "anim_warn=". $eqLogic->getConfiguration('anim_warn').";";
	$eqparams .= "AnimEnabled=". $eqLogic->getConfiguration('animenabled').";";
	$eqparams .= "ShowForm=". $eqLogic->getConfiguration('showform').";";
	$eqparams .= "ETHPort=". $eqLogic->getConfiguration('AnimPort');
	
	return ($eqparams);
}


function encodePath($inpath)
{
	$url = rawurlencode($inpath);
	// we must keep the / for path
	$url = str_replace('%2F', '/', $url);
	
	return ($url);
}


function processCommand($query,$cmdtype)
{

		$param = array();
		$reply = interactQuery::tryToReply($query,$param);
		echo $reply['reply'];

}


function getGrammarList($filetype)
{
	$glist="";
	global $eqLogic;

	$cmds = cmd::byEqLogicId($eqLogic->getId());

	foreach ($cmds as $cmd){

		if ( $cmd->getConfiguration("grammar","") == "yes" )  
		{
			if ( $cmd->getConfiguration("filetype","") == $filetype )
			{
				$glist .= $cmd->getName().";";

			}
		}
	}	

	return ($glist);
}


function getAnimList()
{
	$glist="";
	global $eqLogic;

	$cmds = cmd::byEqLogicId($eqLogic->getId());

	foreach ($cmds as $cmd){

		if ( $cmd->getConfiguration("anim","") == "yes" )  
		{

			$glist .= '{"name":"'. $cmd->getName().'" , "model" : "'.$cmd->getConfiguration("model","").'"';
			$glist .= ' , "look":"'. $cmd->getConfiguration("look","").'" , "animation" : "'.$cmd->getConfiguration("animation","").'"';
			$glist .= ' , "parameters":"'. $cmd->getConfiguration("parameter","").'" };';
			
		}
	}	

	return ($glist);
}

function getGrammar($name)
{
	$gfilename="";
	global $eqLogic;

	$cmd = cmd::byEqLogicIdCmdName($eqLogic->getId(),$name);
	$gfilename = $cmd->getConfiguration("request","");

	return ($gfilename);
}


// -------------------------------------------- MAIN

$accessgranted = false;	
	
$eqLogics = eqLogic::byType('avatar');
$uniqueID = init('uid');
$granted="unknown";

$returnOK = [];
$returnOK['error']="0";

// Authenticate Client

if (init('apikey') =="")
{
	foreach ($eqLogics as $eqLogic) 
	{

		if ( $eqLogic->getConfiguration('UID') == $uniqueID  )
		{
			$accessgranted = true;
			$granted=$eqLogic->getName();
			// update IP / Hostname if read only
			$ipgranted = $_SERVER['REMOTE_ADDR'];
			if ( $eqLogic->getConfiguration('forceIP') != "1" )
				{
				$eqLogic->setConfiguration('IP',$ipgranted) ;
				$eqLogic->save();
				}
			break;
		}
	}
}
else
	if (jeedom::apiAccess(init('apikey'), 'avatar')) 
		$accessgranted = true;

if ( $accessgranted )
{
	// Manage Hello Test function 

	if ( init('func') == 'hello' )
	{
		log::add('avatar', 'info', 'Hello from '.$granted.' IP:'.$ipgranted);
		echo 'welcome '.$granted;
		return;
	}
}	
else
{
	log::add('avatar', 'error', 'unauthorised access from '.$_SERVER['REMOTE_ADDR']);
	echo 'who are you '. $_SERVER['REMOTE_ADDR'].' ?';
	die();	
}

// PROCESS API get Commands

switch (init('func')){

	
	case "listgrammar":
	
		$grammarlist = getGrammarList("grammar");
		echo $grammarlist;
	break;

	case "listsubgrammar":
	
		$grammarlist = getGrammarList("subgrammar");
		echo $grammarlist;
	break;

	case "listinclude":
	
		$grammarlist = getGrammarList("sharedgrammar");
		echo $grammarlist;
	break;

	case "getGrammar":
	
		$filename = getGrammar(init('file'));

		$file = file_get_contents($filename);
		echo $file;
	break;

	case "getvoiceconfig":
	
		$config = getVoiceConfig();
		echo $config;
	break;

	case "getanimconfig":
	
		$config = getAnimConfig();
		echo $config;
	break;

	case "listanims":
	
		$animlist = getAnimList();
		echo $animlist;
	break;

	case "process":
	
		$result = processCommand(init('cmd'),init('type'));
		echo $result;
	break;

	
}




?>
