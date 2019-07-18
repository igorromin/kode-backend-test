<?php

namespace app\models;

use Yii;
use paulzi\jsonBehavior\JsonBehavior;
use yii\helpers\Url;

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
    public static $uploadPath;
    const UPLOAD_DIR = "uploads/";

    /**
     * @return string
     */
    public static function getUploadPath() {
        return self::$uploadPath;
    }

    public function init() {
        parent::init();
        self::$uploadPath = Yii::getAlias('@main/'. self::UPLOAD_DIR);
    }

    public function behaviors() {
        return [
            ['class'      => JsonBehavior::class,
             'attributes' => ['value'],
            ],
        ];
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

    public function fields() {
        $fields = parent::fields();
        unset($fields['id']);
        unset($fields['post_id']);
        if ($this->type == 'file') {
            $fields['value'] = function () {
                return Url::to('@mainweb/'.self::UPLOAD_DIR.$this->value['field'], true);
            };
        }
        return $fields;
    }
}
