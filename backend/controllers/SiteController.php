<?php

namespace backend\controllers;

use common\models\LoginForm;
use common\models\UserAttemptlogin; // Pastikan nama kelas sesuai
use Yii;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;

/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['login', 'error'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout', 'index'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => \yii\web\ErrorAction::class,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        $attempts = UserAttemptLogin::find()->orderBy(['user_lastlogin' => SORT_DESC])->all();

        return $this->render('index', [
            'attempts' => $attempts,
        ]);
    }

    /**
     * Login action.
     *
     * @return string|Response
     */
    // public function actionLogin()
    // {
    //     if (!Yii::$app->user->isGuest) {
    //         return $this->goHome();
    //     }

    //     $this->layout = 'blank';

    //     $model = new LoginForm();
    //     if ($model->load(Yii::$app->request->post()) && $model->login()) {
    //         return $this->goBack();
    //     }

    //     $model->password = '';

    //     return $this->render('login', [
    //         'model' => $model,
    //     ]);
    // }

    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->redirect(['index']);
        }

        $this->layout = 'blank';
        $model = new LoginForm();

        if (Yii::$app->request->isPost) {
            if ($model->load(Yii::$app->request->post()) && $model->login()) {
                Yii::$app->session->setFlash('success', 'Login berhasil! Anda akan dialihkan ke Pilihan Dashboard dalam 5 detik.');
            } else {
                Yii::$app->session->setFlash('error', 'Username atau password salah!');
            }
        }

        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        $user = Yii::$app->user->identity;
        $user->user_isonline = 'F'; // Set user offline
        $user->save(false); // Simpan perubahan tanpa validasi

        Yii::$app->user->logout();

        return $this->goHome();
    }
}
