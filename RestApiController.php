<?php

/**
 * Superclass per il controller che implementa i servizi REST.
 * Fonte codice: http://www.yiiframework.com/wiki/175/how-to-create-a-rest-api/
 * 
 * Si presuppone che le richieste arrivino con metodo di autenticazione Basic
 * (quello che richiede il parametro CURLOPT_USERPWD) e che utente/password
 * siano quelle di un utente dell'applicazione.
 * 
 * @author Maurizio Cingolani <mauriziocingolani74@gmail.com>
 * @version 1.0
 */
abstract class RestApiController extends Controller {

    /**
     * Metodo di debug che restituisce l'array $_SERVER.
     */
    public function actionEcho() {
        $this->sendResponse(200, CJSON::encode($_SERVER));
    }

    /**
     * Invoca il metodo {@link checkAuth} per verificare i parametri di autenticazione.
     * @param CAction $action
     * @return boolean True
     */
    protected function beforeAction($action) {
        if ($action->id != 'echo')
            $this->checkAuth();
        return true;
    }

    /**
     * Invia la risposta.
     * @param integer $status
     * @param string $body
     * @param string $content_type
     */
    protected function sendResponse($status = 200, $body = '', $content_type = 'text/html') {
        // set the status
        $status_header = 'HTTP/1.1 ' . $status . ' ' . $this->_getStatusCodeMessage($status);
        header($status_header);
        // and the content type
        header('Content-type: ' . $content_type);
        // pages with body are easy
        if ($body != '') {
            // send the body
            echo $body;
        }
        // we need to create the body if none is passed
        else {
            // create some body messages
            $message = '';
            // this is purely optional, but makes the pages a little nicer to read
            // for your users.  Since you won't likely send a lot of different status codes,
            // this also shouldn't be too ponderous to maintain
            switch ($status) {
                case 401:
                    $message = 'You must be authorized to view this page.';
                    break;
                case 404:
                    $message = 'The requested URL ' . $_SERVER['REQUEST_URI'] . ' was not found.';
                    break;
                case 500:
                    $message = 'The server encountered an error processing your request.';
                    break;
                case 501:
                    $message = 'The requested method is not implemented.';
                    break;
            }
            // servers don't always have a signature turned on 
            // (this is an apache directive "ServerSignature On")
            $signature = ($_SERVER['SERVER_SIGNATURE'] == '') ? $_SERVER['SERVER_SOFTWARE'] . ' Server at ' . $_SERVER['SERVER_NAME'] . ' Port ' . $_SERVER['SERVER_PORT'] : $_SERVER['SERVER_SIGNATURE'];
            // this should be templated in a real-world solution
            $body = '
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <title>' . $status . ' ' . $this->_getStatusCodeMessage($status) . '</title>
</head>
<body>
    <h1>' . $this->_getStatusCodeMessage($status) . '</h1>
    <p>' . $message . '</p>
    <hr />
    <address>' . $signature . '</address>
</body>
</html>';
            echo $body;
        }
        Yii::app()->end();
    }

    /**
     * Utilizza la Basic Auth e verifica i parametri di accesso utilizando la classe UserIdentity.
     * Se l'autenticazione fallisce invia una risposta di errore 401.
     */
    protected function checkAuth() {
        if (!(isset($_SERVER['PHP_AUTH_USER']) and isset($_SERVER['PHP_AUTH_PW'])))
            $this->sendResponse(401);
        $username = $_SERVER['PHP_AUTH_USER'];
        $password = $_SERVER['PHP_AUTH_PW'];
        $identity = new UserIdentity($username, $password);
        if (!$identity->authenticate()) :
            $this->sendResponse(401, 'Error: Utente o password non validi');
        endif;
    }

    /**
     * Realizza una mappa codice->descrizione errori. 
     * @param integer $status Codice di errore
     * @return string Messaggio di errore corrispondente
     */
    private function _getStatusCodeMessage($status) {
        $codes = Array(
            200 => 'OK',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
        );
        return (isset($codes[$status])) ? $codes[$status] : '';
    }

}
