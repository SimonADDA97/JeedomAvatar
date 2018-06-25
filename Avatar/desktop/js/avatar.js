
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


// Global
editor = null;

$("#bt_addGrammar").on('click', function (event) {
    var _cmd = { type: 'action', subType: 'message'};
    _cmd.configuration = { grammar: 'yes' };
    addCmdToTable(_cmd);
});

$("#bt_addAnimation").on('click', function (event) {
    var _cmd = { type: 'action', subType: 'message' };
    _cmd.configuration = { anim: 'yes' };
    addCmdToTable(_cmd);
});

$("#bt_textosay").on('click', function (event) {
    var equip_id = $('.eqLogicAttr[data-l1key=id]').value();
    var textosay = $('#texttosay').value();
    sendtextosay(equip_id, textosay);
});


$("#bt_synchronize").on('click', function (event) {
    var equip_id = $('.eqLogicAttr[data-l1key=id]').value();
    sendsynchronize(equip_id);
});

function sendtextosay(_equip_id, _textosay) {
    $.ajax({
        type: "POST",
        url: "plugins/avatar/core/ajax/avatar.ajax.php",
        data: {
            action: "say",
            eqid: _equip_id,
            message: _textosay
        },
        dataType: 'json',
        error: function (request, status, error) {
            handleAjaxError(request, status, error);
        },
        success: function (reply) {
            $('#div_alert').showAlert({ message: '{{Commande exécutée}}', level: 'success' });
        }
    });

}


function sendsynchronize(_equip_id) {
    $.ajax({
        type: "POST",
        url: "plugins/avatar/core/ajax/avatar.ajax.php",
        data: {
            action: "synchro",
            eqid: _equip_id
        },
        dataType: 'json',
        error: function (request, status, error) {
            handleAjaxError(request, status, error);
        },
        success: function (reply) {
            $('#div_alert').showAlert({ message: '{{Commande exécutée}}', level: 'success' });
        }
    });

}



$("#md_browseScriptFile").dialog({
    autoOpen: false,
    modal: true,
    height: (jQuery(window).height() - 150),
});


$("#table_recogrammar tbody").delegate(".browseScriptFile", 'click', function (event) {
    var tr = $(this).closest('tr');
    $("#md_browseScriptFile").dialog('open');
    $('#div_browseScriptFileTree').fileTree({
        root: '/',
        script: 'plugins/avatar/3rdparty/jquery.fileTree/jqueryFileTree.php?root=' + encodeURIComponent(userScriptDir),
        folderEvent: 'click'
    }, function (file) {
        $("#md_browseScriptFile").dialog('close');
        if (userScriptDir.slice(-1) == '/' && file.slice(0, 1) == '/') {
            file = file.slice(1);
        }
        tr.find('.cmdAttr[data-l1key=configuration][data-l2key=request]').value(userScriptDir + file);
    });
});

$("#table_recogrammar").sortable({ axis: "y", cursor: "move", items: ".cmd", placeholder: "ui-state-highlight", tolerance: "intersect", forcePlaceholderSize: true });

$("#md_editScriptFile").dialog({
    autoOpen: false,
    modal: true,
    height: (jQuery(window).height() - 150),
    width: (jQuery(window).width() - 150)
});


$("#table_recogrammar tbody").delegate(".editScriptFile", 'click', function (event) {
    var tr = $(this).closest('tr');
    var path = tr.find('.cmdAttr[data-l1key=configuration][data-l2key=request]').val();
    if (path.indexOf(' ') > 0) {
        path = path.substr(0, path.indexOf(' '));
    }
    var data = loadScriptFile(path);
    if (data === false) {
        return;
    }

    if (editor != null) {
        editor.getDoc().setValue(data.content);
        editor.setOption("mode", data.mode);
        setTimeout(function () {
            editor.refresh();
        }, 1);
    } else {
        $('#ta_editScriptFile').val(data.content);
        setTimeout(function () {
            editor = CodeMirror.fromTextArea(document.getElementById("ta_editScriptFile"), {
                lineNumbers: true,
                mode: data.mode,
                matchBrackets: true
            });
            editor.getWrapperElement().style.height = ($('#md_editScriptFile').height()) + 'px';
            editor.refresh();
        }, 1);
    }

    $("#md_editScriptFile").dialog('option', 'buttons', {
        "Annuler": function () {
            $(this).dialog("close");
        },
        "Enregistrer": function () {
            if (saveScriptFile(path, editor.getValue())) {
                $(this).dialog("close");
            }
        }
    });
    $("#md_editScriptFile").dialog('open');
});


