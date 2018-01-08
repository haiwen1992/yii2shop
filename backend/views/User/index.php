<table id="table" class="table table-bordered">
    <h1>管理员表</h1>
    <tr>
        <th>id</th>
        <th>用户名</th>
        <th>邮箱</th>
        <th>操作</th>
    </tr>
    <?php foreach ($model as $models):?>
        <tr>
            <td><?=$models->id?></td>
            <td><?=$models->username?></td>
            <td><?=$models->email?></td>
            <td>
        <?=\yii\bootstrap\Html::a('修改',['user/edit','id'=>$models->id],['class'=>'btn btn-warning'])?>
      <?=\yii\bootstrap\Html::a('删除',['user/delete','id'=>$models->id],['class'=>'btn btn-warning'])?>
<!--                <a id="delete" class="btn btn-warning">删除</a>-->
            </td>
        </tr>
    <?php endforeach;?>

</table>
<?=\yii\bootstrap\Html::a('添加',['user/add'],['class'=>'btn btn-warning'])?>