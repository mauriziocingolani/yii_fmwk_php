<?php

/**
 * Fornisce una classe di appoggio per il meccanismo di autenticazione.
 * Se richiesto fa in modo che i dati dell'utente loggato vengano sempre
 * ricaricati da database, anzichè essere "congelati" al momento del login.
 * 
 * @author Maurizio Cingolani <m.cingolani@ggfgroup.it>
 * @version 2.2
 * @category YiiFramework
 */
abstract class WebUser extends CWebUser {

    /** Se true fa in modo che i dati dell'utente loggato vengano sempre ricaricati da database. */
    public $fetchFromDb = false;

    /**
     * Si limita a verificare che l'utente sia ancora connesso (ovvero che nel frattempo
     * non sia scaduta la sessione) prima di restituire la condizione di appartenenza
     * al ruolo.
     * @param boolean $condition Condizione di appartenenza a un certo ruolo
     * @return boolean True o False a seconda della veridicità della condizione
     */
    protected function checkUserLogged() {
        if ($this->isGuest)
            Yii::app()->request->redirect(Yii::app()->homeUrl);
    }

    /**
     * Se la proprietà richiesta è 'user' (oggetto che rappresenta l'utente loggato) e se esplicitamente
     * richiesto tramite la proprietà {@link WebUser::$fetchFromDb}, restituisce l'oggetto User dopo
     * averlo ricaricato da databse, invece di quello creato al momento del login.
     * 
     * @param string $name Nome della proprietà della classe.
     * @return mixed Proprietà richiesta
     */
    public function __get($name) {
        if ($this->fetchFromDb && $name == 'user') :
            return User::ReadRecord($this->id);
        endif;
        return parent::__get($name);
    }

}

/*End of file WebUser.php */
