<?php

/**
 * Superclass per oggetti del database che rappresentano una persona.
 * Espone le proprietà per il nome e il cognome e un metodo per restituire
 * il nome completo.
 *
 * @author Maurizio Cingolani
 * @version 1.0
 */
abstract class AbstractPersonObject extends AbstractDatabaseObject {

    /** Nome */
    public $FirstName;

    /** Cognome */
    public $LastName;

    /**
     * Restituisce il nome completo della persona. Se non indicato diversamente
     * viene restituito il nome seguito dal cognome.
     * 
     * @param boolean $lastNameFirst True per restituire il cognome prima del nome
     * @return string Nome completo
     */
    public function getCompleteName($lastNameFirst = false) {
        return $lastNameFirst ? "$this->LastName $this->FirstName" : "$this->FirstName $this->LastName";
    }

}

/* End of file AbstractPersonObject.php */
