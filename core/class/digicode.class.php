<?php

require_once dirname(__FILE__).'/../../../../core/php/core.inc.php';

class digicode extends eqLogic {

    public static function modifyUserCode($id,$cmdid,$code,$master,$cmdName)
    {
        $digicode    = eqLogic::byId($id);
        $cmd = new digicodeCmd();
        $cmd->setName($cmdName);
        $cmd->setid($cmdid);
        $cmd->setEqLogic_id($id);
        $cmd->setType('info');
        $cmd->setSubType('numeric');
        $cmd->setConfiguration('userCode', $code);
        $cmd->setConfiguration('masterCode', $master);
        $cmd->save();
    }

    public static function RemoveUser($id,$cmdid,$cmdName)
    {
        $cmd = cmd::byId($cmdid);
        if (!is_object($cmd)) {
            throw new Exception('User ID inconnu');
        }
        $cmd->remove();

    }

    public static function AddUser($id,$user,$code,$master)
    {
        $digicode    = eqLogic::byId($id);
        $digicodeCmd = $digicode->getCmd(null, $user);
        if (!is_object($digicodeCmd)) {
            $digicodeCmd = new digicodeCmd();
            $digicodeCmd->setName(__($user, __FILE__));
            $digicodeCmd->setEqLogic_id($digicode->getId());
            $digicodeCmd->setType('info');
            $digicodeCmd->setSubType('numeric');
        }
        $digicodeCmd->setOrder(99);
        $digicodeCmd->setConfiguration('userCode', $code);
        $digicodeCmd->setConfiguration('masterCode', $master);
        $digicodeCmd->save();
    }

    public static function checkandactivate($mode,$eqLogic)
    {
        $etatAlarme =  cmd::byString($eqLogic->getConfiguration('digicodeCmdStatus'));
        $valueEtatAlarme  = $etatAlarme->execCmd();
        $invertEtatFenetres = $eqLogic->getConfiguration('invertdigicodeEtatFenetres');
        $invertEtatPortes = $eqLogic->getConfiguration('invertDigicodeEtatPortes');
        $ActivateEtatPortes = $eqLogic->getConfiguration('ActivateEtatPortes');
        $ActivateEtatFenetres = $eqLogic->getConfiguration('ActivateEtatFenetres');
      	$Name = $eqLogic->getName();

        if (!empty($ActivateEtatPortes)) {
        $etatPortes = cmd::byString($eqLogic->getConfiguration('digicodeEtatPortes'));
        $valueEtatPortes  = $etatPortes->execCmd();
            if (isset($invertEtatPortes) && $invertEtatPortes == 1) {
                $valueEtatPortes = ($valueEtatPortes == 1 || $valueEtatPortes) ? 0 : 1;
            }
        }


        if (!empty($ActivateEtatFenetres)) {
        $etatFenetres = cmd::byString($eqLogic->getConfiguration('digicodeEtatFenetres'));
        $valueEtatFenetres  = $etatFenetres->execCmd();
            if (isset($invertEtatFenetres) && $invertEtatFenetres == 1) {
                $valueEtatFenetres = ($valueEtatFenetres == 1 || $valueEtatFenetres) ? 0 : 1;
            }
        }

        if($valueEtatFenetres == 1 && $valueEtatPortes == 0){
            log::add('digicode', 'DEBUG', 'Au moins une fenêtre est restée ouverte !');
            $eqLogic->checkAndUpdateCmd('message', 'Fenetre(s) ouverte(s)');
            $eqLogic->save();
        }elseif($valueEtatPortes == 1 && $valueEtatFenetres == 0){
            log::add('digicode', 'DEBUG', 'Au moins une porte est restée ouverte !');
            $eqLogic->checkAndUpdateCmd('message', 'Porte(s) ouverte(s)');
            $eqLogic->save();
        }elseif($valueEtatPortes == 1 && $valueEtatFenetres == 1){
            log::add('digicode', 'DEBUG', 'Au moins une porte est restée ouverte !');
            $eqLogic->checkAndUpdateCmd('message', 'Porte(s) et Fenêtre(s) ouverte(s)');
            $eqLogic->save();
        }elseif($valueEtatAlarme ==1){
            log::add('digicode', 'DEBUG', 'Alarme déjà active !');
            $eqLogic->checkAndUpdateCmd('message', 'Alarme déjà active');
            $eqLogic->save();
        }else{
            log::add('digicode', 'DEBUG', 'Tout est ok pour l\'activation');
            return true;
        }
    }


