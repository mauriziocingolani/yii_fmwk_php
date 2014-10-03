<?php

/**
 * Description of PasswordHelper
 *
 * @author Maurizio Cingolani <mauriziocingolani74@gmail.com>
 */
class PasswordHelper extends CPasswordHelper {

    public static function GeneratePassword($length = 10) {
        if ((int) $length <= 0 || (int) $length >= 255)
            throw new Exception('You can\'t generate passwords shorter than 1 or longer than 255 characters.');
        $alphabet = array_merge(range(0, 9), range('a', 'z'), range('A', 'Z'));
        shuffle($alphabet);
        return implode('', array_slice($alphabet, 0, 10));
    }

}
