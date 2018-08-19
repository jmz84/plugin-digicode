<?php

require_once dirname(__FILE__).'/../../../../core/php/core.inc.php';

class digicode extends eqLogic {

  public static function modifyUserCode($id,$cmdid,$code,$cmdName)
  {
    $digicode    = eqLogic::byId($id);
    $cmd = new digicodeCmd();
    $cmd->setName($cmdName);
    $cmd->setid($cmdid);
    $cmd->setEqLogic_id($id);
    $cmd->setType('info');
    $cmd->setSubType('numeric');
    $cmd->setConfiguration('userCode', $code);
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

  public static function AddUser($id,$user,$code)
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
    $digicodeCmd->save();
  }

  public static function checkandactivate($mode,$eqLogic)
  {
    $etatAlarme =  cmd::byString($eqLogic->getConfiguration('digicodeCmdStatus'));
    $valueEtatAlarme  = $etatAlarme->execCmd();
    $etatFenetres = cmd::byString($eqLogic->getConfiguration('digicodeEtatFenetres'));
    $valueEtatFenetres  = $etatFenetres->execCmd();
    $etatPortes = cmd::byString($eqLogic->getConfiguration('digicodeEtatPortes'));
    $valueEtatPorte  = $etatPortes->execCmd();


    if($valueEtatFenetres == 1 && $valueEtatPorte == 0){
      log::add('digicode', 'DEBUG', 'Au moins une fenêtre est restée ouverte !');
      $eqLogic->checkAndUpdateCmd('message', 'Fenetre(s) ouverte(s)');
      $eqLogic->save();
    }elseif($valueEtatPorte == 1 && $valueEtatFenetres == 0){
      log::add('digicode', 'DEBUG', 'Au moins une porte est restée ouverte !');
      $eqLogic->checkAndUpdateCmd('message', 'Porte(s) ouverte(s)');
      $eqLogic->save();
    }elseif($valueEtatPorte == 1 && $valueEtatFenetres == 1){
      log::add('digicode', 'DEBUG', 'Au moins une porte est restée ouverte !');
      $eqLogic->checkAndUpdateCmd('message', 'Porte(s) et Fenêtre(s) ouverte(s)');
      $eqLogic->save();
    }elseif($valueEtatAlarme ==1){
      log::add('digicode', 'DEBUG', 'Alarme déjà active !');
      $eqLogic->checkAndUpdateCmd('message', 'Alarme déjà active');
      $eqLogic->save();
    }else{
      log::add('digicode', 'DEBUG', 'Tout est ok pour l\'activation');
      $eqLogic->checkAndUpdateCmd('message', '');
      $eqLogic->save();
      return true;
    }

  }


  public function postUpdate()
  {
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

    $cmdEtatFenetres = cmd::byString($this->getConfiguration('digicodeEtatFenetres'));
    $valueEtatFenetres  = $cmdEtatFenetres->execCmd();
    $replace['#EtatFenetres#'] = $valueEtatFenetres;
    $cmdEtatPortes = cmd::byString($this->getConfiguration('digicodeEtatPortes'));
    $valueEtatPortes  = $cmdEtatPortes->execCmd();
    $replace['#EtatPortes#'] = $valueEtatPortes;
    $valuedigicodeDelais = $this->getConfiguration('digicodeDelais');
    $replace['#digicodeDelais#'] = $valuedigicodeDelais;


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
    }

}
/********** Getters and setters **********/

}
