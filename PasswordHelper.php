<?php

/**
 * Description of PasswordHelper
 *
 * @author Maurizio Cingolani <mauriziocingolani74@gmail.com>
 * @version 1.0.1
 */
class PasswordHelper extends CPasswordHelper {

    /**
     * Genera una password di lunghezza indicata utilizzando lettere minuscole e maiuscole, cifre
     * ed eventualmente i caratteri speciali $@#&%
     * @param int $length Lunghezza della password (da 1 a 255, default 10)
     * @param boolean $allowSpecialCharacters Se true inserisce i caratteri speciali $@#&%
     * @return string Password generata
     * @throws Exception Se la lunghezza indicata è minore di 1 o maggiore di 255
     */
    public static function GeneratePassword($length = 10, $allowSpecialCharacters = false) {
        if ((int) $length <= 0 || (int) $length >= 255)
            throw new Exception('You can\'t generate passwords shorter than 1 or longer than 255 characters.');
        $alphabet = array_merge(range(0, 9), range('a', 'z'), range('A', 'Z'));
        if ($allowSpecialCharacters)
            $alphabet = array_merge($alphabet, array('$', '@', '#', '&', '%'));
        shuffle($alphabet);
        return implode('', array_slice($alphabet, 0, $length));
    }

}
