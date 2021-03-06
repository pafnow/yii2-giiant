<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var yii\web\View $this */
/* @var pafnow\giiant\crud\Generator $generator */

$urlParams = $generator->generateUrlParams();
$nameAttribute = $generator->getNameAttribute();

echo "<?php\n";
?>

use yii\helpers\Html;
use <?= $generator->indexWidgetType === 'grid' ? "yii\\grid\\GridView" : "yii\\widgets\\ListView" ?>;

/* @var $this yii\web\View */
<?= !empty($generator->searchModelClass) ? "/* @var \$searchModel " . ltrim($generator->searchModelClass, '\\') . " */\n" : '' ?>
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '<?= Inflector::pluralize(Inflector::camel2words(StringHelper::basename($generator->modelClass))) ?>';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass), '-', true) ?>-index">
<?php if(!empty($generator->searchModelClass)): ?>
<?= "    <?php " . ($generator->indexWidgetType === 'grid' ? "// " : "") ?>
echo $this->render('_search', ['model' => $searchModel]); ?>
<?php endif; ?>

    <div class="clearfix">
        <p class="pull-left">
            <?= "<?= " ?>Html::a('<span class="glyphicon glyphicon-plus"></span> New <?= Inflector::camel2words(StringHelper::basename($generator->modelClass)) ?>', ['create'], ['class' => 'btn btn-success']) ?>
        </p>

        <div class="pull-right">
<?php
    $items = [];
    $model = new $generator->modelClass;
    foreach ($generator->getModelRelations($model) AS $relation) {
                // relation dropdown links
                $iconType = ($relation->multiple) ? 'arrow-right' : 'arrow-left';
                if ($generator->isPivotRelation($relation)) {
                    $iconType = 'random';
                }
                $controller = $generator->pathPrefix . Inflector::camel2id(
                        StringHelper::basename($relation->modelClass),
                        '-',
                        true
                    );
                $route = $generator->createRelationRoute($relation,'index');
                $label      = Inflector::titleize(StringHelper::basename($relation->modelClass), '-', true);
                $items[] = [
                    'label' => '<i class="glyphicon glyphicon-' . $iconType . '"> ' . $label . '</i>',
                    'url'   => [$route]
                ];
    } 
?>

            <?= "<?=" ?> \yii\bootstrap\ButtonDropdown::widget(
                [
                    'id'       => 'giiant-relations',
                    'encodeLabel' => false,
                    'label'    => '<span class="glyphicon glyphicon-paperclip"></span> Relations',
                    'dropdown' => [
                        'options'      => [
                            'class' => 'dropdown-menu-right'
                        ],
                        'encodeLabels' => false,
                        'items'        => <?= str_replace("\n","\n\t\t\t\t\t\t",\yii\helpers\VarDumper::export($items)) . "\n" ?>
                    ],
                ]
            ); <?= "?>\n" ?>
        </div>
    </div>

<?php if ($generator->indexWidgetType === 'grid'): ?>
    <?= "<?php " ?>echo GridView::widget([
        'dataProvider' => $dataProvider,
<?= !empty($generator->searchModelClass) ? "\t\t'filterModel' => \$searchModel," : ""; ?>
        'columns' => [
<?php
    $count = 0;
    foreach ($generator->getTableSchema()->columns as $column) {
        $format = str_replace("\n","\n\t\t\t",trim($generator->columnFormat($column,$model)));
        if ($format == false) continue;
        if (++$count < 8) {
            echo "\t\t\t{$format},\n";
        } else {
            echo "\t\t\t/*{$format}*/\n";
        }
    }
?>
            [
                'class' => '<?= $generator->actionButtonClass ?>',
                'urlCreator' => function($action, $model, $key, $index) {
                    // using the column name as key, not mapping to 'id' like the standard generator
                    $params = is_array($key) ? $key : [$model->primaryKey()[0] => (string) $key];
                    $params[0] = \Yii::$app->controller->id ? \Yii::$app->controller->id . '/' . $action : $action;
                    return \yii\helpers\Url::toRoute($params);
                },
                'contentOptions' => ['nowrap'=>'nowrap']
            ],
        ],
    ]); ?>
    <?php else: ?>
        <?= "<?php " ?>echo ListView::widget([
        'dataProvider' => $dataProvider,
        'itemOptions' => ['class' => 'item'],
        'itemView' => function ($model, $key, $index, $widget) {
        return Html::a(Html::encode($model-><?= $nameAttribute ?>), ['view', <?= $urlParams ?>]);
        },
        ]); ?>
    <?php endif; ?>

</div>