<?php

use yii\helpers\StringHelper;

/**
 * @var yii\web\View $this
 * @var yii\gii\generators\crud\Generator $generator
 */

/** @var \yii\db\ActiveRecord $model */
$model = new $generator->modelClass;
$safeAttributes = $model->safeAttributes();
if (empty($safeAttributes)) {
    $safeAttributes = $model->getTableSchema()->columnNames;
}

echo "<?php\n";
?>

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\widgets\FileInput;

/**
* @var yii\web\View $this
* @var <?= ltrim($generator->modelClass, '\\') ?> $model
* @var yii\widgets\ActiveForm $form
*/
?>

<div class="<?= \yii\helpers\Inflector::camel2id(StringHelper::basename($generator->modelClass), '-', true) ?>-form">

    <?= "<?php " ?>$form = ActiveForm::begin(['layout' => '<?= $generator->formLayout ?>', 'enableClientValidation' => false,  'options' => ['enctype' => 'multipart/form-data']]); ?>

    <div class="">
        <?= "<?php " ?>echo $form->errorSummary($model); ?>
        <?php echo "<?php \$this->beginBlock('main'); ?>\n"; ?>

        <p><?php foreach ($safeAttributes as $attribute) {
                $column   = $generator->getTableSchema()->columns[$attribute];

                $prepend = $generator->prependActiveField($column, $model);
                $field = $generator->activeField($column, $model);
                $append = $generator->appendActiveField($column, $model);

                if ($prepend) {
                    echo str_replace("\n","\n\t\t\t","\n<?= " . $prepend . " ?>");
                }
                if ($field) {
                    echo str_replace("\n","\n\t\t\t","\n<?= " . $field . " ?>");
                }
                if ($append) {
                    echo str_replace("\n","\n\t\t\t","\n<?= " . $append . " ?>");
                }
        } ?></p>
        <?php echo "<?php \$this->endBlock(); ?>"; ?>

        <?php
        $label = substr(strrchr($model::className(), "\\"), 1);;

        $items = <<<EOS
[
    'label'   => '$label',
    'content' => \$this->blocks['main'],
    'active'  => true,
],
EOS;
        ?>

        <?=
        "<?= \yii\bootstrap\Tabs::widget([
            'encodeLabels' => false,
            'items' => [ " . str_replace("\n","\n\t\t\t",$items) . " ]
        ]); ?>";
        ?>

        <hr/>

        <?= "<?= " ?>Html::submitButton('<span class="glyphicon glyphicon-check"></span> '.($model->isNewRecord ? 'Create' : 'Save'), ['class' => $model->isNewRecord ? 'btn btn-primary' : 'btn btn-primary']) ?>

    </div>
    
    <?= "<?php " ?>ActiveForm::end(); ?>

</div>