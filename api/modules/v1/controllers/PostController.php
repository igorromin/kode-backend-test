<?php


namespace app\api\modules\v1\controllers;

use app\models\Like;
use app\models\Post;
use app\models\PostField;
use app\api\modules\v1\models\PostForm;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\VerbFilter;
use yii\rest\Controller;
use yii\web\HttpException;
use yii\web\UploadedFile;

class PostController extends Controller
{
    public $serializer = [
        'class' => 'app\components\DefaultSerializer',
        'collectionEnvelope' => 'items'
    ];

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::class,
            'only' => ['create', 'delete', 'like'],
        ];

        return $behaviors;
    }

    public function actionIndex() {
        return new ActiveDataProvider([
            'query' => Post::find()->where(['=', 'deleted_at', 0]),
        ]);
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
                $field->load(['post_id' => $post_id, 'value' => ['field' => $params['text']], 'type' => 'text'],'');
                if (!($field->validate() && $field->save())) {
                    return $field;
                }
            }
            if ($model->files) {
                foreach ($model->files as $file) {
                    $file_name = Yii::$app->security->generateRandomString() . '.' . $file->extension;
                    $file->saveAs(Yii::getAlias(PostField::$uploadPath.$file_name));
                    $field = new PostField();
                    $field->load(['post_id' => $post_id, 'value' => ['field' => $file_name], 'type' => 'file'],'');
                    if (!($field->validate() && $field->save())) {
                        return $field;
                    }
                }
            }
            if ($model->link) {
                $field = new PostField();
                $preview = PreviewController::getPreviewByUrl($model->link);
                $link_array = ['field' => $model->link, 'info' => $preview];
                $field->load(['post_id' => $post_id, 'value' => $link_array,  'type' => 'link'],'');
                //$field->value->set(json_encode($link_array, JSON_UNESCAPED_UNICODE));
                if (!($field->validate() && $field->save())) {
                    return $field;
                }
            }
            return $post;
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

    public function actionDelete($id) {
        $post = Post::find()->where(['=', 'deleted_at', 0])->andWhere(['=', 'id', $id])->one();
        if ($post && $post->user_id == Yii::$app->user->id) {
            $post->deleted_at = time();
            $post->save();
            return Yii::t('app', 'Successful deleted');
        } elseif ($post) {
            throw new HttpException(403, Yii::t('app', 'You are not a owner'));
        } else {
            throw new HttpException(404, Yii::t('app', 'Post not found'));
        }
    }

    public function actionView($id) {
        $this->serializer['defaultExpand'] = ['likes'];
        $post = Post::find()->where(['=', 'deleted_at', 0])->andWhere(['=', 'id', $id])->one();
        if ($post) {
            return $post;
        } else {
            throw new HttpException(404, Yii::t('app', 'Post not found'));
        }
    }

}