$("#table_recogrammar tbody").delegate(".newScriptFile", 'click', function (event) {
    var tr = $(this).closest('tr');
    bootbox.prompt("Nom de la grammaire ?", function (result) {
        if (result !== null) {
            var path = addUserScript(result);
            if (path !== false) {
                tr.find('.cmdAttr[data-l1key=configuration][data-l2key=request]').val(path);
                $('#md_newUserScript').modal('hide');
                tr.find('.editScriptFile').click();
            }
        }
    });
});

$("#table_recogrammar tbody").delegate(".removeScriptFile", 'click', function (event) {
    var tr = $(this).closest('tr');
    var path = tr.find('.cmdAttr[data-l1key=configuration][data-l2key=request]').val();
    if (path.indexOf(' ') > 0) {
        path = path.substr(0, path.indexOf(' '));
    }
    if (path.indexOf('?') > 0) {
        path = path.substr(0, path.indexOf('?'));
    }
    $.hideAlert();
    bootbox.confirm('{{Etes-vous sûr de vouloir supprimer le script :}} <span style="font-weight: bold ;">' + path + '</span> ?', function (result) {
        if (result) {
            removeScript(path);
            tr.find('.cmdAttr[data-l1key=configuration][data-l2key=request]').val('');
        }
    });
});


$('#btn_testAvatarserver').click(function () {
    var equip_id = $('.eqLogicAttr[data-l1key=id]').value();

    updateVoiceList(equip_id);

});


function testServer(_equip_id) {
    $.ajax({
        type: "POST",
        url: "plugins/avatar/core/ajax/avatar.ajax.php",
        data: {
            action: "testServer",
            eqid: _equip_id
        },
        dataType: 'json',
        error: function (request, status, error) {
            handleAjaxError(request, status, error);
        },
        success: function (reply) {
            if (reply.result !== 'OK')
                $('#div_alert').showAlert({ message: reply.result, level: 'danger' });
            else
                $('#div_alert').showAlert({ message: '{{Test de connection réussi}}', level: 'success' });
        }
    });

}
 

 
function updateVoiceList( _equip ) {
	
	var _optionhtml = "";
	
		$.ajax({
		type: "POST",
		url: "plugins/avatar/core/ajax/avatar.ajax.php", 
		data: {
			action: "getVoices",
			equipid: _equip.id
		},
		dataType: 'json',
		error: function (request, status, error) {
			handleAjaxError(request, status, error);
		},
		success: function (data) {
			
			if (data.state !== 'ok') {
				$('#div_alert').showAlert({message: data.result, level: 'danger'});
				return;
			}
			// la liste des voix
			var eqlist = data.result;
			var idx =0;
			for (var voice  in voicelist) {
				if ( voice ==  _equip.selvoice )
                    _optionhtml += '<option selected>' + voicelist[idx]+'</option>';
				else
                    _optionhtml += '<option >' + voicelist[idx]+'</option>';
					
			}

		}
	});	
	
	$('#sel_voice').html(_optionhtml);
	
}

function printEqLogic(_eqLogic) {
    $('#sel_anim_start').empty();
    $('#sel_anim_idle').empty();
    $('#sel_anim_say').empty();
    $('#sel_anim_warn').empty();

    $('#sel_anim_start').append('<option value="Aucune">Aucune</option>');
    $('#sel_anim_idle').append('<option value="Aucune">Aucune</option>');
    $('#sel_anim_say').append('<option value="Aucune">Aucune</option>');
    $('#sel_anim_warn').append('<option value="Aucune">Aucune</option>');
}

