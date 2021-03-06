<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\grid\ActionColumn;
?>
<?php 
/**
 * @var  $this yii\web\View
 * @var  $filterModel catalog\models\filters\BrandFilter
 * @var  $dataProvider yii\data\ActiveDataProvider
 * 
 */
$this->title = Yii::t('app', 'Manage brands');
?>
<div class="grid-buttons">
    <?= Html::a(Yii::t('app', 'Add new brand'), ['create'], [
         'class' => 'btn btn-sm btn-primary',
    ])?>
</div>
<?= GridView::widget([
    'id' => 'catalog_brand_grid',
    'filterModel' => $filterModel,
    'dataProvider' => $dataProvider,
    'columns' => [
        'id',
        'name',
        [
            'class'  => ActionColumn::class,
            'header' => Yii::t('app', 'Action'),
            'template' => '{update} {delete}',
        ],
    ],
])?>