
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
    var _cmd = { type: 'action' };
    _cmd.configuration = { subtype: 'grammar' };
    addCmdToTable(_cmd);
});

$("#bt_addAnimation").on('click', function (event) {
    var _cmd = { type: 'action' };
    _cmd.configuration = { subtype: 'anim' };
    addCmdToTable(_cmd);
});


$("#md_browseScriptFile").dialog({
    autoOpen: false,
    modal: true,
    height: (jQuery(window).height() - 150),
});

$('#table_recogrammar tbody').delegate('tr .remove', 'click', function (event) {
    $(this).closest('tr').remove();
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
				$('#div_alert').showAlert({message: reply.result, level: 'danger'});
			else
				$('#div_alert').showAlert({message: '{{Test de connection réussi}}', level: 'success'});
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



function saveEqLogic(_eqLogic) {

    	
	return _eqLogic;
	
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

  //  if (_cmd.configuration['subtype'] == 'grammar') {
    
        var tr = '<tr class="cmd" data-cmd_id="' + init(_cmd.id) + '">';

        tr += '<td>';
        tr += '<input class="cmdAttr form-control input-sm" data-l1key="id"  style="display : none;">';
        tr += '<div class="row">';
        tr += '<div class="col-sm-6">';
        tr += '<input class="cmdAttr form-control input-sm" data-l1key="name">';
        tr += '<input class="cmdAttr form-control input-sm" data-l1key="type" style="display : none;">';
        tr += '<input class="cmdAttr form-control input-sm" data-l1key="subType" style="display : none;">';
        tr += '<input class="cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="subtype" style="display : none;">';
        tr += '</div>';
        tr += '</div>';
        tr += '</td>';
        tr += '<td>';
        tr += '<center>';
        tr += '<span><label class="checkbox-inline"><input type="checkbox" class="cmdAttr" data-l1key="configuration" data-l2key="status"/>{{Active}}</label></span> ';
        tr += '</center>';
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
            tr += '<a class="btn btn-default btn-xs cmdAction" data-action="test"><i class="fa fa-rss"></i> {{Tester}}</a>';
        }
        tr += ' <a class="btn btn-default btn-xs cmdAction" data-action="copy" title="Dupliquer"><i class="fa fa-files-o"></i></a> ';
        tr += '<i class="fa fa-minus-circle pull-right cmdAction cursor" data-action="remove"></i></td>';
        tr += '</tr>';

        $('#table_recogrammar tbody').append(tr);
        $('#table_recogrammar tbody tr:last').setValues(_cmd, '.cmdAttr');


        _cmd.type = 'action';
        _cmd.subType = 'message';
        _cmd.configuration['subtype'] == 'grammar';
        $('#table_recogrammar tbody tr:last .cmdAttr[data-l1key=type]').value('Action');

        var tr = $('#table_recogrammar tbody tr:last');
        jeedom.eqLogic.builSelectCmd({
            id: $(".li_eqLogic.active").attr('data-eqLogic_id'),
            filter: { type: 'info' },
            error: function (error) {
                $('#div_alert').showAlert({ message: error.message, level: 'danger' });
            },
            success: function (result) {
                tr.find('.cmdAttr[data-l1key=value]').append(result);
                tr.find('.cmdAttr[data-l1key=configuration][data-l2key=updateCmdId]').append(result);
                tr.setValues(_cmd, '.cmdAttr');
                jeedom.cmd.changeType(tr, init(_cmd.subType));
                initTooltips();
            }
        });

//    }
//    else if (_cmd.configuration['subtype'] == 'anim') {
//  }
    
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





function printEqLogic(_eqLogic) {

   
}
 