function addCmdToTable(_cmd) {


    if (!isset(_cmd)) {
        var _cmd = {};
    }
    if (!isset(_cmd.configuration)) {
        _cmd.configuration = {};
    }
    if (init(_cmd.logicalId) == 'refresh') {
        return;
    }

    if (_cmd.configuration['grammar'] == 'yes') {
    
        var tr = '<tr class="cmd" data-cmd_id="' + init(_cmd.id) + '" >';
         tr += '<td>';
         tr += '<span class="cmdAttr" data-l1key="id"></span>';
         tr += '<span class="cmdAttr" style="display : none;" data-l1key="type" ></span>';
         tr += '<span class="cmdAttr" style="display : none;" data-l1key="subType" ></span>';
         tr += '<span class="cmdAttr" style="display : none;" data-l1key="configuration" data-l2key="grammar" ></span>';
         tr += '</td>';
        tr += '<td>';
        tr += '<div class="row">';
        tr += '<div class="col-sm-6">';
        tr += '<input class="cmdAttr form-control input-sm" data-l1key="name">';
        tr += '</div>';
        tr += '</div>';
        tr += '</td>';
        tr += '<td>';
        tr += '<select class="cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="filetype" style="margin-top : 5px;" >';
        tr += '<option value="grammar">{{Grammaire Active}}</option>';
        tr += '<option value="subgrammar">{{Grammaire Inactive}}</option>';
        tr += '<option value="sharedgrammar">{{Grammaire Partagée}}</option>';
        tr += '</select>';
        tr += '</td>';
        tr += '<td><textarea style="height : 45px;" class="cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="request"></textarea>';
        tr += '<a class="btn btn-default browseScriptFile" style="margin-top : 5px;"><i class="fa fa-folder-open"></i> {{Parcourir}}</a> ';
        tr += '<a class="btn btn-default editScriptFile" style="margin-top : 5px;"><i class="fa fa-edit"></i> {{Editer}}</a> ';
        tr += '<a class="btn btn-success newScriptFile" style="margin-top : 5px;"><i class="fa fa-file-o"></i> {{Nouveau}}</a> ';
        tr += '<a class="btn btn-danger removeScriptFile" style="margin-top : 5px;"><i class="fa fa-trash-o"></i> {{Supprimer}}</a> ';
        tr += '</div>';
        tr += '</td>';
        tr += '<td>';
        if (is_numeric(_cmd.id)) {
            tr += '<a class="btn btn-default btn-xs cmdAction" data-action="configure"><i class="fa fa-cogs"></i></a> ';
            tr += '<a class="btn btn-default btn-xs cmdAction" data-action="test"><i class="fa fa-rss"></i> {{Activer}}</a>';
        }
        tr += ' <a class="btn btn-default btn-xs cmdAction" data-action="copy" title="Dupliquer"><i class="fa fa-files-o"></i></a> ';
        tr += '<i class="fa fa-minus-circle pull-right cmdAction cursor" data-action="remove"></i></td>';
        
        tr += '</tr>';

        //_cmd.type = 'action';
        //_cmd.subType = 'message';
        $('#table_recogrammar tbody').append(tr);
        $('#table_recogrammar tbody tr:last').setValues(_cmd, '.cmdAttr');

            

    }
    else if (_cmd.configuration['anim'] == 'yes') {


        var tr = '<tr class="cmd" data-cmd_id="' + init(_cmd.id) + '" >';
        tr += '<td>';
        tr += '<span class="cmdAttr" data-l1key="id"></span>';
        tr += '<span class="cmdAttr" style="display : none;" data-l1key="type" ></span>';
        tr += '<span class="cmdAttr" style="display : none;" data-l1key="subType" ></span>';
        tr += '<span class="cmdAttr" style="display : none;" data-l1key="configuration" data-l2key="anim" ></span>';
        tr += '</td>';
        tr += '<td>';
        tr += '<div class="col-sm-6">';
        tr += '<input class="cmdAttr form-control input-sm" data-l1key="name">';
        tr += '</div>';
        tr += '</td>';
        tr += '<td>';
        tr += '<select class="cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="model" style="margin-top : 5px;" >';
        tr += '<option value="Taichi">{{Taichi}}</option>';
        tr += '<option value="Satori">{{Satori}}</option>';
        tr += '<option value="CyberSoldier">{{Cyber}}</option>';
        tr += '</select>';
        tr += '</td>';
        tr += '<td><input class="cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="look">';
        tr += '</td>';
        tr += '<td><input class="cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="status" placeholder="1" >';
        tr += '</td>';
        tr += '<td><input class="cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="animation">';
        tr += '</td>';
        tr += '<td><textarea style="height : 45px;" class="cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="parameter"></textarea>';
        tr += '</td>';
        tr += '<td>';
        if (is_numeric(_cmd.id)) {
            tr += '<a class="btn btn-default btn-xs cmdAction" data-action="configure"><i class="fa fa-cogs"></i></a> ';
            tr += '<a class="btn btn-default btn-xs cmdAction" data-action="test"><i class="fa fa-rss"></i> {{Play}}</a>';
        }
        tr += ' <a class="btn btn-default btn-xs cmdAction" data-action="copy" title="Dupliquer"><i class="fa fa-files-o"></i></a> ';
        tr += '<i class="fa fa-minus-circle pull-right cmdAction cursor" data-action="remove"></i></td>';

        tr += '</tr>';

        //_cmd.type = 'action';
        //_cmd.subType = 'message';
        $('#table_cmdAnims tbody').append(tr);
        $('#table_cmdAnims tbody tr:last').setValues(_cmd, '.cmdAttr');
        if (_cmd.name != undefined) {

            var _myeq = jeedom.eqLogic.byId(_cmd.eqLogic_id);

            jeedom.eqLogic.byId({
                id: _cmd.eqLogic_id,
                error: function (error) {
                    $('#div_alert').showAlert({ message: error.message, level: 'danger' });
                },
                success: function (equip) {

                    if (_cmd.name == equip.configuration['anim_warn'])
                        $('#sel_anim_warn').append('<option selected value="' + _cmd.name + '">' + _cmd.name + '</option>');
                    else
                        $('#sel_anim_warn').append('<option value="' + _cmd.name + '">' + _cmd.name + '</option>');
                    if (_cmd.name == equip.configuration['anim_start'])
                        $('#sel_anim_start').append('<option selected value="' + _cmd.name + '">' + _cmd.name + '</option>');
                    else
                        $('#sel_anim_start').append('<option value="' + _cmd.name + '">' + _cmd.name + '</option>');
                    if (_cmd.name == equip.configuration['anim_idle'])
                        $('#sel_anim_idle').append('<option selected value="' + _cmd.name + '">' + _cmd.name + '</option>');
                    else
                        $('#sel_anim_idle').append('<option value="' + _cmd.name + '">' + _cmd.name + '</option>');
                    if (_cmd.name == equip.configuration['anim_say'])
                        $('#sel_anim_say').append('<option selected value="' + _cmd.name + '">' + _cmd.name + '</option>');
                    else
                        $('#sel_anim_say').append('<option value="' + _cmd.name + '">' + _cmd.name + '</option>');


                }
            });


           
        }
    }
    
}



