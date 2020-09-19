<?php

namespace frontend\controllers;

use common\components\BaseController;
use frontend\models\Goods;
use yii\bootstrap4\LinkPager;
use yii\data\ActiveDataProvider;
use yii\data\Pagination;
use yii\web\NotFoundHttpException;

/**
 * Site controller
 */
class StoreController extends BaseController
{
    public $stylesName;

    public function actionView($id){
        $model = Goods::findOne(['id'=>$id]);
        $this->stylesName = 'product-p';
        if (!$model){
            throw  new NotFoundHttpException('Not Found');
        }
        if ($model->images && gettype($model->images) == 'string')
        {
            $model->images = json_decode($model->images);
        }
        if ($model->part_number_list && gettype($model->part_number_list) == 'string')
        {
            $model->part_number_list = json_decode($model->part_number_list);
        }
        if ($model->comparison_number_list && gettype($model->comparison_number_list) == 'string')
        {
            $model->comparison_number_list = json_decode($model->comparison_number_list);
        }

        return $this->render('view',['model'=>$model]);
    }

    public function actionProducts()
    {
        return $this->render('products');
    }

    public function actionTurboActuator()
    {
        $query = Goods::find()->where(['category_id' => 5]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 12,
            ],

        ]);
        $title = 'TURBO ACTUATOR';
        return $this->render('turbo-actuator', ['dataProvider' => $dataProvider, 'title' => $title]);
    }

    public function actionTurboActuatorPositionSensor()
    {
        $query = Goods::find()->where(['category_id' => 6]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 12,
            ],

        ]);
        $title = 'TURBO ACTUATOR POSITION SENSOR';
        return $this->render('turbo-actuator-position-sensor', ['dataProvider' => $dataProvider, 'title' => $title]);
    }

    public function actionTurboCharger()
    {
        $query = Goods::find()->where(['category_id' => 7]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 12,
            ],

        ]);
        $title = 'TURBO CHARGER';
        return $this->render('turbo-charger', ['dataProvider' => $dataProvider, 'title' => $title]);
    }

    public function actionTurboCleaner()
    {
        $query = Goods::find()->where(['category_id' => 8]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 12,
            ],

        ]);
        $title = 'TURBO CLEANER';
        return $this->render('turbo-cleaner', ['dataProvider' => $dataProvider, 'title' => $title]);
    }
}