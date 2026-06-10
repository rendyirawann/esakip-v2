<?php

namespace backend\controllers;

use Yii;
use backend\models\User;
use backend\models\SakipKoordinasi;
use yii\data\ActiveDataProvider;
use backend\models\search\UserSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all User models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        $dataProvider->pagination = false;

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single User model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        // Buat query untuk data sakip_koordinasi yang terkait dengan user ini
        $query = SakipKoordinasi::find()->where(['refuser_id' => $id]);

        // Buat DataProvider dengan query dan pengaturan paginasi
        $koordinasiDataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 3, // Tampilkan 3 data per halaman
            ],
            'sort' => [
                // Urutkan berdasarkan nama SKPD jika diinginkan
                'attributes' => [
                    'refskpd_id' => [
                        'asc' => ['sakip_skpd.nama_skpd' => SORT_ASC],
                        'desc' => ['sakip_skpd.nama_skpd' => SORT_DESC],
                    ],
                ],
            ],
        ]);

        // Tambahkan join dengan tabel SKPD agar bisa di-sort
        $query->joinWith('refSkpd');

        // Kirim model dan dataProvider ke view
        return $this->render('view', [
            'model' => $model,
            'koordinasiDataProvider' => $koordinasiDataProvider, // Kirim variabel baru ini
        ]);
    }

    /**
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new User();

        if (Yii::$app->request->isAjax) {
            if ($model->load(Yii::$app->request->post())) {
                // Generate auth_key
                $model->generateAuthKey();

                // Generate password hash
                $model->setPassword($model->password_hash);
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', 'Berhasil Tambah Data');
                    return $this->asJson(['success' => true, 'redirect' => \yii\helpers\Url::to(['view', 'id' => $model->id])]);
                } else {
                    // Tambahkan log error
                    Yii::error("Error saving data: " . json_encode($model->getErrors()));
                    return $this->asJson(['success' => false, 'errors' => $model->getErrors()]);
                }
            }

            return $this->renderAjax('_form', [
                'model' => $model,
            ]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Sakipperiode model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id Refperiode ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        // Simpan password sebelum di-update
        $oldPassword = $model->password_hash;

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            // Periksa apakah ada perubahan pada password
            if (!empty($model->password_hash) && $model->password_hash !== $oldPassword) {
                // Hash password baru
                $model->setPassword($model->password_hash);
            } else {
                // Jika tidak ada perubahan, kembalikan password ke nilai sebelumnya
                $model->password_hash = $oldPassword;
            }

            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Berhasil Memperbarui Data');
                return $this->asJson(['success' => true, 'redirect' => \yii\helpers\Url::to(['view', 'id' => $model->id])]);
            } else {
                // Tambahkan log error
                Yii::error("Error saving data: " . json_encode($model->getErrors()));
                return $this->asJson(['success' => false, 'errors' => $model->getErrors()]);
            }
        }

        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('_form', [
                'model' => $model,
            ]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }


    /**
     * Deletes an existing User model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
