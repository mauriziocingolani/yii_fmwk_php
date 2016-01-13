<?php

/**
 * Rappresenta un record della tabella 'YiiTweets'
 *
 * @author Maurizio Cingolani <mauriziocingolani74@gmail.com>
 * @version 1.0
 */
class TwitterTweet extends CActiveRecord {

    public $id;
    public $id_str;
    public $created;
    public $text;

    public function tableName() {
        return 'YiiTweets';
    }

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public static function CreateFromObj($data) {
        $tweet = self::model()->find(array(
            'condition' => 'id_str=:idstr',
            'params' => array(':idstr' => $data->id_str),
        ));
        if (!$tweet) :
            $tweet = new TwitterTweet;
            $tweet->id_str = $data->id_str;
            $tweet->created = date('Y-m-d H:i:s', strtotime($data->created_at));
            $tweet->text = Twitter::GetTweetText($data);
            $tweet->save();
            $tweet->refresh();
            return $tweet;
        endif;
        return true;
    }

}
