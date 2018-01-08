<h1>角色列表</h1>
<table id="example" class="table table-responsive">
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
            <?=\yii\bootstrap\Html::a('修改',['auth-item/editr','name'=>$mdels->name],['class'=>'btn btn-warning'])?>
            <?=\yii\bootstrap\Html::a('删除',['auth-item/deleter','name'=>$mdels->name],['class'=>'btn btn-warning'])?>
        </td>
    </tr>
<?php endforeach;?>
</tbody>
</table>
<?=\yii\bootstrap\Html::a('添加',['auth-item/addr'],['class'=>'btn btn-warning'])?>