    public function postUpdate()
    {
        $digicodeCmd = $this->getCmd(null, 'Mode désactivé');
        if (!is_object($digicodeCmd)) {
            $digicodeCmd = new digicodeCmd();
            $digicodeCmd->setName(__('Mode désactivé', __FILE__));
            $digicodeCmd->setEqLogic_id($this->getId());
            $digicodeCmd->setIsVisible(0);
            $digicodeCmd->setLogicalId('Mode désactivé');
            $digicodeCmd->setType('action');
            $digicodeCmd->setSubType('default');

        }
        $digicodeCmd->setConfiguration('type', 'cmdwiget');
        $digicodeCmd->save();

        $digicodeCmd = $this->getCmd(null, 'Mode partiel');
        if (!is_object($digicodeCmd)) {
            $digicodeCmd = new digicodeCmd();
            $digicodeCmd->setName(__('Mode partiel', __FILE__));
            $digicodeCmd->setEqLogic_id($this->getId());
            $digicodeCmd->setIsVisible(0);
            $digicodeCmd->setLogicalId('Mode partiel');
            $digicodeCmd->setType('action');
            $digicodeCmd->setSubType('default');

        }
        $digicodeCmd->setConfiguration('type', 'cmdwiget');
        $digicodeCmd->save();

        $digicodeCmd = $this->getCmd(null, 'Mode total');
        if (!is_object($digicodeCmd)) {
            $digicodeCmd = new digicodeCmd();
            $digicodeCmd->setName(__('Mode total', __FILE__));
            $digicodeCmd->setEqLogic_id($this->getId());
            $digicodeCmd->setIsVisible(0);
            $digicodeCmd->setLogicalId('Mode total');
            $digicodeCmd->setType('action');
            $digicodeCmd->setSubType('default');

        }
        $digicodeCmd->setConfiguration('type', 'cmdwiget');
        $digicodeCmd->save();

        $digicodeCmd = $this->getCmd(null, 'exec alarme');
        if (!is_object($digicodeCmd)) {
            $digicodeCmd = new digicodeCmd();
            $digicodeCmd->setName(__('exec alarme', __FILE__));
            $digicodeCmd->setEqLogic_id($this->getId());
            $digicodeCmd->setIsVisible(0);
            $digicodeCmd->setLogicalId('exec alarme');
            $digicodeCmd->setType('action');
            $digicodeCmd->setSubType('message');

        }
        $digicodeCmd->setConfiguration('type', 'cmdwiget');
        $digicodeCmd->save();

        $digicodeCmd = $this->getCmd(null, 'save code');
        if (!is_object($digicodeCmd)) {
            $digicodeCmd = new digicodeCmd();
            $digicodeCmd->setName(__('save code', __FILE__));
            $digicodeCmd->setEqLogic_id($this->getId());
            $digicodeCmd->setIsVisible(0);
            $digicodeCmd->setLogicalId('save code');
            $digicodeCmd->setType('action');
            $digicodeCmd->setSubType('message');
        }
        $digicodeCmd->setConfiguration('type', 'cmdwiget');
        $digicodeCmd->save();

        $digicodeCmd = $this->getCmd(null, 'etat');
        if (!is_object($digicodeCmd)) {
            $digicodeCmd = new digicodeCmd();
            $digicodeCmd->setName(__('etat', __FILE__));
            $digicodeCmd->setEqLogic_id($this->getId());
            $digicodeCmd->setIsVisible(0);
            $digicodeCmd->setLogicalId('etat');
            $digicodeCmd->setType('info');
            $digicodeCmd->setSubType('numeric');
        }
        $digicodeCmd->setConfiguration('type', 'cmdwiget');
        $digicodeCmd->save();

        $digicodeCmd = $this->getCmd(null, 'message');
        if (!is_object($digicodeCmd)) {
            $digicodeCmd = new digicodeCmd();
            $digicodeCmd->setName(__('message', __FILE__));
            $digicodeCmd->setEqLogic_id($this->getId());
            $digicodeCmd->setIsVisible(0);
            $digicodeCmd->setLogicalId('message');
            $digicodeCmd->setType('info');
            $digicodeCmd->setSubType('string');
        }
        $digicodeCmd->setConfiguration('type', 'cmdwiget');
        $digicodeCmd->save();

        $digicodeCmd = $this->getCmd(null, 'codemaitre');
        if (!is_object($digicodeCmd)) {
            $digicodeCmd = new digicodeCmd();
            $digicodeCmd->setName(__('codemaitre', __FILE__));
            $digicodeCmd->setEqLogic_id($this->getId());
            $digicodeCmd->setIsVisible(0);
            $digicodeCmd->setLogicalId('codemaitre');
            $digicodeCmd->setType('info');
            $digicodeCmd->setSubType('numeric');
        }
        $digicodeCmd->setConfiguration('type', 'cmdwiget');
        $digicodeCmd->save();
        /********** Getters and setters **********/

    }


