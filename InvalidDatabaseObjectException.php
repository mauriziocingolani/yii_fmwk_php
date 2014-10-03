<?php

/**
 * Eccezione sollevata quando da un'azione di controller che riceve come parametro
 * un id di oggetto database non valido.
 * Il messaggio può essere visualizzato in inglese (se il sito utilizza il meccanismo multilanguage)
 * oppure in italiano (default)
 * @author Maurizio Cingolani <mauriziocingolani74@gmail.com>
 * @version 1.1
 */
class InvalidDatabaseObjectException extends CHttpException {

    /**
     * Crea una nuova istanza della classe, inizializzando il messaggio di errore. Se il sito supporta il multilingua
     * e il linguaggio attuale è l'inglese viene utilizzato il messaggio in inglese, mentre negli altri casi viene
     * utilizzato quello in italiano.
     * @param string $description Nome dell'oggetto cui fa riferimento la pagina (es. "La ditta", "Il referente")
     * @param boolean $isFemale Indica se l'oggetto deve avere suffisso femminile
     */
    public function __construct($description, $isFemale = false) {
        $o = $isFemale === true ? 'a' : 'o';
        if (isset(Yii::app()->session['language']) && Yii::app()->session['language'] == 'en') :
            parent::__construct(410, "$description you were looking for either never exsisted or does not exist anymore. " .
                    "To avoid errors like this you might want to click on the links you find in the pages instead of " .
                    "modifying the values you read in the url field.");
        else :
            parent::__construct(410, "$description richiest$o non esiste o potrebbe essere stat$o eliminat$o nel frattempo. " .
                    "Per evitare questo tipo di errori sei pregato di utilizzare sempre i link che trovi nelle pagine e di non " .
                    "modificare mai manualmente i valori riportati nella barra dell'indirizzo.");
        endif;
    }

}
