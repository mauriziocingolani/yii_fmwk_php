<?php

/**
 * Questo oggetto permette di costruire ad alto livello un array di configurazione per l'applicazione.
 * Tutti i metodi restituiscono l'oggetto corrente per permettere il concatenamento.
 * @author Maurizio Cingolani <mauriziocingolani74@gmail.com>
 * @version 1.0.2
 */
class Config extends CComponent {

    /** Eventuale sottocartella per i files riservati. */
    private $_subfolder;

    /** Lista degli alias */
    private $aliases;

    /** Percorso base (cartella 'protected'). */
    private $basePath;

    /** Lista dei componenti. */
    private $components;

    /** Lista degli import. */
    private $import;

    /** Nome dell'applicazione. */
    private $name;

    /** Nome del file con i parametri (default 'params.php'). */
    private $params;

    /** Lista dei componenti per il preload (default 'log'). */
    private $preload;

    /** Nome del file con le regole di routing (default 'rules.php'). */
    private $rules;

    /** Stringa della timezone (default 'Europe/Rome'). */
    private $timeZone;

    /**
     * Inizializza le proprietà e assegna il nome ed eventualemente la sottocartella dei files riservati.
     * @param type $name Nome dell'applicazione
     * @param type $subFolder Eventuale sottocartella per i files riservati (db, mail, etc...)
     */
    public function __construct($name, $subFolder = null) {
        if ($subFolder)
            $this->_subfolder = $subFolder;
        $this->basePath = dirname(__FILE__) . DIRECTORY_SEPARATOR . '../..';
        $this->components = array();
        $this->name = $name;
        $this->import = array();
        $this->preload = array('log');
        $this->timeZone = 'Europe/Rome';
        // Files di configurazione
        $this->params = 'params.php';
        $this->rules = 'rules.php';
    }

    /**
     * Costruisce e restituisce l'array di configurazione utilizzando le proprietà impostate.
     * @return array Array di configurazione
     */
    public function getConfig() {
        $config = array(
            'basePath' => $this->basePath,
            'components' => $this->components,
            'import' => $this->import,
            'language' => null,
            'name' => $this->name,
            'params' => require $this->basePath . '/config' . ($this->_subfolder ? '/' . $this->_subfolder : '') . '/' . $this->params,
            'preload' => $this->preload,
            'timeZone' => $this->timeZone,
        );
        if (count($this->aliases) > 0)
            $config['aliases'] = $this->aliases;
        return $config;
    }

    /* === Setters === */

    /**
     * Popola la proprietà {@link $aliases} con gli elementi dell'array passato.
     * @param array $alias Lista degli alias
     * @return Config Oggetto attuale per concatenamento
     */
    public function addAlias(array $alias) {
        foreach ($alias as $a => $v) :
            if (!isset($this->aliases[$a]))
                $this->aliases[$a] = $v;
        endforeach;
        return $this;
    }

    /**
     * Aggiunge (o sovrascrive) un singolo componente nella proprietà {@link $components}.
     * L'array passato come parametro deve avere come elementi i singoli componenti da aggiungere,
     * con il nome del componente come chiave e con il contenuto come valore.
     * <b>Se il componente da aggiungere è già presente (perchè assegnato tramite uno dei
     * metodi ad alto livello) verrà sovrascritto.</b>
     * @param array $components Array di componenti da aggiungere.
     * @return Config Oggetto attuale per concatenamento
     */
    public function addComponent(array $component) {
        $this->components = array_merge($this->components, $component);
        return $this;
    }

    /**
     * Imposta il componente 'db', caricando i parametri dal file indicato. Se il parametro è assente
     * viene utilizzato il valore di default 'db.php'. La posizione del file è determinata dalla proprietà
     * {@link $_subfolder}.
     * @param type $dbFile Nome del file con i parametri di connessione al db.
     * @return Config Oggetto attuale per concatenamento
     */
    public function addDbComponent($dbFile = null) {
        $this->components = array_merge($this->components, array(
            'db' => require $this->basePath . '/config' . ($this->_subfolder ? '/' . $this->_subfolder : '') . '/' . ($dbFile ? $dbFile : 'db.php'),
        ));
        return $this;
    }

    /**
     * Imposta il componente 'mail', caricando i parametri dal file indicato. Se il parametro è assente
     * viene utilizzato il valore di default 'mail.php'. La posizione del file è determinata dalla proprietà
     * {@link $_subfolder}.
     * @param string $mailFile Nome del file con i parametri della posta
     * @return Config Oggetto attuale per concatenamento
     */
    public function addMailComponent($mailFile = null) {
        $this->components = array_merge($this->components, array(
            'mail' => require $this->basePath . '/config' . ($this->_subfolder ? '/' . $this->_subfolder : '') . '/' . ($mailFile ? $mailFile : 'mail.php'),
        ));
        return $this;
    }

    /**
     * Aggiunge alla proprietà {@link $preload} il componente (o i componenti) da precaricare.
     * @param mixed $component Componente (o array di componenti) da aggiungere alla proprietà {@link $preload}.
     * @return Config Oggetto attuale per concatenamento
     */
    public function addPreload($component) {
        if (is_array($component)) :
            foreach ($component as $comp) :
                if (!isset($this->preload[$comp]))
                    $this->preload[] = $comp;
            endforeach;
        else :
            if (!isset($this->preload[$component]))
                $this->preload[] = $component;
        endif;
        return $this;
    }

