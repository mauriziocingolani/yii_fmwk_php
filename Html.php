<?php

/**
 * Estende la classe CHtml con alcuni metodi di utilità.
 *
 * @author Maurizio Cingolani
 * @version 1.0.4
 */
class Html extends CHtml {

    /**
     * Genera un tag img con l'immagine richiesta. Il percorso dell'immagine
     * è quello definito nel parametro 'imagesPath' all'interno
     * del file di configurazione.
     * @param string $src None dell'immagine
     * @param string $alt Testo alternativo
     * @param array $htmlOptions Altri attributi html
     * @return string Tag img
     */
    public static function image($src, $alt = '', $htmlOptions = array()) {
        return parent::image(Yii::app()->params['imagesPath'] . $src, $alt, $htmlOptions);
    }

    /**
     * Questo metodo, che riceve come argomenti i messaggi nelle varie lingue, permette di sceglierne uno
     * in base alla lingua attualemente selezionata.
     * Il numero di argomenti deve rispecchiare il numero e l'ordine delle lingue, definite nell'array Yii::app()->params['languages'].
     * Quindi il metodo restituisce l'argomento corrispondente alla lingua attualmente selezionata.
     * Per velocizzare l'esecuzione ed evitare di cercare la lingua attuale (assegnata alla variabile Yii::app()->session['language'])
     * a ogni invocazione del metodo, viene utilizzata la variabile Yii::app()->session['languageIndex']; questa viene impostata
     * a ogni cambio lingua dal metodo {@link Controller::beforeAction}, e indica in che posizione dell'array delle
     * lingue si trova quella attualmente selezionata. In questo modo è immediato decidere quale argomento
     * deve essere restituito senza attraversare l'array delle lingue.
     * 
     * @return string Messaggio nella lingua selezionata
     * @throws CException Quando il numero di argomenti non coincide con il numero di linguaggi impostati in Yii::app()->params['languages']
     */
    public static function MultilanguageText() {
        if (!isset(Yii::app()->params['languages']) || func_num_args() !== count(Yii::app()->params['languages'])) :
            throw new CException(__METHOD__ . ' : the number of arguments doesn\'t match the number of languages.');
        endif;
        if (!isset(Yii::app()->session['languageIndex']))
            return func_get_arg(0);

        return func_get_arg(Yii::app()->session['languageIndex']);
    }

    /**
     * Inserisce in fondo alla pagina lo snippet javascript con il codice di monitoraggio GA.
     * Se non viene passato il codice come parametro viene utilizzato il parametro dell'applicazione
     * ['googleAnalytics]['account']. Se nemmeno quest'ultimo è impostato viene sollevata una CException.
     * Prima di registrare lo script viene inibito il caricamento del core jQuery.
     * @param string $account Codice di monitoraggio
     * @throws CException Se il parametro {@link $account} è nullo e non è impostato il parametro ['googleAnalytics]['account] dell'applicazione.
     */
    public static function GoogleAnalytics($account = null) {
        if ($account == null)
            $account = Yii::app()->params['googleAnalytics']['account'];
        if ($account == null)
            throw new CException('Devi specificare l\'account Analytics (come argomento del metodo Html::GoogleAnalytics o come parametro dell\'applicazione).');
        $cs = Yii::app()->clientScript;
        $cs->scriptMap = array(
            'jquery.js' => false,
            'jquery.min.js' => false,
            'jquery.ui.js' => false,
        );
        $cs->registerScript(sha1(time()), "(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){" .
                "(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o)," .
                "m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)" .
                "})(window,document,'script','//www.google-analytics.com/analytics.js','ga');" .
                "ga('create', '{$account}', 'auto');" .
                "ga('send', 'pageview');");
    }

}

/* End of file Html.php */