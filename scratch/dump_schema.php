<?php
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../vendor/yiisoft/yii2/Yii.php';
require __DIR__ . '/../common/config/bootstrap.php';
require __DIR__ . '/../console/config/bootstrap.php';

$config = yii\helpers\ArrayHelper::merge(
    require __DIR__ . '/../common/config/main.php',
    require __DIR__ . '/../common/config/main-local.php',
    require __DIR__ . '/../console/config/main.php',
    require __DIR__ . '/../console/config/main-local.php'
);

$application = new yii\console\Application($config);

$tableSchema = Yii::$app->db->getTableSchema('v2_sakip_tujuanrenstra_p');
if ($tableSchema) {
    echo "Columns of v2_sakip_tujuanrenstra_p:\n";
    foreach ($tableSchema->columnNames as $name) {
        echo "- $name\n";
    }
} else {
    echo "Table v2_sakip_tujuanrenstra_p not found.\n";
}
