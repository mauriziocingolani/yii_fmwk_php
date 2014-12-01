<?php

/**
 * Suprclass per i modelli della tabella 'users'. Aggiunge agli oggetti
 * di tipo 'persona' le proprietà tipiche degli utenti e alcuni metodi
 * per l'autenticazione.
 *
 * @author Maurizio Cingolani
 * @version 1.1.1
 */
abstract class AbstractUserObject extends AbstractPersonObject {
    /* ID utente */

    public $UserID;

    /** Ruolo */
    public $RoleID;

    /** Nome utente */
    public $UserName;

    /** Sesso */
    public $Gender;

    /** Password */
    public $Password;

    /** Password criptata */
    public $EncryptedPassword;

    /** Abilitato */
    public $Enabled;

    /**
     * Verifica se la password coincide con quella dell'utente.
     * Se il record contiene la password criptata viene usato il meccanismo
     * di decrittazione, altrimente quello normale.
     * 
     * NOTA: per criptare la password con CSecurityManager occorre usare
     * la funzione base64_encode prima di salvare nel database. Inoltre deve
     * essere stato impostato il parametro 'encryptionKey' nella configurazione.
     * 
     * @param string $password Password da verificare
     * @return boolean True se la password coincide con quella dell'utente
     */
    public function comparePassword($password) {
        if ($this->EncryptedPassword) :
            return $password === Yii::app()->securityManager->decrypt(base64_decode($this->EncryptedPassword), Yii::app()->params['encryptionKey']);
        endif;
        return CPasswordHelper::verifyPassword($password, $this->Password);
    }

    public function getGenderSuffix() {
        return $this->isMale() ? 'o' : 'a';
    }

    /**
     * Restituisce true se l'utente è di sesso maschile o se la proprietà
     * {@link Gender} non è impostata.
     * @return boolean True se l'utente è di sesso maschile
     */
    public function isMale() {
        return $this->Gender === 'M' || !isset($this->Gender);
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
