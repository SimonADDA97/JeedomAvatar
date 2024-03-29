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

require_once dirname(__FILE__) . '/../../../core/php/core.inc.php';
include_file('core', 'authentification', 'php');
if (!isConnect()) {
	include_file('desktop', '404', 'php');
	die();
}
?>
<form class="form-horizontal">
    <fieldset>
		<div class="form-group" >
			<label class="col-lg-5 control-label">{{Client vocal}}</label>
            <div class="col-lg-4">
			     <a class="btn btn-default" href="https://github.com/rmesnard/avatar/releases/tag/1.0.0"><i class="fa fa-cloud-download"></i> {{Télécharger le client vocal pour Windows 64}}</a>
			</div>   
		</div>   
		
		<div class="form-group" >
			<label class="col-lg-5 control-label">{{Client 3D}}</label>
            <div class="col-lg-4">
			     <a class="btn btn-default" href="https://github.com/rmesnard/Lijah3D/releases/tag/1.0.0"><i class="fa fa-cloud-download"></i> {{Télécharger le client 3D pour Windows 64}}</a>
			</div>   
		</div>   

		<div class="form-group">
            <label class="col-lg-2 control-label">{{Chemin des scripts utilisateur}}</label>
            <div class="col-lg-4">
               <input type="text" class="configKey form-control" data-l1key="userScriptDir" />
            </div>
        </div>


    </fieldset>
</form>


