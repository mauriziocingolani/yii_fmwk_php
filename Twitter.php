<?php

/**
 * Componente per mostrare le ultime modifiche postate su Twitter.
 * Si appoggia alla tabella 'YiiTweets' del database.
 *
 * @author Maurizio Cingolani <mauriziocingolani74@gmail.com>
 * @version 1.1
 */
class Twitter extends CApplicationComponent {

    public $oauth_access_token;
    public $oauth_access_token_secret;
    public $consumer_key;
    public $consumer_secret;
    public $hashtag;
    public $screenName;
    private $postfields;
    private $getfield;
    protected $oauth;
    public $url;

    public function getTweets($limit = null) {
        $url = 'https://api.twitter.com/1.1/statuses/user_timeline.json';
        $getfield = "?count=3200&screen_name=$this->screenName";
        $requestMethod = 'GET';
        $tweets = json_decode($this->setGetfield($getfield)
                        ->buildOauth($url, $requestMethod)
                        ->performRequest());
        $tws = array();
        foreach ($tweets as $tw) :
            foreach ($tw->entities->hashtags as $ht) :
                if ($ht->text === $this->hashtag) :
                    $tweet = TwitterTweet::CreateFromObj($tw);
                    if ($tweet === true)
                        break 2;
                endif;
            endforeach;
        endforeach;
        $crit = new CDbCriteria;
        $crit->order = 'created DESC';
        if ($limit)
            $crit->limit = $limit;
        return TwitterTweet::model()->findAll($crit);
    }

    /**
     * Set postfields array, example: array('screen_name' => 'J7mbo')
     * 
     * @param array $array Array of parameters to send to API
     * 
     * @return TwitterAPIExchange Instance of self for method chaining
     */
    public function setPostfields(array $array) {
        if (!is_null($this->getGetfield())) {
            throw new Exception('You can only choose get OR post fields.');
        }

        if (isset($array['status']) && substr($array['status'], 0, 1) === '@') {
            $array['status'] = sprintf("\0%s", $array['status']);
        }

        $this->postfields = $array;

        return $this;
    }

    /**
     * Set getfield string, example: '?screen_name=J7mbo'
     * 
     * @param string $string Get key and value pairs as string
     * 
     * @return \TwitterAPIExchange Instance of self for method chaining
     */
    public function setGetfield($string) {
        if (!is_null($this->getPostfields())) {
            throw new Exception('You can only choose get OR post fields.');
        }

        $search = array('#', ',', '+', ':');
        $replace = array('%23', '%2C', '%2B', '%3A');
        $string = str_replace($search, $replace, $string);

        $this->getfield = $string;

        return $this;
    }

    /**
     * Get getfield string (simple getter)
     * 
     * @return string $this->getfields
     */
    public function getGetfield() {
        return $this->getfield;
    }

    /**
     * Get postfields array (simple getter)
     * 
     * @return array $this->postfields
     */
    public function getPostfields() {
        return $this->postfields;
    }

    /**
     * Build the Oauth object using params set in construct and additionals
     * passed to this method. For v1.1, see: https://dev.twitter.com/docs/api/1.1
     * 
     * @param string $url The API url to use. Example: https://api.twitter.com/1.1/search/tweets.json
     * @param string $requestMethod Either POST or GET
     * @return \TwitterAPIExchange Instance of self for method chaining
     */
    private function buildOauth($url, $requestMethod) {
        if (!in_array(strtolower($requestMethod), array('post', 'get'))) {
            throw new Exception('Request method must be either POST or GET');
        }

        $consumer_key = $this->consumer_key;
        $consumer_secret = $this->consumer_secret;
        $oauth_access_token = $this->oauth_access_token;
        $oauth_access_token_secret = $this->oauth_access_token_secret;

        $oauth = array(
            'oauth_consumer_key' => $consumer_key,
            'oauth_nonce' => time(),
            'oauth_signature_method' => 'HMAC-SHA1',
            'oauth_token' => $oauth_access_token,
            'oauth_timestamp' => time(),
            'oauth_version' => '1.0'
        );

        $getfield = $this->getGetfield();

        if (!is_null($getfield)) {
            $getfields = str_replace('?', '', explode('&', $getfield));
            foreach ($getfields as $g) {
                $split = explode('=', $g);
                $oauth[$split[0]] = $split[1];
            }
        }

        $base_info = $this->buildBaseString($url, $requestMethod, $oauth);
        $composite_key = rawurlencode($consumer_secret) . '&' . rawurlencode($oauth_access_token_secret);
        $oauth_signature = base64_encode(hash_hmac('sha1', $base_info, $composite_key, true));
        $oauth['oauth_signature'] = $oauth_signature;

        $this->url = $url;
        $this->oauth = $oauth;

        return $this;
    }

    /**
     * Perform the actual data retrieval from the API
     * 
     * @param boolean $return If true, returns data.
     * 
     * @return string json If $return param is true, returns json data.
     */
    private function performRequest($return = true) {
        if (!is_bool($return)) {
            throw new Exception('performRequest parameter must be true or false');
        }

        $header = array($this->buildAuthorizationHeader($this->oauth), 'Expect:');

        $getfield = $this->getGetfield();
        $postfields = $this->getPostfields();

        $options = array(
            CURLOPT_HTTPHEADER => $header,
            CURLOPT_HEADER => false,
            CURLOPT_URL => $this->url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 10,
        );

        if (!is_null($postfields)) {
            $options[CURLOPT_POSTFIELDS] = $postfields;
        } else {
            if ($getfield !== '') {
                $options[CURLOPT_URL] .= $getfield;
            }
        }

        $feed = curl_init();
        curl_setopt_array($feed, $options);
        $json = curl_exec($feed);
        curl_close($feed);

        if ($return) {
            return $json;
        }
    }

    /**
     * Private method to generate the base string used by cURL
     * 
     * @param string $baseURI
     * @param string $method
     * @param array $params
     * 
     * @return string Built base string
     */
    private function buildBaseString($baseURI, $method, $params) {
        $return = array();
        ksort($params);

        foreach ($params as $key => $value) {
            $return[] = "$key=" . $value;
        }

        return $method . "&" . rawurlencode($baseURI) . '&' . rawurlencode(implode('&', $return));
    }

    /**
     * Private method to generate authorization header used by cURL
     * 
     * @param array $oauth Array of oauth data generated by buildOauth()
     * 
     * @return string $return Header used by cURL for request
     */
    private function buildAuthorizationHeader($oauth) {
        $return = 'Authorization: OAuth ';
        $values = array();

        foreach ($oauth as $key => $value) {
            $values[] = "$key=\"" . rawurlencode($value) . "\"";
        }

        $return .= implode(', ', $values);
        return $return;
    }

    /**
     * Rimpiazza gli hashtag e i link con gli opportuni elementi HTML.
     * Gli hashtag vengono chiusi in un tag <span>, mentre i link vengono
     * rimpiazzati con un tag <a>.
     * @param type $tweet Oggetto del tweet
     * @return string Testo del tweet risistemato
     */
    public static function GetTweetText($d) {
        $text = trim(str_replace('#' . Yii::app()->twitter->hashtag, '<span class="hashtag">#' . Yii::app()->twitter->hashtag . '</span>', $d->text));
        if (isset($d->entities) && is_array($d->entities->urls)) :
            foreach ($d->entities->urls as $url) :
                $text = str_replace($url->url, Html::link($url->display_url, $url->expanded_url, array('target' => 'blank')), $text);
            endforeach;
        endif;
        return $text;
    }

}
