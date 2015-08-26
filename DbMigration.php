<?php

/**
 * Description of DbMigration
 *
 * @author maurizio
 * @version 1.0
 */
class DbMigration extends CDbMigration {

    /**
     * Fa override del metodo aggiungendo semplicemente la possibilità di non specificare $refColumns
     * in caso coincida con $columns.
     * @param string $name Nome della fk
     * @param string $table Tabella
     * @param string $columns Colonne
     * @param string $refTable Tabella di riferimento
     * @param string $refColumns Colonne di riferimento (opzionale)
     * @param string $delete Azione in caso di eliminazione di record con riferimenti
     * @param string $update Azione in caso di aggiornamento record con riferimenti
     */
    public function addForeignKey($name, $table, $columns, $refTable, $refColumns = null, $delete = null, $update = null) {
        parent::addForeignKey($name, $table, $columns, $refTable, $refColumns ? $refColumns : $columns, $delete, $update);
    }

    /**
     * Restituisce la dichiarazione per la colonna che funge da pk a seconda del parametro passato:
     * <ul>
     * <li>valore nullo: restituisce il tipo di dato per la pk (intero senza segno con auto-increment)</li>
     * <li>nome di colonna: restituisce la dichiarazione PRIMARY KEY per la colonna indicata.</li>
     * </ul>
     * Utilizzo standard:
     * <p>
     * <code>
     * $this->createTable('tableName',array(<br />
     *      'ColonnaPK' => self::Pk(),# --> int(11) unsigned NOT NULL AUTO_INCREMENT<br />
     *      ... ,<br />
     *      self::Pk('ColonnaPK'), # --> PRIMARY KEY (ColonnaPK)<br />
     * ));
     * </code>
     * </p>
     * 
     * @param string $pk Colonna
     * @return string Dichiarazione della pk
     */
    public static function Pk($pk = null) {
        if ($pk)
            return "PRIMARY KEY ($pk)";
        return 'int(11) unsigned NOT NULL AUTO_INCREMENT';
    }

    /**
     * Restituisce la stringa con le opzioni per la creazione di una tabella.
     * @param string $engine Tipo di engine (default InnoDB)
     * @param string $charset Set caratteri (default 'latin1')
     * @return string Stringa con le opzioni per la tabella
     */
    public static function TableOptions($engine = 'InnoDB', $charset = 'latin1') {
        return "ENGINE = $engine CHARSET = $charset";
    }

    /**
     * Restituisce la dichiarazione di tipo per una colonna CHAR(length).
     * @param int $length Numero di caratteri
     * @param boolean $notNull True per richiedere che la colonna sia NOT NULL
     * @return string Dichiarazione di tipo
     */
    public static function TypeChar($length = 255, $notNull = false) {
        return "char($length) " . ($notNull ? 'NOT ' : 'DEFAULT') . ' NULL';
    }

    /**
     * Restituisce la dichiarazione di tipo per una colonna DATE (o DATETIME).
     * @param boolean $time Se true indica il tipo DATETIME, altrimenti DATE
     * @param boolean $notNull True per richiedere che la colonna sia NOT NULL
     * @return string Dichiarazione di tipo
     */
    public static function TypeDate($time = false, $notNull = false) {
        return 'date' . ($time ? 'time ' : ' ') . ($notNull ? 'NOT ' : 'DEFAULT') . ' NULL';
    }

    /**
     * Restituisce la dichiarazione di tipo per una colonna FLOAT.
     * @param boolean $notNull True per richiedere che la colonna sia NOT NULL
     * @return string Dichiarazione di tipo
     */
    public static function TypeFloat($notNull = false) {
        return 'float ' . ($notNull ? 'NOT ' : 'DEFAULT') . ' NULL';
    }

    /**
     * Restituisce la dichiarazione di tipo per una colonna INT(11).
     * @param boolean $notNull True per richiedere che la colonna sia NOT NULL
     * @return string Dichiarazione di tipo
     */
    public static function TypeInt($notNull = false) {
        return 'int(11) unsigned ' . ($notNull ? 'NOT ' : 'DEFAULT') . ' NULL';
    }

    /**
     * Restituisce la dichiarazione di tipo per una colonna VARCHAR(length).
     * @param int $length Numero di caratteri
     * @param boolean $notNull True per richiedere che la colonna sia NOT NULL
     * @return string Dichiarazione di tipo
     */
    public static function TypeVarchar($length = 255, $notNull = false) {
        return "varchar($length) " . ($notNull ? 'NOT ' : 'DEFAULT') . ' NULL';
    }

}
