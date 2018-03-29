<?php
if (!isConnect('admin')) {
	throw new Exception('{{401 - Accès non autorisé}}');
}
sendVarToJS('userScriptDir', getRootPath() . '/' . config::byKey('userScriptDir', 'avatar'));

$plugin = plugin::byId('avatar');
sendVarToJS('eqType', $plugin->getId());
$eqLogics = eqLogic::byType($plugin->getId());

?>




<div class="row row-overflow">
	<div class="col-lg-2 col-md-3 col-sm-4">
		<div class="bs-sidebar">
			<ul id="ul_eqLogic" class="nav nav-list bs-sidenav">
				<a class="btn btn-warning " style="width : 100%;margin-top : 5px;margin-bottom: 5px;" href="/index.php?v=d&p=plugin&id=avatar">
					<i class="fa fa-cogs"></i> {{Configuration du plugin}} 
				</a>   
				<a class="btn btn-warning " style="width : 100%;margin-top : 5px;margin-bottom: 5px;" href="/index.php?v=d&p=log&logfile=avatar">
					<i class="fa fa-comment"></i> {{Logs du plugin}} 
				</a> 	  
				<a class="btn btn-default eqLogicAction" style="width : 100%;margin-top : 5px;margin-bottom: 5px;" data-action="add"><i class="fa fa-plus-circle"></i> {{Ajouter un Avatar}}</a>
				<li class="filter" style="margin-bottom: 5px;"><input class="filter form-control input-sm" placeholder="{{Rechercher}}" style="width: 100%"/></li>
				<legend><i class="fa fa-cog"></i> {{Avatar}}</legend>
				<?php
						foreach ($eqLogics as $eqLogic) {
							$opacity = ($eqLogic->getIsEnable()) ? '' : jeedom::getConfiguration('eqLogic:style:noactive');
							echo '<li class="cursor li_eqLogic" data-eqLogic_id="' . $eqLogic->getId() . '" style="' . $opacity . '"><a>' . $eqLogic->getHumanName(true) . '</a></li>';
						}
						?>
			</ul>
		</div>
	</div>

	<div class="col-lg-10 col-md-9 col-sm-8 eqLogicThumbnailDisplay" style="border-left: solid 1px #EEE; padding-left: 25px;">
		<legend><i class="fa fa-cog"></i> {{Gestion}}</legend> 

		<div class="eqLogicThumbnailContainer">
			<div class="cursor eqLogicAction" data-action="add" style="background-color : #ffffff; height : 200px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 160px;margin-left : 10px;" >
				<center>
					<i class="fa fa-plus-circle" style="font-size : 7em;color:#00979C;"></i>
				</center>
				<span style="font-size : 1.1em;position:relative; top : 23px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;color:#00979C"><center>Ajouter</center></span>
			</div>
			<div class="cursor eqLogicAction" data-action="gotoPluginConf" style="background-color : #ffffff; height : 200px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 160px;margin-left : 10px;">
				<center>
					<i class="fa fa-wrench" style="font-size : 7em;color:#00979C;"></i>
				</center>
				<span style="font-size : 1.1em;position:relative; top : 23px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;color:#00979C"><center>{{Configuration}}</center></span>
			</div>		
			<div class="cursor eqLogicAction" style="background-color : #ffffff; height : 200px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 160px;margin-left : 10px;">
				<a target="_blank" style="text-decoration: none!important;" href="https://www.jeedom.fr/doc/documentation/plugins/avatar/fr_FR/avatar.html">
					<center>
						<i class="fa fa-book" style="font-size : 7em;color:#00979C;"></i>
					</center> 
					<span style="font-size : 1.1em;position:relative; top : 23px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;color:#00979C"><center>{{Documentation}}</center></span>
				</a>
			</div>			

		</div>

		<legend><i class="fa fa-table"></i> {{Configuration}}
		</legend>
		<div class="eqLogicThumbnailContainer">
			<?php
				foreach ($eqLogics as $eqLogic) {
						$opacity = ($eqLogic->getIsEnable()) ? '' : jeedom::getConfiguration('eqLogic:style:noactive');
						echo '<div class="eqLogicDisplayCard cursor" data-eqLogic_id="' . $eqLogic->getId() . '" style="background-color : #ffffff; height : 200px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 160px;margin-left : 10px;' . $opacity . '" >';
						echo "<center>";
						echo '<img src="plugins/avatar/doc/images/avatar_icon.png" height="105" width="95" />';
						echo "</center>";
						echo '<span style="font-size : 1.1em;position:relative; top : 15px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;"><center>' . $eqLogic->getHumanName(true, true) . '</center></span>';
						echo '</div>';
				}
				?>
		</div>

	</div>

	<!-- Affichage de l'eqLogic sélectionné -->
	<div class="col-lg-10 col-md-9 col-sm-8 eqLogic" style="border-left: solid 1px #EEE; padding-left: 25px;display: none;">
		<legend>
			<i class="fa fa-arrow-circle-left eqLogicAction cursor" data-action="returnToThumbnailDisplay"></i> {{Général}}
			<i class='fa fa-cogs eqLogicAction pull-right cursor expertModeVisible' data-action='configure'></i>
			<a class="btn btn-xs btn-default pull-right eqLogicAction" data-action="copy"><i class="fa fa-files-o"></i> {{Dupliquer}}</a>
		</legend>
		<a class="btn btn-success eqLogicAction pull-right" data-action="save"><i class="fa fa-check-circle"></i> {{Sauvegarder la configuration}}</a>
		<a class="btn btn-danger eqLogicAction pull-right" data-action="remove"><i class="fa fa-minus-circle"></i> {{Supprimer l'équipement}}</a>

		<ul class="nav nav-tabs" role="tablist">
			<li role="presentation" class="active"><a href="#eqlogictab" aria-controls="home" role="tab" data-toggle="tab"><i class="fa fa-list-alt"></i> {{Configuration}}</a></li>
			<li role="presentation"><a href="#cfgReco" aria-controls="profile" role="tab" data-toggle="tab"><i class="fa fa-list-alt"></i> {{Reconnaissance vocale}}</a></li>
			<li role="presentation"><a href="#cfgVoice" aria-controls="profile" role="tab" data-toggle="tab"><i class="fa fa-list-alt"></i> {{Voix}}</a></li>
			<li role="presentation"><a href="#cmdAnims" aria-controls="profile" role="tab" data-toggle="tab"><i class="fa fa-list-alt"></i> {{Animations 3D}}</a></li>
		</ul>

		<div class="tab-content" style="height:calc(100% - 50px);overflow:auto;overflow-x: hidden;">

			<div role="tabpanel" class="tab-pane active" id="eqlogictab">	
											

				<form class="form-horizontal">
					<fieldset>								
						<div class="form-group">
							<label class="col-sm-2 control-label">{{Nom de l'équipement Avatar}}</label>
							<div class="col-sm-2">
								<input type="text" class="eqLogicAttr form-control" data-l1key="id" style="display : none;" />
								<input type="text" class="eqLogicAttr form-control" data-l1key="name" placeholder="{{Nom de l'équipement Avatar}}"/>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label" >{{Objet parent}}</label>
							<div class="col-sm-2">
								<select id="sel_object" class="eqLogicAttr form-control" data-l1key="object_id">
									<option value="">{{Aucun}}</option>
									<?php
											foreach (object::all() as $object) {
												echo '<option value="' . $object->getId() . '">' . $object->getName() . '</option>';
											}
											?>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label">{{Catégorie}}</label>
							<div class="col-sm-8">
								<?php
										foreach (jeedom::getConfiguration('eqLogic:category') as $key => $value) {
											echo '<label class="checkbox-inline">';
											echo '<input type="checkbox" class="eqLogicAttr" data-l1key="category" data-l2key="' . $key . '" />' . $value['name'];
											echo '</label>';
										}
										?>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label"></label>
							<div class="col-sm-8">
								<label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="isEnable" checked="true" />{{Activer}}</label>
								<label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="isVisible" checked="true" />{{Visible}}</label>
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-2 control-label">{{UID}}</label>
							<div class="col-sm-2">
								<input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="UID" readonly="true" />
							</div>
						</div>
						
						<div class="form-group">
							<label class="col-sm-2 control-label">{{IP / Hostname}}</label>
							<div class="col-sm-2">
								<input id="inp_P" type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="IP" readonly="false" placeholder="{{IP / Hostname}}" />
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-2 control-label">{{Port Voix}}</label>
							<div class="col-sm-2">
								<input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="VoicePort" placeholder="{{Port}}" />
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-2 control-label">{{Port Anim 3D}}</label>
							<div class="col-sm-2">
								<input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="AnimPort" placeholder="{{Port}}" />
							</div>
						</div>
						
						<div class="form-group">
							<div class="col-sm-3">
								<a id="btn_testAvatarserver" class="btn btn-success eqLogicAction pull-right" ><i class="fa fa-check-circle"></i> {{Tester la connection}}</a>
							</div>
						</div>				
				

					</fieldset>
				</form>

			</div>

			<div role="tabpanel" class="tab-pane" id="cfgReco" >
				
				<br>
					<div class="form-group row">
						<label class="control-label col-sm-2">{{Configuration de la reconaissance vocale}}</label>
						<label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="recoenabled" checked="true" />{{Activer}}</label>
					</div>

					<div class="form-group row">
						<div class="col-xs-2">
							<label for="Confidence" >{{Confidence}}</label>
							<input id="Confidence" type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="Confidence" placeholder="{{75}}" />
						</div>
						<div class="col-xs-2">
							<label for="CultureInfo" >{{Culture}}</label>
							<input id="CultureInfo" type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="CultureInfo" placeholder="{{fr - FR}}" />
						</div>
					</div>

					<div class="form-group row">
					  <div class="col-xs-2">
						<label for="room">{{Nom de la piece}}</label>
						<input id="room" type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="Room" placeholder="{{piece}}" />
					  </div>
					  <div class="col-xs-2">
						<label for="roomd">{{Nom de la piece avec prefixe de,du,de la... }}</label>
						<input id="roomd" type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="Roomd" placeholder="{{de la piece}}" />
					  </div>
					</div> 

					<div class="form-group row">
					  <div class="col-xs-2">
						<label for="level">{{Nom de l'étage}}</label>
						<input id="level" type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="Level" placeholder="{{Etage}}" />
					  </div>
					  <div class="col-xs-2">
						<label for="leveld">{{Nom de l'etage avec prefixe de,du,de la... }}</label>
						<input id="leveld" type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="Leveld" placeholder="{{de l'etage}}" />
					  </div>
					  <div class="col-xs-2">
						<label for="levela">{{Nom de l'etage avec prefixe a , au ... }}</label>
						<input id="levela" type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="Levela" placeholder="{{a l'etage}}" />
					  </div>
					</div> 

				<br>
					<div class="row">
					<div class="col-sm-12">
						<a class="btn btn-success btn-sm pull-right" id="bt_addGrammar"><i class="fa fa-plus-circle"></i> {{Ajouter une grammaire}}</a>

						<table id="table_recogrammar" class="table table-bordered table-condensed">
							<thead>
								<tr>
									<th style="width: 50px;">#</th>
									<th>{{Grammaire}}</th>
									<th>{{Status initial}}</th>
									<th>{{Contenu}}</th>
									<th>{{Action}}</th>
								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
						</div> 
					</div> 
				
			</div>


			<div role="tabpanel" class="tab-pane" id="cfgVoice" >
			
			<br/>
					<div class="form-group row">
					<label class="control-label col-sm-2">{{Configuration de la parole}}</label>
					
					
						<div class="col-sm-8">
								<label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="voiceenabled" checked="true" />{{Activer}}</label>
						</div>


					</div>


					<div class="form-group row">
						<label class="col-sm-2 control-label">{{Voix}}</label>
						<div class="col-sm-2">
							<input id="sel_voice" type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="Voice" placeholder="{{Microsoft Hortense Desktopge}}" />
						</div>
						<div class="col-sm-2">
							<a id="btn_choosevoice" class="btn btn-success eqLogicAction pull-right" ><i class="fa fa-check-circle"></i> {{Choisir une voix}}</a>
						</div>
					</div>

			
			</div>
		

			<div role="tabpanel" class="tab-pane" id="cmdAnims" >

				
					<br/>

					<div class="form-group row">
						<label class="control-label col-sm-2">{{Animations}}</label>
						<label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="animenabled" checked="true" />{{Activer}}</label>
					</div>

					<div class="form-group row">

					<div class="form-group">
						<label class="col-sm-2 control-label">{{Mode par défaut d'affichage}}</label>
						<div class="col-sm-2">
							<select id="video_mode" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="video_mode" >
								<option value="" >{{Overlay}}</option>
								<option value="" >{{Plein écran}}</option>
								<option value="" >{{Stream uniquement}}</option>
							</select>
						</div>

						<label class="col-sm-2 control-label">{{Model par défaut}}</label>
						<div class="col-sm-2">
							<select id="start_model" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="start_model" >
								<option value="" >Taichi</option>
							</select>
						</div>
					</div>	
					</div>


					<div class="form-group">		
					<br/>
					<div class="form-group row">
					<div class="col-sm-12">
						<a class="btn btn-success btn-sm pull-right" id="bt_addAnimation"><i class="fa fa-plus-circle"></i> {{Ajouter une grammaire}}</a>
					</div>	
					</div>
						<table id="table_cmdAnims" class="table table-bordered table-condensed">

							<thead>
								<tr>
									<th>#</th>
									<th>{{Nom}}</th>
									<th>{{Model}}</th>									
									<th>{{Animations}}</th>
								</tr>
							</thead>					

							<tbody>
							</tbody>

						</table>

					</div>

				
			</div>



		</div>
	</div>
</div>


<div id="md_browseScriptFile" title="Parcourir...">
    <div style="display: none;" id="div_browseScriptFileAlert"></div>
    <div id="div_browseScriptFileTree"></div>
</div>

<div id="md_editScriptFile" title="Editer...">
    <div style="display: none;" id="div_editScriptFileAlert"></div>
    <textarea id="ta_editScriptFile" class="form-control" style="height: 100%;"></textarea>
</div>


<?php include_file('desktop', 'avatar', 'js', 'avatar');?>
<?php include_file('core', 'plugin.template', 'js');?>