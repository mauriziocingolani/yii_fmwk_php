<?php

/**
 * Superclass per tutti i modelli delle tabelle del database. Fornisce una prima implementazione
 * (tuttavia ancora astratta) dell'interfaccia DatabaseObject.
 *
 * @author Maurizio Cingolani
 * @version 1.0.11
 */
abstract class AbstractDatabaseObject extends CActiveRecord implements DatabaseObject {

    /** Data e ora di creazione del record */
    public $Created;
    public $_Created;

    /** Utente che ha creato il record */
    public $CreatedBy;

    /** Data e ora di modifica del record */
    public $Updated;
    public $_Updated;

    /** Ultimo utente che ha modificato il record */
    public $UpdatedBy;

    /**
     * Utilizzando i valori contenuti nei campi Created e Updated (se presenti)
     * imposta le proprietà _Created e _Updated con i corrispondenti timestamp.
     */
    protected function afterFind() {
        parent::afterFind();
        if ($this->Created)
            $this->_Created = strtotime($this->Created);
        if ($this->Updated)
            $this->_Updated = strtotime($this->Updated);
    }

    /**
     * Imposta i campi Created/CreatedBy e Updated/UpdatedBy prima del salvataggio.
     * @return boolean True
     */
    protected function beforeSave() {
        if (parent::beforeSave()) :
            if ($this->isNewRecord) :
                $this->Created = date('Y-m-d H:i:s ');
                $this->CreatedBy = Yii::app()->user->id;
            else :
                $this->Updated = date('Y-m-d H:i:s ');
                $this->UpdatedBy = Yii::app()->user->id;
            endif;
            return true;
        endif;
    }

    /**
     * Restituisce una stringa con le informazioni su data e utente responsabile
     * della creazione ed eventualmente della modifica del record. Presuppone che
     * nel modello siano definite le relazioni 'Creator' e 'Updater'.
     * Se richiesto viene visualizzata anche la chiave primaria.
     * @param type $showId True per mostrare l'ID primario
     * @return string Informazioni di creazione  modifica
     */
    public function getCreatedUpdatedString($showId = false) {
        return 'Creazione : ' . date('d-m-Y', $this->_Created) . ' alle ore ' . date('H:i', $this->_Created) .
                ($this->Creator ? ' da parte di <span style="text-decoration: underline;">' . $this->Creator->UserName . '</span>' : '') .
                ($this->_Updated ? '<br />Ultima modifica : ' . date('d-m-Y', $this->_Updated) . ' alle ore ' . date('H:i', $this->_Updated) .
                        ($this->Updater ? ' da parte di <span style="text-decoration: underline;">' . $this->Updater->UserName . '</span>' : '') : '') .
                ($showId ? '<br />' . $this->model()->tableSchema->primaryKey . ": $this->primaryKey" : '');
    }

    /**
     * Questo metodo fa da vocabolario per gli errori SQL generati dalle azioni fatte dagli utenti (inserimenti,
     * elimiinazioni, aggiornamenti) che violano chiavi univoche o secondarie. Ogni modello dovrebbe fare
     * override di questo metodo, definendo la propria lista di errori personalizzata.
     * Un buon metodo, se non si vuole riscrivere tutta la lista, è quello di usare l'operatore '+' per fare il merge
     * dei due array (questo comune e quello del singolo modello), avendo l'accortezza di mettere sempre
     * prima quello del modello; in questo modo ogni modello può sovrascrivere solo i messaggi dei codici
     * di errore che gli interessano, lasciando per tutti gli altri quello di default definito in questa classe.
     * @return array Lista degli errori
     */
    public static function errors() {
        return array(
            1062 => 'Duplicate entry.',
            1451 => 'Cannot delete or update a parent row: a foreign key constraint fails.',
        );
    }

    /**
     * Restituisce la lista dei valori di un campo ENUM di una tabella. Il terzo parametro indica se la collezione
     * restituita deve avere i valori stessi come chiavi (CMap) oppure no (CList con chiavi numeriche incrementali);
     * se è true questo metodo può essere utilizzato direttamente come fonte dati per una DropDownList.
     * @param string $table Tabella
     * @param string $field Campo della tabella
     * @param boolean $useValuesAsKeys Se true imposta i valori stessi come chiavi della lista (CMap) restituita
     * @return mixed Lista dei valori (CMap oppure CList)
     */
    protected static function GetEnumValues($table, $field, $useValuesAsKeys = false) {
        try {
            $data = $useValuesAsKeys ? new CMap : new CList;
            $connection = Yii::app()->db;
            $row = $connection->createCommand("SHOW COLUMNS FROM {$table} WHERE Field = '{$field}' ")->query()->read();
            $type = $row['Type'];
            preg_match('/^enum\((.*)\)$/', $type, $matches);
            foreach (explode(',', $matches[1]) as $value) {
                $d = trim($value, "'");
                $useValuesAsKeys ? $data->add($d, $d) : $data->add($d);
            }
            return $data;
        } catch (CDbException $ex) {
            return $ex;
        }
    }

    /**
     * Elimina il record identificato dall'ID $pk con una semplice invocazione
     * del metodo deleteByPk.
     * @param CActiveRecord $model Modello del record da eliminare
     * @param int $pk ID del record da eliminare
     * @return boolean True se l'eliminazione ha avuto successo
     */
    protected static function SimpleDeleteRecord(CActiveRecord $model, $pk) {
        try {
            return $model->deleteByPk($pk);
        } catch (CDbException $ex) {
            return $ex;
        }
    }

    /**
     * Restituisce il record identificato dall'ID $pk con una semplice invocazione
     * del metodo findByPk.
     * @param CActiveRecord $model Modello del record da recuperare
     * @param type $pk ID del record
     * @return CActiveRecord Record identificato dall'ID $pk
     */
    protected static function SimpleReadRecord(CActiveRecord $model, $pk) {
        try {
            return $model->findByPk($pk);
        } catch (CDbException $ex) {
            return $ex;
        }
    }

    /**
     * Esegue un semplice findAll() con l'eventuale criterio di ordinamento e con
     * l'eventuale condizione dei soli record abilitati (ovvero con Enabled=1).
     * @param CActiveRecord $model Modello dei record da recuperare
     * @param type $order Criterio di ordinamento
     * @param type $onlyEnabled True per restituire solo i record abilitati (Enabled=1)
     * @return mixed Array di record oppure eccezione
     */
    protected static function SimpleGetAll(CActiveRecord $model, $order = '', $onlyEnabled = false) {
        try {
            $criteria = new CDbCriteria;
            if (strlen($order) > 0)
                $criteria->order = $order;
            if ($onlyEnabled === true)
                $criteria->addCondition('Enabled=1');
            return $model->findAll($criteria);
        } catch (CDbException $ex) {
            return $ex;
        }
    }

}

/* End of file AbstractDatabaseObject.php */
