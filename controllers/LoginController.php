<?php

namespace app\controllers;

use app\models\LoginForm;
use yii\rest\Controller;

class LoginController extends Controller
{
    public function actionCreate() {
        $model = new LoginForm();
        if ($model->load(\Yii::$app->getRequest()->getBodyParams(), '') && $model->login()) {
            return [
                'access_token' => \Yii::$app->user->identity->getAuthKey(),
            ];
        } else {
            return $model;
        }
    }
}