function loadScriptFile(_path) {
    $.hideAlert();
    var result = false;
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // méthode de transmission des données au fichier php
        url: "plugins/avatar/core/ajax/avatar.ajax.php", // url du fichier php
        data: {
            action: "getScriptContent",
            path: _path,
        },
        dataType: 'json',
        async: false,
        error: function (request, status, error) {
            handleAjaxError(request, status, error, $('#div_alert'));
        },
        success: function (data) { // si l'appel a bien fonctionné
            if (data.state != 'ok') {
                $('#div_alert').showAlert({ message: data.result, level: 'danger' });
                return false;
            }
            result = data.result;
            switch (result.extension) {
                case 'php':
                    result.mode = 'text/x-php';
                    break;
                case 'sh':
                    result.mode = 'shell';
                    break;
                case 'pl':
                    result.mode = 'text/x-php';
                    break;
                case 'py':
                    result.mode = 'text/x-python';
                    break;
                case 'rb':
                    result.mode = 'text/x-ruby';
                    break;
                default:
                    result.mode = 'text/x-php';
                    break;
            }
        }
    });
    return result;
}

function saveScriptFile(_path, _content) {
    $.hideAlert();
    var success = false;
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // méthode de transmission des données au fichier php
        url: "plugins/avatar/core/ajax/avatar.ajax.php", // url du fichier php
        data: {
            action: "saveScriptContent",
            path: _path,
            content: _content,
        },
        dataType: 'json',
        async: false,
        error: function (request, status, error) {
            handleAjaxError(request, status, error, $('#div_editScriptFileAlert'));
        },
        success: function (data) { // si l'appel a bien fonctionné
            if (data.state != 'ok') {
                $('#div_editScriptFileAlert').showAlert({ message: data.result, level: 'danger' });
                return;
            }
            success = true;
            $('#div_alert').showAlert({ message: 'Script sauvegardé', level: 'success' });
        }
    });
    return success;
}

function addUserScript(_name) {
    $.hideAlert();
    var success = false;
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // méthode de transmission des données au fichier php
        url: "plugins/avatar/core/ajax/avatar.ajax.php", // url du fichier php
        data: {
            action: "addUserScript",
            name: _name,
        },
        dataType: 'json',
        async: false,
        error: function (request, status, error) {
            handleAjaxError(request, status, error, $('#div_newUserScriptAlert'));
        },
        success: function (data) { // si l'appel a bien fonctionné
            if (data.state != 'ok') {
                $('#div_newUserScriptAlert').showAlert({ message: data.result, level: 'danger' });
                return;
            }
            success = data.result;
        }
    });
    return success;
}

function removeScript(_path) {
    $.hideAlert();
    var success = false;
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // méthode de transmission des données au fichier php
        url: "plugins/avatar/core/ajax/avatar.ajax.php", // url du fichier php
        data: {
            action: "removeScript",
            path: _path,
        },
        dataType: 'json',
        async: false,
        error: function (request, status, error) {
            handleAjaxError(request, status, error, $('#div_newUserScriptAlert'));
        },
        success: function (data) { // si l'appel a bien fonctionné
            if (data.state != 'ok') {
                $('#div_newUserScriptAlert').showAlert({ message: data.result, level: 'danger' });
                return;
            }
            $('#div_alert').showAlert({ message: 'Script supprimé', level: 'success' });
            success = true;
        }
    });
    return success;
}


