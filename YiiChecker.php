<?php

/**
 * Esegue i controlli sui requisiti di sistema. L'array restituito dal metodo {@link YiiChecker::Requirements()}
 * va passato alla view della pagina come lista di parametri.
 *
 * @author Maurizio Cingolani <mauriziocingolani74@gmail.com>
 * @version 1.0.1
 */
class YiiChecker extends CComponent {

    /** Testi dei vari requisiti */
    static $messages;

    /**
     * Esegue i controlli sui requisiti di sistema necessari a Yii.
     * Restituisce un array con tre elementi:
     * <ul>
     * <li>requirements: lista degli esiti dei singoli controlli</li>
     * <li>result: risultato globale (1 passato, 0 fallito, -1 passato con warnings)</li>
     * <li>serverInfo: info sul server</li>
     * </ul>
     * @return array Lista di parametri
     */
    public static function Requirements() {
        self::$messages = array(
            '$_SERVER does not have {vars}.' => '$_SERVER non contiene {vars}.',
            '$_SERVER variable' => 'variabile $_SERVER',
            '$_SERVER["SCRIPT_FILENAME"] must be the same as the entry script file path.' => '$_SERVER["SCRIPT_FILENAME"] deve essere identico al path del file di entrata.',
            'APC extension' => 'Estensione APC',
            'All <a href="http://www.yiiframework.com/doc/api/#system.db">DB-related classes</a>' => 'Tutte le <a href="http://www.yiiframework.com/doc/api/#system.db">classi legate al DB</a>',
            'DOM extension' => 'Estensione DOM',
            'Either $_SERVER["REQUEST_URI"] or $_SERVER["QUERY_STRING"] must exist.' => 'Uno tra $_SERVER["REQUEST_URI"] o $_SERVER["QUERY_STRING"] deve esistere',
            'GD extension' => 'Estensione GD',
            'Mcrypt extension' => 'Estensione Mcrypt',
            'Memcache extension' => 'Estensione Memcache',
            'PCRE extension' => 'Estensione PCRE',
            'PDO MySQL extension' => 'Estensione PDO MySQL',
            'PDO PostgreSQL extension' => 'Estensione PDO PostgreSQL',
            'PDO SQLite extension' => 'Estensione PDO SQLite',
            'PDO extension' => 'Estensione PDO',
            'PHP 5.1.0 or higher is required.' => 'È richiesto PHP 5.1.0 o superiore',
            'PHP version' => 'Versione PHP',
            'Reflection extension' => 'Estensione Reflection',
            'SOAP extension' => 'Estensione SOAP',
            'SPL extension' => 'Estensione SPL',
            'This is required if you are using MySQL database.' => 'Necessario se si utilizza il database MySQL.',
            'This is required if you are using PostgreSQL database.' => 'Necessario se si utilizza il database PostgreSQL.',
            'This is required if you are using SQLite database.' => 'Necessario se si utilizza il database SQLite .',
            'Unable to determine URL path info. Please make sure $_SERVER["PATH_INFO"] (or $_SERVER["PHP_SELF"] and $_SERVER["SCRIPT_NAME"]) contains proper value.' => 'Impossibile determinare il path dell\'URL. Assicurarsi che $_SERVER["PATH_INFO"] (o $_SERVER["PHP_SELF"] e $_SERVER["SCRIPT_NAME"]) contengano valori adeguati.',
        );
        $requirements = array(
            array(
                self::T('yii', 'PHP version'),
                true,
                version_compare(PHP_VERSION, "5.1.0", ">="),
                '<a href="http://www.yiiframework.com">Yii Framework</a>',
                self::T('yii', 'PHP 5.1.0 or higher is required.')),
            array(
                self::T('yii', '$_SERVER variable'),
                true,
                '' === $message = self::CheckServerVar(),
                '<a href="http://www.yiiframework.com">Yii Framework</a>',
                $message),
            array(
                self::T('yii', 'Reflection extension'),
                true,
                class_exists('Reflection', false),
                '<a href="http://www.yiiframework.com">Yii Framework</a>',
                ''),
            array(
                self::T('yii', 'PCRE extension'),
                true,
                extension_loaded("pcre"),
                '<a href="http://www.yiiframework.com">Yii Framework</a>',
                ''),
            array(
                self::T('yii', 'SPL extension'),
                true,
                extension_loaded("SPL"),
                '<a href="http://www.yiiframework.com">Yii Framework</a>',
                ''),
            array(
                self::T('yii', 'DOM extension'),
                false,
                class_exists("DOMDocument", false),
                '<a href="http://www.yiiframework.com/doc/api/CHtmlPurifier">CHtmlPurifier</a>, <a href="http://www.yiiframework.com/doc/api/CWsdlGenerator">CWsdlGenerator</a>',
                ''),
            array(
                self::T('yii', 'PDO extension'),
                false,
                extension_loaded('pdo'),
                self::T('yii', 'All <a href="http://www.yiiframework.com/doc/api/#system.db">DB-related classes</a>'),
                ''),
            array(
                self::T('yii', 'PDO SQLite extension'),
                false,
                extension_loaded('pdo_sqlite'),
                self::T('yii', 'All <a href="http://www.yiiframework.com/doc/api/#system.db">DB-related classes</a>'),
                self::T('yii', 'Required for SQLite database.')),
            array(
                self::T('yii', 'PDO MySQL extension'),
                false,
                extension_loaded('pdo_mysql'),
                self::T('yii', 'All <a href="http://www.yiiframework.com/doc/api/#system.db">DB-related classes</a>'),
                self::T('yii', 'Required for MySQL database.')),
            array(
                self::T('yii', 'PDO PostgreSQL extension'),
                false,
                extension_loaded('pdo_pgsql'),
                self::T('yii', 'All <a href="http://www.yiiframework.com/doc/api/#system.db">DB-related classes</a>'),
                self::T('yii', 'Required for PostgreSQL database.')),
            array(
                self::T('yii', 'PDO Oracle extension'),
                false,
                extension_loaded('pdo_oci'),
                self::T('yii', 'All <a href="http://www.yiiframework.com/doc/api/#system.db">DB-related classes</a>'),
                self::T('yii', 'Required for Oracle database.')),
            array(
                self::T('yii', 'PDO MSSQL extension (pdo_mssql)'),
                false,
                extension_loaded('pdo_mssql'),
                self::T('yii', 'All <a href="http://www.yiiframework.com/doc/api/#system.db">DB-related classes</a>'),
                self::T('yii', 'Required for MSSQL database from MS Windows')),
            array(
                self::T('yii', 'PDO MSSQL extension (pdo_dblib)'),
                false,
                extension_loaded('pdo_dblib'),
                self::T('yii', 'All <a href="http://www.yiiframework.com/doc/api/#system.db">DB-related classes</a>'),
                self::T('yii', 'Required for MSSQL database from GNU/Linux or other UNIX.')),
            array(
                self::T('yii', 'PDO MSSQL extension (<a href="http://sqlsrvphp.codeplex.com/">pdo_sqlsrv</a>)'),
                false,
                extension_loaded('pdo_sqlsrv'),
                self::T('yii', 'All <a href="http://www.yiiframework.com/doc/api/#system.db">DB-related classes</a>'),
                self::T('yii', 'Required for MSSQL database with the driver provided by Microsoft.')),
            array(
                self::T('yii', 'PDO ODBC extension'),
                false,
                extension_loaded('pdo_odbc'),
                self::T('yii', 'All <a href="http://www.yiiframework.com/doc/api/#system.db">DB-related classes</a>'),
                self::T('yii', 'Required in case database interaction will be through ODBC layer.')),
            array(
                self::T('yii', 'Memcache extension'),
                false,
                extension_loaded("memcache") || extension_loaded("memcached"),
                '<a href="http://www.yiiframework.com/doc/api/CMemCache">CMemCache</a>',
                extension_loaded("memcached") ? self::T('yii', 'To use memcached set <a href="http://www.yiiframework.com/doc/api/CMemCache#useMemcached-detail">CMemCache::useMemcached</a> to <code>true</code>.') : ''),
            array(
                self::T('yii', 'APC extension'),
                false,
                extension_loaded("apc"),
                '<a href="http://www.yiiframework.com/doc/api/CApcCache">CApcCache</a>',
                ''),
            array(
                self::T('yii', 'Mcrypt extension'),
                false,
                extension_loaded("mcrypt"),
                '<a href="http://www.yiiframework.com/doc/api/CSecurityManager">CSecurityManager</a>',
                self::T('yii', 'Required by encrypt and decrypt methods.')),
            array(
                self::T('yii', 'crypself::T() CRYPT_BLOWFISH option'),
                false,
                function_exists('crypt') && defined('CRYPT_BLOWFISH') && CRYPT_BLOWFISH,
                '<a href="http://www.yiiframework.com/doc/api/1.1/CPasswordHelper">CPasswordHelper</a>',
                self::T('yii', 'Required for secure password storage.')),
            array(
                self::T('yii', 'SOAP extension'),
                false,
                extension_loaded("soap"),
                '<a href="http://www.yiiframework.com/doc/api/CWebService">CWebService</a>, <a href="http://www.yiiframework.com/doc/api/CWebServiceAction">CWebServiceAction</a>',
                ''),
            array(
                self::T('yii', 'GD extension with<br />FreeType support<br />or ImageMagick<br />extension with<br />PNG support'),
                false,
                '' === $message = self::CheckCaptchaSupport(),
                '<a href="http://www.yiiframework.com/doc/api/CCaptchaAction">CCaptchaAction</a>',
                $message),
            array(
                self::T('yii', 'Ctype extension'),
                false,
                extension_loaded("ctype"),
                '<a href="http://www.yiiframework.com/doc/api/CDateFormatter">CDateFormatter</a>, <a href="http://www.yiiframework.com/doc/api/CDateFormatter">CDateTimeParser</a>, <a href="http://www.yiiframework.com/doc/api/CTextHighlighter">CTextHighlighter</a>, <a href="http://www.yiiframework.com/doc/api/CHtmlPurifier">CHtmlPurifier</a>',
                ''
            ),
            array(
                self::T('yii', 'Fileinfo extension'),
                false,
                extension_loaded("fileinfo"),
                '<a href="http://www.yiiframework.com/doc/api/CFileValidator">CFileValidator</a>',
                self::T('yii', 'Required for MIME-type validation')
            ),
        );
        $result = 1;  // 1: all pass, 0: fail, -1: pass with warnings
        foreach ($requirements as $i => $requirement) {
            if ($requirement[1] && !$requirement[2])
                $result = 0;
            else if ($result > 0 && !$requirement[1] && !$requirement[2])
                $result = -1;
            if ($requirement[4] === '')
                $requirements[$i][4] = '&nbsp;';
        }
        return array(
            'requirements' => $requirements,
            'result' => $result,
            'serverInfo' => self::GetServerInfo(),
        );
    }

