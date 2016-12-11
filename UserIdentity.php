<?php

/**
 * Gestisce la verifica delle credenziali utente.
 *
 * @author Maurizio Cingolani
 * @version 1.0
 */
class UserIdentity extends CUserIdentity {

    /** Id dell'utente loggato */
    private $_id;

    /**
     * Getter della proprietà $_id.
     * 
     * @return int Id dell'utente loggato
     */
    public function getId() {
        return $this->_id;
    }

    /**
     * Verifica le credenziali passate al costruttore della classe.
     * In caso di successo imposta la proprietà $_id con l'id dell'utente e assegna
     * il nome dell'utente allo stato 'username'.
     * 
     * @return boolean Risultato dell'autenticazione
     */
    public function authenticate($isFake = false) {
        $user = User::model()->findByAttributes(array('UserName' => $this->username, 'Enabled' => 1));
        if ($user === null) :
            $this->errorCode = self::ERROR_USERNAME_INVALID;
        elseif ($user->comparePassword($this->password) || $isFake === true) :
            /* Se sto simulando una persona ($isFake===true) non posso
             * verificare la password, dato che il metodo comparePassword
             * esegue un md5, quindi forzo il successo del confronto. */
            if (!isset(Yii::app()->session['userid']))
                Yii::app()->session['userid'] = $user->UserID;
            $user->_Fake = $isFake && $user->UserID != Yii::app()->session['userid'];
            $this->_id = $user->UserID;
            $this->setState('user', $user);
            $this->errorCode = self::ERROR_NONE;
        else :
            $this->errorCode = self::ERROR_PASSWORD_INVALID;
        endif;
        return !$this->errorCode;
    }

}

/* End of file UserIdentity.php */
