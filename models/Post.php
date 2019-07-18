<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "posts".
 *
 * @property int $id
 * @property int $user_id
 * @property int $created_at
 * @property int $deleted_at
 *
 * @property Users $user
 * @property PostFields[] $postFields
 * @property Likes[] $likes
 */
class Post extends ActiveRecord
{
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at']
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'posts';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'created_at', 'deleted_at'], 'integer'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'created_at' => 'Created At',
            'deleted_at' => 'Deleted At',
        ];
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            $this->user_id = Yii::$app->user->id;
            $this->deleted_at = 0;
            return true;
        }
        return false;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPostFields()
    {
        return $this->hasMany(PostField::class, ['post_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLikes()
    {
        return $this->hasMany(Like::class, ['post_id' => 'id']);
    }

    public function fields() {
        $fields = parent::fields();
        unset($fields['user_id'], $fields['deleted_at']);
        $fields['user'] = 'user';
        $fields['post_fields'] = 'postFields';
        $fields['likes'] = function () {
            return (int)$this->getLikes()->count();
        };
        return $fields;
    }

    public function extraFields() {
        return ['likes'];
    }

}
