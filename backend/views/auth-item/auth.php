<?php
$form = \yii\bootstrap\ActiveForm::begin([]);
//===================加载文件
$this->registerCssFile('@web/DataTables/media/css/jquery.dataTables.css');
$this->registerJsFile('@web/DataTables/media/css/jquery.dataTables.js',[
    //依赖关系
    'depends'=>\yii\web\JqueryAsset::className()
]);
$this->registerJsFile('@web/DataTables/media/js/jquery.dataTables.js',[
    //依赖关系
    'depends'=>\yii\web\JqueryAsset::className()
]);
//$html = <<<html
//<!--或者下载到本地，下面有下载地址-->
//<!-- DataTables CSS -->
//<link rel="stylesheet" type="text/css" href="./web/DataTables/media/css/jquery.dataTables.css">
//
//<!-- jQuery -->
//<script type="text/javascript" charset="utf8" src="./web/DataTables/media/css/jquery.dataTables.js"></script>
//
//<!-- DataTables -->
//<script type="text/javascript" charset="utf8" src="./web/DataTables/media/js/jquery.dataTables.js"></script>
//
//<!--第二步：添加如下 HTML 代码-->
//
//<table id="table_id_example" class="display">
//    <thead>
//    <tr>
//        <th>Column 1</th>
//        <th>Column 2</th>
//    </tr>
//    </thead>
//    <tbody>
//    <tr>
//        <td>Row 1 Data 1</td>
//        <td>Row 1 Data 2</td>
//    </tr>
//    <tr>
//        <td>Row 2 Data 1</td>
//        <td>Row 2 Data 2</td>
//    </tr>
//    </tbody>
//</table>
//html;
//
//
$js = <<<js
<!--第三步：初始化Datatables-->
$(document).ready( function () {
$('#table_id_example').DataTable();
} );
js;
//?>





