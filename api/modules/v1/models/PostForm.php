<?php

namespace app\api\modules\v1\models;

use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class PostForm extends Model
{
    public $text;
    public $files;
    public $link;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['files'], 'file', 'extensions' => 'jpg, png, gif, flv, mp4, m3u8, ts, 3gp, mov, avi, wmv', 'maxFiles' => 10],
            ['link', 'url'],
            [['text'], 'atLeastOne', 'skipOnEmpty' => false, 'params' => ['in' => ['text','files', 'link']]],
        ];
    }

    public function atLeastOne($attribute, $params)
    {
        if($this->is_array_empty($this->getAttributes($params['in']))) {
            $this->addError($attribute, \Yii::t('app', 'You must fill at least one of the attributes {attributes}!', [
                'attributes' => implode(', ', $params['in']),
            ]));
        }
    }

    private function is_array_empty($arr){
        if(is_array($arr)){
            foreach($arr as $key => $value){
                if($value !== 0 && !empty($value)){
                    return false;
                    break;
                }
            }
            return true;
        }
    }
}
