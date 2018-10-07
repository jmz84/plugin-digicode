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

if (!isConnect()) {
    throw new Exception('{{401 - Accès non autorisé}}');
}
if (init('id') == '') {
    throw new Exception(__('L\'id ne peut etre vide', __FILE__));
}
$digicode = digicode::byId(init('id'));
if (!is_object($digicode)) {
    throw new Exception(__('L\'équipement est introuvable : ', __FILE__) . init('id'));
}
if ($digicode->getEqType_name() != 'digicode') {
    throw new Exception(__('Cet équipement n\'est pas de type motion : ', __FILE__) . $digicode->getEqType_name());
}
$id = init('id');
sendVarToJS('id', $id);
?>
<?php
echo '<table class="table table-bordered table-condensed" style="width: 100%">';
echo '    <thead>';
echo '        </tr>';
echo '            <th style="width: 10%">Code maître</th><th style="width: 40%">{{Utilisateur}}</th><th style="width: 35%">{{Code}}</th><th style="width: 25%"></th>';
echo '        </tr>';

foreach ($digicode->getCmd('info') as $cmd) {
    if($cmd->getName() != 'etat' && $cmd->getName() != 'message' && $cmd->getName() != 'codemaitre'){

        echo      '<tr class="cmd" data-cmd_id="' . $cmd->getname() . '">';
      echo '<td>';
   if($cmd->getConfiguration('masterCode') == 1){    
         echo '<input type="checkbox" checked = checked DISABLED/>';           
   }else{
             echo '<input type="checkbox" DISABLED/>';    
   }
      echo '</td>';
      echo '<td>';
        echo $cmd->getname();
        echo '</td>';
        echo '<td>';
        echo $cmd->getConfiguration('userCode');
        echo '</td>';
        echo '<td>';
        echo ' <a class="btn btn-success bt_Modify btn-xs" data-id="' . $cmd->getid() . '"><i class="fa fa-gear"></i></a></center>';
        echo ' <a class="btn btn-danger bt_Remove btn-xs" data-id="' . $cmd->getid() . '"><i class="fa fa-trash-o"></i></a></center>';
        echo '</td>';
        echo '</tr>';
    }
}

echo '    </thead>';
echo '</table>';
echo ' <div align="right">';
echo ' <a class="btn btn-success bt_Add btn-xs">Ajouter</a> ';
echo ' <a class="btn btn-danger bt_Cancel btn-xs">Annuler</a> ';
echo ' </div>';

?>

<script>

$('.bt_Cancel').on('click', function() {
    $('#md_modal').dialog("close");
});
$('.bt_Modify').on('click', function() {
    var cmdid = $(this).attr('data-id');
    var cmdName = $(this).closest('.cmd').attr('data-cmd_id');
    
        var code = code;
        bootbox.prompt("Code", function(code){

 bootbox.prompt({
  title: "Ce code est-il toujours un code maitre ?",
  inputType: 'select',
  inputOptions: [{
    text: 'Oui',
    value: '1',
  }, {
    text: 'Non',
    value: '2',
  }],
  callback: function(master) {        
        action = 'Modify';
        $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "plugins/digicode/core/ajax/digicode.ajax.php", // url du fichier php
        data: {
            action: action,
            id: id,
            cmdid:cmdid,
            cmdName:cmdName,
			code: code,
            master: master,           
        },
        dataType: 'json',
        error: function (request, status, error) {
            handleAjaxError(request, status, error);
        },
        success: function(data) {
            $('#md_modal').dialog("close");

            if (data.state != 'ok') {
                $('#div_alert').showAlert({message: data.result, level: 'danger'});
                return;
            }
        }
    });
    
  }    
 })   
});    
});

$('.bt_Remove').on('click', function() {
    var cmdid = $(this).attr('data-id');
    var cmdName = $(this).closest('.cmd').attr('data-cmd_id');
    action = 'Remove';
    $.ajax({// fonction permettant de faire de l'ajax
    type: "POST", // methode de transmission des données au fichier php
    url: "plugins/digicode/core/ajax/digicode.ajax.php", // url du fichier php
    data: {
        action: action,
        id: id,
        cmdid:cmdid,
        cmdName:cmdName,
    },
    dataType: 'json',
    error: function (request, status, error) {
        handleAjaxError(request, status, error);
    },
    success: function(data) { // si l'appel a bien fonctionné
    if (data.state != 'ok') {
        $('#div_alert').showAlert({message: data.result, level: 'danger'});
        return;
    }
    $('#md_modal').dialog("close");

}
});
});

$('.bt_Add').on('click', function() {
    var cmdid = $(this).attr('data-id');
    var cmdName = $(this).closest('.cmd').attr('data-cmd_id');
    bootbox.prompt("Utilisateur", function(user){

        var code = code;
        bootbox.prompt("Code", function(code){
 bootbox.prompt({
  title: "Ce code est-il un code maitre ?",
  inputType: 'select',
  inputOptions: [{
    text: 'Oui',
    value: '1',
  }, {
    text: 'Non',
    value: '2',
  }],
  callback: function(master) {         
          
            action = 'Add';
            $.ajax({// fonction permettant de faire de l'ajax
            type: "POST", // methode de transmission des données au fichier php
            url: "plugins/digicode/core/ajax/digicode.ajax.php", // url du fichier php
            data: {
                action: action,
                id: id,
                user:user,
                code: code,
              	master: master,
            },
            dataType: 'json',
            error: function (request, status, error) {
                handleAjaxError(request, status, error);
            },
            success: function(data) {
                $('#md_modal').dialog("close")

                if (data.state != 'ok') {
                    $('#div_alert').showAlert({message: data.result, level: 'danger'});
                    return;
                }
            }
        });
  }   
     });         
          
    });
});
});

</script>

<?php include_file('desktop', 'digicode', 'js', 'digicode');?>
<?php include_file('core', 'plugin.template', 'js');?>
