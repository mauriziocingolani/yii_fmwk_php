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
    public function authenticate() {
        $user = User::model()->findByAttributes(array('UserName' => $this->username, 'Enabled' => 1));
        if ($user === null) :
            $this->errorCode = self::ERROR_USERNAME_INVALID;
        elseif ($user->comparePassword($this->password)) :
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