    private static function GetServerInfo() {
        $info[] = isset($_SERVER['SERVER_SOFTWARE']) ? $_SERVER['SERVER_SOFTWARE'] : '';
        $info[] = '<a href="http://www.yiiframework.com/">Yii Framework</a>/' . self::GetYiiVersion();
        $info[] = @strftime('%Y-%m-%d %H:%M', time());
        return implode(' ', $info);
    }

    private static function T($category, $message, $params = array()) {
        if (empty(self::$message))
            return $message;
        if (isset(self::$messages[$message]) && self::$messages[$message] !== '')
            $message = self::$messages[$message];

        return $params !== array() ? strtr($message, $params) : $message;
    }

    private static function CheckServerVar() {
        $vars = array('HTTP_HOST', 'SERVER_NAME', 'SERVER_PORT', 'SCRIPT_NAME', 'SCRIPT_FILENAME', 'PHP_SELF', 'HTTP_ACCEPT', 'HTTP_USER_AGENT');
        $missing = array();
        foreach ($vars as $var) {
            if (!isset($_SERVER[$var]))
                $missing[] = $var;
        }
        if (!empty($missing))
            return self::T('yii', '$_SERVER does not have {vars}.', array('{vars}' => implode(', ', $missing)));

//        if (realpath($_SERVER["SCRIPT_FILENAME"]) !== realpath(__FILE__))
//            return self::T('yii', '$_SERVER["SCRIPT_FILENAME"] must be the same as the entry script file path.');

        if (!isset($_SERVER["REQUEST_URI"]) && isset($_SERVER["QUERY_STRING"]))
            return self::T('yii', 'Either $_SERVER["REQUEST_URI"] or $_SERVER["QUERY_STRING"] must exist.');

        if (!isset($_SERVER["PATH_INFO"]) && strpos($_SERVER["PHP_SELF"], $_SERVER["SCRIPT_NAME"]) !== 0)
            return self::T('yii', 'Unable to determine URL path info. Please make sure $_SERVER["PATH_INFO"] (or $_SERVER["PHP_SELF"] and $_SERVER["SCRIPT_NAME"]) contains proper value.');

        return '';
    }

    private static function CheckCaptchaSupport() {
        if (extension_loaded('imagick')) {
            $imagick = new Imagick();
            $imagickFormats = $imagick->queryFormats('PNG');
        }
        if (extension_loaded('gd'))
            $gdInfo = gd_info();
        if (isset($imagickFormats) && in_array('PNG', $imagickFormats))
            return '';
        elseif (isset($gdInfo)) {
            if ($gdInfo['FreeType Support'])
                return '';
            return t('yii', 'GD installed,<br />FreeType support not installed');
        }
        return t('yii', 'GD or ImageMagick not installed');
    }

    /**
     * Restituisce la versione di Yii attualemente in uso.
     * @return string Versione di Yii
     */
    public static function GetYiiVersion() {
        return Yii::getVersion();
    }

}
