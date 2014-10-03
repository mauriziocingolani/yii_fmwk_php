<?php

/**
 * Suprclass per i modelli della tabella 'users'. Aggiunge agli oggetti
 * di tipo 'persona' le proprietà tipiche degli utenti e alcuni metodi
 * per l'autenticazione.
 *
 * @author Maurizio Cingolani
 * @version 1.0
 */
abstract class AbstractUserObject extends AbstractPersonObject {
    /* ID utente */

    public $UserID;

    /** Ruolo */
    public $RoleID;

    /** Nome utente */
    public $UserName;

    /** Password */
    public $Password;

    /** Abilitato */
    public $Enabled;

    /**
     * Verifica se la password coincide con quella dell'utente.
     * 
     * @param string $password Password da verificare
     * @return boolean True se la password coincide con quella dell'utente
     */
    public function comparePassword($password) {
        return CPasswordHelper::verifyPassword($password, $this->Password);
    }

    /**
     * Registra il logout di un utente dall'applicazone.
     * Semplice wrapper per il metodo {@link Login::UpdateRecord} al quale viene passato tra i parametri
     * l'ID dell'utente attuale.
     */
    public function setLogout() {
        try {
            Login::UpdateRecord(null, array('UserID' => $this->UserID));
        } catch (CDbException $e) {
            return $e->getMessage();
        }
    }

}

/* End of file AbstractUserObject.php */
