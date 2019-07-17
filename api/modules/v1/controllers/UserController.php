<?php


namespace app\api\modules\v1\controllers;

use app\api\modules\v1\models\LoginForm;
use yii\rest\Controller;
use yii\web\HttpException;

class UserController extends Controller
{
    public function actionLogin() {
        $model = new LoginForm();
        if ($model->load(\Yii::$app->getRequest()->getBodyParams(), '') && $model->login()) {
            return [
                'access_token' => \Yii::$app->user->identity->getAuthKey(),
            ];
        } else {
            //Если вернуть модель - проставит статус 422, что не совсем синтаксически верно, поэтому выбрасываю Exception
            throw new HttpException(401, $model->getFirstError('password'));
        }
    }

}