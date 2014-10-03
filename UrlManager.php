<?php

/**
 * Estende {@link CUrlManager} aggiungendo la possibilità di specificare
 * le regole di routing in più lingue. Le regole vanno definite nel file di configurazione
 * "alla rovescia", ovvero in formato:
 * 
 * 'rules' => array(
 *      'controller/view' => {url}
 * )
 * 
 * Attualmente i valori di {url} supportati sono di tre tipi:
 * 
 * <ol>
 * <li>string: url unico per tutte le lingue</li>
 * <li>CList: gli url dirigono tutti sull'azione specificata, senza distinzione di lingua</li>
 * <li>array: gli url sono associati alle varie lingue, e provocano un redirect quando si cambia lingua</li>
 * </ol>
 * 
 * Nel metodo {@link UrlManager::init()} le regole vengono risistemate nel formato
 * corretto per il routing, mentre quelle originali vengono salvate nella proprietà {@link _rawRules}.
 * Quindi la classe {@link Controller} nel metodo {@link Controller::beforeAction} invoca la funzione
 * {@link UrlManager::checkRouteAgainstLanguages}, che utilizza {@link _rawRules} per capire se
 * l'url della route corrisponde alla lingua attualemente selezionata, e in caso contrario effettua
 * automaticamente il redirect.
 * 
 * <strong>NOTA</strong>: questa versione non prevede la gestione in multilingua di url complessi,
 * ovvero del tipo {controller/view/parametro>}, che dovrenno essere gestiti dal controller in questione.
 *
 * @author Maurizio Cingolani <mauriziocingolani74@gmail.com>
 * @version 1.0.1
 */
class UrlManager extends CUrlManager {

    /**
     * @var array Regole originali, ovvero invertite (route=>url)
     */
    private $_rawRules;

    /**
     * Questo metodo ricostruisce le regole impostate nel file di configurazione in modo che siano
     * utilizzabili dal framework per fare il routing. In particolare:
     * <ul>
     * <li>gli url di tipo stringa vengono "girati", ovvero rimessi in formato url=>route</li>
     * <li>gli url di tipo CList e array vengono "esplosi" e quindi girati</li>
     * </ul>
     * 
     * <strong>NOTA</strong>: è fondamentale che il metodo parent::init() venga invocato alla
     * fine, dato che è lui che elabora la variabile $this->rules per creare le regole effettivamente
     * usate per il routing.
     */
    public function init() {
        $rules = array();
        $this->_rawRules = $this->rules;
        foreach ($this->rules as $action => $name) :
            if (is_array($name)) :
                foreach ($name as $n) :
                    $rules[$n] = $action;
                endforeach;
            elseif ($name instanceof CList) :
                foreach ($name as $n) :
                    $rules[$n] = $action;
                endforeach;
            else :
                $rules[$name] = $action;
            endif;
        endforeach;
        $this->rules = $rules;
        parent::init(); // Sempre per ultimo!
    }

    /**
     * Restituisce l'url corretto in base alle lingue impostate e alle regole definite nel file di configurazione.
     * Se non è impostato il multilingua viene restituito l'url passato come parametro; stessa cosa se le regole
     * non prevedono nomi diversi per le varie lingue, ovvero se l'elemento di {@link _rawRules} corrispondente 
     * alla route è uno scalare oppure una CList.
     * Se invece le regole prevedono nomi diversi (ovvero se l'elemento corrispondente di {@link _rawRules} è
     * un array), allora viene restituito l'url giusto, ovvero l'elemento dell'array di nomi che corrisponde all'indice
     * della lingua (Yii::app()->session['languageIndex]).
     * 
     * @param string $route Route attuale (controller/action)
     * @param string $url Url Attuale (senza / iniziale)
     * @return string Url corretto
     */
    public function checkRouteAgainstLanguages($route, $url) {
        if (!isset(Yii::app()->params['languages']) || !is_array(Yii::app()->params['languages']))
            return $url;
        if (isset($this->_rawRules[$route])) :
            $r = $this->_rawRules[$route];
            if (is_array($r)) :
                if ($r[Yii::app()->session['languageIndex']] != $url) :
                    return $r[Yii::app()->session['languageIndex']];
                endif;
            endif;
        endif;
        return $url;
    }

}
