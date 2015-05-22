<?php

/**
 * Apre un file Excel come oggetto PHPExcel e gestisce la lettura sequenziale delle righe
 * implementando l'interfaccia {@link Iterator}. Ogni riga viene restituita sotto forma di un
 * oggetto le cui proprietà hanno lo stesso nome delle intestazioni di colonna del foglio in questione.
 * Si presuppone che ogni foglio abbia nella prima riga le intestazioni di colonna, e che ciascuna di esse
 * sia in formato compatibile con le regole di denominazione delle proprietà di oggetti PHP (si veda 
 * {@see http://php.net/manual/en/language.variables.basics.php}.
 * 
 * @property PHPExcel $_objPHPExcel Oggetto PHPExcel
 * @property integer $_nsheets Numero di fogli del file
 * @property array $headers Intestazioni delle colonne
 * @property integer $_maxColumn Numero di colonne
 * @property integer $_maxRow Numero di righe
 * @property PHPExcel_Worksheet $_sheet Foglio attivo
 * @property integer $_position Riga attuale
 * 
 * Getters
 * @property integer $sheetCount
 * @property array $headers
 * 
 * @author Maurizio Cingolani
 * @version 1.0
 */
class ExcelFile extends CComponent implements Iterator {

    private $_objPHPExcel;
    private $_nsheets;
    private $_headers;
    private $_maxColumn;
    private $_maxRow;
    private $_sheet;
    private $_position;

    /**
     * Costruisce l'oggetto {@link ExcelFile::$_objPHPExcel} e inizializza il primo foglio come attivo.
     * @param string $filePath Percorso del file Excel
     */
    public function __construct($filePath) {
        $readerType = PHPExcel_IOFactory::identify($filePath);
        $reader = PHPExcel_IOFactory::createReader($readerType);
        $reader->setReadDataOnly(true);
        $this->_objPHPExcel = $reader->load($filePath);
        $this->_nsheets = $this->_objPHPExcel->getSheetCount();
        $this->_initSheet(0);
    }

    /**
     * Restituisce {@link ExcelFile::$_headers}.
     * @return array Intestazioni di colonna
     */
    public function getHeaders() {
        return $this->_headers;
    }

    /**
     * Restituisce {@link ExcelFile::$_nsheets}.
     * @return integer Numero di fogli presenti nel file
     */
    public function getSheetCount() {
        return $this->_nsheets;
    }

    /**
     * Imposta il foglio indicato (se esite) come attivo.
     * @param integer $pIndex Indice del foglio (0,1,...)
     */
    public function setCurrentSheet($pIndex) {
        if ($pIndex >= 0 && $pIndex < $this->_nsheets)
            $this->_initSheet($pIndex);
    }

    /**
     * Restituisce la riga attuale del foglio Excel sotto forma di un oggetto le cui proprietà hanno lo
     * stesso nome delle intestazioni di  colonna.
     * @return stdClass Oggetto che rappresenta la riga del file Excel
     */
    public function current() {
        $c = new stdClass();
        for ($column = 0; $column < $this->_maxColumn; $column++) :
            $cell = $this->_sheet->getCellByColumnAndRow($column, $this->_position);
            $prop = $this->_headers[$column];
            $c->$prop = $cell->getValue();
        endfor;
        return $c;
    }

    /**
     * Restituisce la riga attuale.
     * @return integer Riga attuale
     */
    public function key() {
        return $this->_position;
    }

    /**
     * Passa alla riga successiva.
     */
    public function next() {
        ++$this->_position;
    }

    /**
     * Ritorna all'inizio del foglio attuale (ovvero alla seconda riga).
     */
    public function rewind() {
        $this->_position = 2;
    }

    /**
     * Verifica che la riga attuale sia valida, ovvero che il suo indice non abbia superato
     * il numero totale di righe.
     * @return boolean true se la riga attuale è valida
     */
    public function valid() {
        return $this->_position <= $this->_maxRow;
    }

    /**
     * Rende attivo il foglio indicato, reinizializzando le proprietà {@link ExcelFile::$_maxColumn},
     * {@link ExcelFile::$_maxRow}, {@link ExcelFile::$_headers} e riportando la posizione all'inizio
     * (ovvero alla seconda riga).
     * @param integer $pIndex Indice del foglio da rendere attivo
     */
    private function _initSheet($pIndex) {
        $this->_sheet = $this->_objPHPExcel->getSheet($pIndex);
        $this->_maxRow = (int) $this->_sheet->getHighestRow();
        $this->_maxColumn = (int) PHPExcel_Cell::columnIndexFromString($this->_sheet->getHighestColumn());
        $this->_headers = array();
        for ($column = 0; $column < $this->_maxColumn; $column++) :
            $cell = $this->_sheet->getCellByColumnAndRow($column, 1);
            $this->_headers[$column] = $cell->getValue();
        endfor;
        $this->rewind();
    }

}
