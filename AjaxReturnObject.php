<?php

/**
 * Questo oggetto viene di norma restituito dalle azioni che fanno da callback a chiamate Ajax.
 * Contengono le informazioni sull'eventuale errore che si è verificato e sui dati restituiti dalla
 * chiamata.
 *
 * @author Maurizio Cingolani
 * @version 1.0.2
 */
class AjaxReturnObject extends CComponent {

    /** Modalità data */
    const MODE_DATA = 0;

    /** Modalità id */
    const MODE_ID = 1;

    /** Indica se si è verificato un errore */
    public $error = false;

    /** Messaggio di errore o successo */
    public $message;

    /** Messaggio di errore 
     * @deprecated since version 1.0.2 
     */
    public $errorMessage;

    /** Dati restituiti dalla chiamata */
    public $data;

    /** Id restituito dalla chiamata */
    public $id;

    /**
     * Costruisce l'istanza della classe. Solleva un'eccezione 400 se invocata al di fuori di una richiesta Ajax.
     * Se viene passato il primo parametro $result, allora in base alla modalità scelta con il secondo parametro $mode
     * viene popolata la proprietà $data oppure $id.
     * 
     * @param mixed $result Esito della chiamata (array o intero)
     * @param integer $mode Modalità di ritorno (data o id)
     * @throws CHttpException 400 se la richiesta non è di tio Ajax
     */
    public function __construct($result = null, $mode = self::MODE_DATA) {
        if (!Yii::app()->request->isAjaxRequest)
            throw new CHttpException(400, 'Richiesta non valida.');
        if (isset($result))
            $mode === self::MODE_ID ? $this->setErrorOrId($result) : $this->setErrorOrData($result);
    }

    /**
     * Imposta la proprietà $errorMessage con il messaggio di errore e imposta a true la proprietà $error. 
     * 
     * @param string $message Messaggio di errore
     */
    public function setErrorMessage($message) {
        if ($message !== null && strlen($message) > 0) :
            $this->error = true;
            $this->message = $message;
            $this->errorMessage = $message;
        endif;
    }

    /**
     * Imposta la proprietà $message con il messaggio di successo e per sicurezza
     * imposta a false la proprietà $error.
     * @param string $message Messaggio di successo
     */
    public function setSuccessMessage($message) {
        if ($message !== null && strlen($message) > 0) :
            $this->error = false;
            $this->message = $message;
        endif;
    }

    /**
     * Se il paramentro $result non è una stringa lo assegna alla proprietà $data,
     * altrimenti lo usa per impostare il messaggio di errore.
     * 
     * @param mixed $result Dati restituiti dalla chiamata oppure messaggio di errore
     * @return AjaxReturnObject Istanza corrente
     */
    public function setErrorOrData($result) {
        is_string($result) ? $this->setErrorMessage($result) : $this->data = $result;
        return $this;
    }

    /**
     * Se il paramentro $result non è una stringa lo assegna alla proprietà $id,
     * altrimenti lo usa per impostare il messaggio di errore.
     * 
     * @param mixed $result Id restituito dalla chiamata oppure messaggio di errore
     * @return AjaxReturnObject Istanza corrente
     */
    public function setErrorOrId($result) {
        is_string($result) ? $this->setErrorMessage($result) : $this->id = $result;
        return $this;
    }

}

/* End of file AjaxReturnObject.php */
