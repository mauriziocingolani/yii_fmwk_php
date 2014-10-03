<?php

/**
 * Definisce i metodi standard che tutte le classi CActiveRecord dovranno implementare:
 * CreateRecord, ReadRecord, UpdateRecord, DeleteRecord.
 * 
 * @author Maurizio Cingolani
 * @version 1.0
 */
interface DatabaseObject {

    /**
     * Crea un nuovo record.
     * @param array $parameters Parametri
     */
    public static function CreateRecord(array $parameters);

    /**
     * Recupera il record specificato in base alla chiave primaria.
     * @param int $pk Chiave primaria che identifica il record
     */
    public static function ReadRecord($pk);

    /**
     * Recupera il record specificato in base alla chiave primaria e ne aggiorna i campi
     * secondo i parametri passati.
     * @param int $pk Chiave primaria che identifica il record
     * @param array $parameters Parametri
     */
    public static function UpdateRecord($pk, array $parameters);

    /**
     * Elimina il record specificato in base alla chiave primaria.
     * @param int $pk Chiave primaria che identifica il record
     */
    public static function DeleteRecord($pk);
}

/* End of file DatabaseObject.php */
