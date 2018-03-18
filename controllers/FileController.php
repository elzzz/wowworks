<?php

namespace app\controllers;

use app\models\File;
use app\modules\api\models\Image;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

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
                foreach ($model->getImagesPath() as $i => $image) {
                    $modelImage = new Image();
                    $modelImage->path = $image;
                    $modelImage->file_id = $model->id;
                    $modelImage->number = $i+1;
                    $modelImage->url = Url::to('@web/file/'.$model->id.'/'.($i+1), true);
                    $modelImage->save();
                }

                return $this->redirect(['view', 'id' => $model->id]);
            }
        }
        return $this->render('upload', [
            'model' => $model,
        ]);
    }

    /**
     * Downloads zip archive of File model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDownload($id)
    {
        $this->layout = false;

        return $this->render('download', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Shows picture selected file of File model.
     * @param integer $id
     * @param integer $imgId
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionImage($id, $imgId)
    {
        $this->layout = false;

        return $this->render('image', [
            'model' => $this->findImage($id, $imgId),
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
            if (strtotime('now') < strtotime($model->deleted_at)) {
                return $model;
            } else {
                throw new NotFoundHttpException('File has been deleted.');
            }
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Finds the Image model based on its primary key value and foreign key of the File model.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @param integer $imgId
     * @return Image the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findImage($id, $imgId)
    {
        if (($modelImage = Image::findOne(['file_id'=>$id, 'number'=>$imgId])) !== null) {
            return $modelImage;
        } else {
            throw new NotFoundHttpException('No such image.');
        }
    }
}
