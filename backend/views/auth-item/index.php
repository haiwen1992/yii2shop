<?php
//===================加载文件
$this->registerCssFile('@web/DataTables/media/css/jquery.dataTables.css');
$this->registerJsFile('@web/DataTables/media/js/jquery.dataTables.js',[
    //依赖关系
    'depends'=>\yii\web\JqueryAsset::className()
]);
?> <h1>权限列表</h1>
<table id="example" class="display">
    <thead>
    <tr>
        <th>名称</th>
        <th>描述</th>
        <th>操作</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($model as $mdels):?>
        <tr>
            <td><?=$mdels->name?></td>
            <td><?=$mdels->description?></td>
            <td>
<?=\yii\bootstrap\Html::a('修改',['auth-item/edit','name'=>$mdels->name],['class'=>'btn btn-warning'])?>
<?=\yii\bootstrap\Html::a('删除',['auth-item/delete','name'=>$mdels->name],['class'=>'btn btn-warning'])?>
      </td>
     </tr>
    <?php endforeach;?>
    </tbody>
</table>
<?=\yii\bootstrap\Html::a('添加',['auth-item/addit'],['class'=>'btn btn-warning'])?>

<?php
$js=<<<JS
<!--第三步：初始化Datatables-->
$(document).ready( function () {
    $('#table_id_example').DataTable();
} );
$('#example').dataTable({
"oLanguage": {
"sLengthMenu": "每页显示 _MENU_ 条记录",
"sZeroRecords": "对不起，查询不到任何相关数据",
"sInfo": "当前显示 _START_ 到 _END_ 条，共 _TOTAL_条记录",
"sInfoEmtpy": "找不到相关数据",
"sInfoFiltered": "数据表中共为 _MAX_ 条记录)",
"sProcessing": "正在加载中...",
"sSearch": "搜索",
"oPaginate": {
        "sFirst": "第一页",
"sPrevious":" 上一页 ",
"sNext": " 下一页 ",
"sLast": " 最后一页 "
},
}
});
JS;
$this->registerJs($js);
//?>