    /**
     * Aggiunge il componente 'sessions'. E' possibile indicare la durata in secondi del timeout di sessione
     * ed eventualmente il nome della tabella db per le sessioni.
     * @param int $timeoutSecs Timeout della sessione in secondi
     * @param string $sessionTable Nome della tabella per le sessioni (default 'sessions')
     * @return Config Oggetto attuale per concatenamento
     */
    public function addSessionComponent($timeoutSecs, $sessionTable = null) {
        $this->components = array_merge($this->components, array(
            'session' => array(
                'autoCreateSessionTable' => false,
                'autoStart' => true,
                'class' => 'CDbHttpSession',
                'connectionID' => 'db',
                'sessionTableName' => ($sessionTable ? $sessionTable : 'sessions'),
                'timeout' => $timeoutSecs > 0 ? $timeoutSecs : 60 * 60 * 24,
            ),
        ));
        return $this;
    }

    /**
     * Aggiunge i componenti standard:
     * <ul>
     * <li>authManager</li>
     * <li>errorHandler</li>
     * <li>log</li>
     * <li>urlManager</li>
     * </ul>
     * I compnenti possono poi essere rimodificati mediante sovrascrittura con il
     * metodo {@link Config::addComponent()}.
     * 
     * Il parametro opzionale permette di sovrascrivere le impostazioni dei componenti standard.
     * Parametri attualemente impostabili:
     * <ul>
     * <li>urlManager.class</li>
     * </ul>
     * In futuro il nome dell'opzione dovrà rispecchiare la gerarchia dell'array e impostare automaticamente
     * il parametro in questione.
     * @param array $options Lista di parametri da modificare nella configurazione standard
     * @return Config Oggetto attuale per concatenamento
     */
    public function addStandardComponents(array $options = null) {
        $this->components = array_merge($this->components, array(
            'authManager' => array(
                'class' => 'CPhpAuthManager',
            ),
            'errorHandler' => array(
                'errorAction' => 'site/error',
            ),
            'log' => array(
                'class' => 'CLogRouter',
                'routes' => array(
                    array(
                        'class' => 'CProfileLogRoute',
                        'levels' => '',
                        'enabled' => YII_DEBUG,
                    ),
                    array(
                        'class' => 'CWebLogRoute',
                        'enabled' => YII_DEBUG,
                    ),
                ),
            ),
            'urlManager' => array(
                'urlFormat' => 'path',
                'showScriptName' => false,
                'rules' => require $this->basePath . '/config' . ($this->_subfolder ? '/' . $this->_subfolder : '') . '/' . $this->rules,
            ),
        ));
        // Impostazione opzioni
        if ($options && is_array($options)) :
            foreach ($options as $opt => $value) :
                if ($opt == 'urlManager.class') :
                    $this->components['urlManager']['class'] = $value;
                endif;
//                $split = preg_split('/[\.]/', $opt);
//                $last = array_pop($split);
//                $element = $this->components;
//                foreach ($split as $sp) :
//                    if (isset($element[$sp])) :
//                        $element = $element[$sp];
//                    else :
//                        $element = null;
//                        break;
//                    endif;
//                endforeach;
//                if ($element) :
//                    CVarDumper::dump($element[$last], 10, true);
//                    $element[$last] = $value;
//                    CVarDumper::dump($element[$last], 10, true);
//
//                endif;
//                CVarDumper::dump($element, 10, true);
            endforeach;
        endif;
        return $this;
    }

    /**
     * Aggiunge alla proprietà {@link $import} le cartelle standard:
     * <ul>
     * <li>models</li>
     * <li>components</li>
     * <li>components.framework</li>
     * <li>extensions.behaviors</li>
     * <li>extensions.bootstrap</li>
     * <li>extensions.validators</li>
     * </ul>
     * Eventualmente aggiunge anche le altre cartelle specificate tramite parametro.
     * @param array $otherImports Altri import oltre a quelli standard
     * @return Config Oggetto attuale per concatenamento
     */
    public function addStandardImports(array $otherImports = null) {
        $this->import[] = 'application.models.*';
        $this->import[] = 'application.components.*';
        $this->import[] = 'application.components.framework.*';
        $this->import[] = 'ext.behaviors.*';
        $this->import[] = 'ext.bootstrap.*';
        $this->import[] = 'ext.validators.*';
        if (is_array($otherImports)) :
            foreach ($otherImports as $oi) :
                $this->import[] = $oi;
            endforeach;
        endif;
        return $this;
    }

    /**
     * Aggiunge il componente 'user'.
     * @return Config Oggetto attuale per concatenamento
     */
    public function addUserComponent() {
        $this->components = array_merge($this->components, array(
            'user' => array(
                'allowAutoLogin' => false,
                'loginUrl' => array('site/index'),
            ),
        ));
        return $this;
    }

    /**
     * Imposta la proprietà {@link $basePath}.
     * @param string $basePath 
     * @return Config Oggetto attuale per concatenamento
     */
    public function setBasePath($basePath) {
        $this->basePath = $basePath;
        return $this;
    }

    /**
     * Imposta la proprietà {@link $timeZone}.
     * @param string $timeZone
     * @return Config Oggetto attuale per concatenamento
     */
    public function setTimeZone($timeZone) {
        $this->timeZone = $timeZone;
        return $this;
    }

}