    public function toHtml($_version = 'dashboard')
    {
        $replace = $this->preToHtml($_version);
        if (!is_array($replace)) {
            return $replace;
        }
        $version = jeedom::versionAlias($_version);
        foreach ($this->getCmd('info') as $cmd) {
            $replace['#' . $cmd->getLogicalId() . '#']    = $cmd->execCmd();
            $replace['#' . $cmd->getLogicalId() . '_id#'] = $cmd->getId();
            if ($cmd->getIsHistorized() == 1) {
                $replace['#' . $cmd->getLogicalId() . '_history#'] = 'history cursor';
            }
        }

        foreach ($this->getCmd('action') as $cmd) {
            $replace['#' . $cmd->getLogicalId() . '_id#'] = $cmd->getId();
        }

        $ActivateEtatFenetres = $this->getConfiguration('ActivateEtatFenetres');
        if (isset($ActivateEtatFenetres) && $ActivateEtatFenetres == 1) {
            $invertEtatFenetres = $this->getConfiguration('invertdigicodeEtatFenetres');
            $cmdEtatFenetres = cmd::byString($this->getConfiguration('digicodeEtatFenetres'));
            $valueEtatFenetres  = $cmdEtatFenetres->execCmd();
            if (isset($invertEtatFenetres) && $invertEtatFenetres == 1) {
                $valueEtatFenetres = ($valueEtatFenetres == 1 || $valueEtatFenetres) ? 0 : 1;
            }

            $replace['#EtatFenetres#'] = $valueEtatFenetres;
        }

        $ActivateEtatPortes = $this->getConfiguration('ActivateEtatPortes');
        if (isset($ActivateEtatPortes) && $ActivateEtatPortes == 1) {
            $invertEtatPortes = $this->getConfiguration('invertDigicodeEtatPortes');
            $cmdEtatPortes = cmd::byString($this->getConfiguration('digicodeEtatPortes'));
            $valueEtatPortes  = $cmdEtatPortes->execCmd();
            if (isset($invertEtatPortes) && $invertEtatPortes == 1) {
                $valueEtatPortes = ($valueEtatPortes == 1 || $valueEtatPortes) ? 0 : 1;
            }


            $replace['#EtatPortes#'] = $valueEtatPortes;
        }





        $valuedigicodeDelais = $this->getConfiguration('digicodeDelais');
        $replace['#digicodeDelais#'] = $valuedigicodeDelais;

        $version = $_version;
        log::add('digicode', 'debug', 'toHtml version: '.$version);
        if ($_version == 'dplan')
        {
            $replace['#background-color#'] = $this->getConfiguration('designBckColor', 'rgba(128, 128, 128, 0)');

        }
        return $this->postToHtml($_version, template_replace($replace, getTemplate('core', $version, 'digicode', 'digicode')));
    }



}
class digicodeCmd extends cmd {


