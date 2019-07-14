<?php


namespace app\controllers;

use app\models\Like;
use app\models\Post;
use app\models\PostField;
use app\models\PostForm;
use Yii;
use yii\filters\auth\HttpBearerAuth;
use yii\rest\Controller;
use yii\web\UploadedFile;

class PostController extends Controller
{


    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::class,
            'only' => ['create', 'delete', 'like'],
        ];
        return $behaviors;
    }

    public function actionCreate() {
        $model = new PostForm();
        $params = Yii::$app->getRequest()->getBodyParams();
        $model->setAttributes($params);
        $model->files = UploadedFile::getInstancesByName('files');
        if ($model->validate()) {
            $post = new Post();
            $post_id = null;

            if ($post->validate() && $post->save()) {
                $post_id = $post->id;
            } else {
                return $post;
            }

            if ($model->text) {
                $field = new PostField();
                $field->load(['post_id' => $post_id, 'value' => $params['text'], 'type' => 'text'],'');
                if (!($field->validate() && $field->save())) {
                    return $field;
                }
            }
            if ($model->files) {
                foreach ($model->files as $file) {
                    $file_name = Yii::$app->security->generateRandomString() . '.' . $file->extension;
                    $file->saveAs(Yii::getAlias('@app/uploads/'.$file_name));
                    $field = new PostField();
                    $field->load(['post_id' => $post_id, 'value' => $file_name, 'type' => 'file'],'');
                    if (!($field->validate() && $field->save())) {
                        return $field;
                    }
                }
            }

           return $model;
        } else {
            return $model;
        }
    }

    public function actionLike($id) {
        $like = Like::findOne(['user_id' => Yii::$app->user->id, 'post_id' => $id]);
        if ($like) {
            $like->delete();
            return Yii::t('app', 'Successful dislike');
        } else {
            $like = new Like();
            $like->post_id = $id;
            if ($like->validate() && $like->save()) {
                return Yii::t('app', 'Successful like');
            } else {
                return $like;
            }
        }
    }
}