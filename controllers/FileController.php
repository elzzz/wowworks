<?php

namespace app\controllers;


use Yii;
use app\models\File;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use app\modules\api\models\Image;

/**
 * FileController implements the CRUD actions for File model.
 */
class FileController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all File models.
     * @return mixed
     */
    public function actionIndex()
    {

//        Yii::$app->queue->push(new DeleteFromFile());
        $dataProvider = new ActiveDataProvider([
            'query' => File::find()->select('*')->where('CURRENT_TIMESTAMP <= deleted_at'),
            'sort' => ['defaultOrder' => ['uploaded_at' => SORT_DESC]],
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single File model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new File model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionUpload()
    {
        $model = new File();

        if ($model->load(Yii::$app->request->post())) {

            $model->pdfFile = UploadedFile::getInstance($model, 'pdfFile');
            $model->name = uniqid();
            $model->size = round($model->pdfFile->size / (1024 * 1024), 2);
            $model->extension = $model->pdfFile->extension;

            if ($model->save()) {

                foreach ($model->getImages() as $i=>$image) {
                    $modelImage = new Image();
                    $modelImage->url = $image;
                    $modelImage->file_id = $model->id;
                    $modelImage->number = $i+1;
                    $modelImage->save();
                }

                return $this->redirect(['view', 'id' => $model->id]);
            }
        }
        return $this->render('upload', [
            'model' => $model,
        ]);
    }

    public function actionDownload($id)
    {
        $this->layout = false;

        return $this->render('download', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionImage($id, $img_id)
    {
        $this->layout = false;

        return $this->render('image', [
            'model' => $this->findImage($id, $img_id),
        ]);
    }

    /**
     * Finds the File model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return File the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = File::findOne($id)) !== null) {
            if (strtotime('now') < strtotime($model->deleted_at)){
                return $model;
            } else {
                throw new NotFoundHttpException('File has been deleted.');
            }
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    protected function findImage($id, $img_id) {
        if (($modelImage = Image::findOne(['file_id'=>$id, 'number'=>$img_id])) !== null) {
            return $modelImage;
        } else {
            throw new NotFoundHttpException('No such image.');
        }

    }
}