    public function execute($_options = null)
    {
        $eqLogic = $this->getEqLogic();
        $eqLogics = eqLogic::byType('digicode');
        $cmdAlarmeDesactive = $eqLogic->getConfiguration('digicodeCmdDesactivation');

        switch ($this->getLogicalId()) {

            case 'save code':
            $value    = $_options['message'];
            $mode   = substr($value, -1, 1);
            $code    = substr($value, 0, 4);

            foreach ($eqLogics as $digicode) {
                if ($digicode->getIsEnable() == 1) {
                    foreach ($digicode->getCmd('info') as $cmd) {
                        if(!empty($cmd->getConfiguration('userCode')) && $cmd->getConfiguration('userCode') == $value && $cmd->getConfiguration('masterCode') == 1){
                            $cmd_virt = $eqLogic->getCmd(null, 'codemaitre');

                            $cmd_value = $cmd_virt->execCmd();
                            log::add('digicode', 'DEBUG', 'test : '. $cmd_value);
                            if($cmd_value == 1){
                                log::add('digicode', 'DEBUG', 'MasterCode !!!');
                                $eqLogic->checkAndUpdateCmd('codemaitre', '0');
                                $eqLogic->save();
                                $eqLogic->checkAndUpdateCmd('message', 'Désactivation du code maitre');
                                $eqLogic->save();
                            }else{
                                log::add('digicode', 'DEBUG', 'MasterCode !!!');
                                $eqLogic->checkAndUpdateCmd('codemaitre', '1');
                                $eqLogic->save();
                                $eqLogic->checkAndUpdateCmd('message', 'Activation du code maitre');
                                $eqLogic->save();
                            };
                            break;
                        };
                        if (!empty($cmd->getConfiguration('userCode')) && $cmd->getConfiguration('userCode') == $code){
                            switch ($mode) {
                                case 'P':
                                $returnCheck = $eqLogic->checkandactivate($mode,$eqLogic);
                                if( $returnCheck ==1){
                                    log::add('digicode', 'DEBUG', 'Activation de l\'alarme partielle par l\'utilisateur '. $cmd->getName() . ' (Code utilisé ' .$cmd->getConfiguration('userCode') .')' );
                                    return 2;
                                }
                                break;

                                case 'T':
                                $returnCheck =$eqLogic->checkandactivate($mode,$eqLogic);
                                if( $returnCheck ==1){
                                    log::add('digicode', 'DEBUG', 'Activation de l\'alarme totale par l\'utilisateur '. $cmd->getName() . ' (Code utilisé ' .$cmd->getConfiguration('userCode') .')' );
                                    return 1;
                                }
                                break;

                                case 'D':
                                log::add('digicode', 'DEBUG', 'Désactivation de l\'alarme par l\'utilisateur '. $cmd->getName() . ' (Code utilisé ' .$cmd->getConfiguration('userCode') .')' );
                                $cmd2 = cmd::byString($cmdAlarmeDesactive);
                                $cmd2->execCmd();
                                $eqLogic->checkAndUpdateCmd('etat', '0');
                                $eqLogic->save();
                                $eqLogic->checkAndUpdateCmd('message', '');
                                $eqLogic->save();
                                return 0;
                                break;

                            }
                        } elseif (!empty($cmd->getConfiguration('userCode')) && $cmd->getConfiguration('userCode') != $code){
                            log::add('digicode', 'DEBUG', 'Code erroné');
                            $eqLogic->checkAndUpdateCmd('message', 'Code erroné');
                            $eqLogic->save();
                        }
                    }
                }
            }
            break;

            case 'exec alarme':
            $value    = $_options['message'];
            $eqLogic = $this->getEqLogic();
            $cmdAlarmetotal = $eqLogic->getConfiguration('digicodeCmdTotal');
            $cmdAlarmePartiel = $eqLogic->getConfiguration('digicodeCmdPartiel');

            if($value == 1){
                $cmd = cmd::byString($cmdAlarmetotal);
                $cmd->execCmd();
                $eqLogic->checkAndUpdateCmd('etat', '1');
                $eqLogic->save();
            }
            if($value == 2){

                $cmd = cmd::byString($cmdAlarmePartiel);
                $cmd->execCmd();
                $eqLogic->checkAndUpdateCmd('etat', '2');
                $eqLogic->save();
            }
            break;

            case 'Mode désactivé':
            $eqLogic->checkAndUpdateCmd('etat', '0');
            $eqLogic->save();
            break;

            case 'Mode partiel':
            $eqLogic->checkAndUpdateCmd('etat', '2');
            $eqLogic->save();
            break;

            case 'Mode total':
            $eqLogic->checkAndUpdateCmd('etat', '1');
            $eqLogic->save();
            break;
        }



    }
    /********** Getters and setters **********/

}
