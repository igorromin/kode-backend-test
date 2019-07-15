<?php

namespace app\models;

use Yii;
use paulzi\jsonBehavior\JsonBehavior;

/**
 * This is the model class for table "posts_fields".
 *
 * @property int $id
 * @property int $post_id
 * @property string $type
 * @property string $value
 *
 * @property Posts $post
 */
class PostField extends \yii\db\ActiveRecord
{
    private $_isJson;

    public function __construct($isJson = false, $config = []) {
        $this->_isJson = $isJson;
        parent::__construct($config);
    }

    /**
     * @return bool
     */
    public function getIsJson() {
        return $this->_isJson;
    }

    /**
     * @param bool $isJson
     */
    public function setIsJson($isJson) {
        $this->_isJson = $isJson;
    }

    public function behaviors() {
        if ($this->isJson) {
            return [['class'      => JsonBehavior::className(),
                     'attributes' => ['value'],],];
        } else {
            return [];
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'posts_fields';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['post_id', 'type', 'value'], 'required'],
            [['post_id'], 'integer'],
            [['type'], 'string', 'max' => 16],
            [['post_id'], 'exist', 'skipOnError' => true, 'targetClass' => Post::class, 'targetAttribute' => ['post_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'post_id' => 'Post ID',
            'type' => 'Type',
            'value' => 'Value',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPost()
    {
        return $this->hasOne(Post::class, ['id' => 'post_id']);
    }


}
