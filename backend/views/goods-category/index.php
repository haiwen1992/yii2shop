<table id="table" class="table table-bordered">
    <h1>商品分类列表</h1>
    <tr>
        <th>id</th>
        <th>名称</th>
        <th>所属分类</th>
        <th>简介</th>
        <th>操作</th>
    </tr>
    <?php foreach ($model as $models):?>
        <tr>
            <td><?=$models->id?></td>
            <td><?=$models->name?></td>
            <td><?=$models->parent_id?></td>
            <td><?=$models->intro?></td>
            <td>
                <?=\yii\bootstrap\Html::a('修改',['goods-category/edit','id'=>$models->id],['class'=>'btn btn-warning'])?>
    <?=\yii\bootstrap\Html::a('删除',['goods-category/delete','id'=>$models->id],['class'=>'btn btn-warning'])?>
</td>
        </tr>
    <?php endforeach;?>

</table>
<?=\yii\bootstrap\Html::a('添加',['goods-category/add'],['class'=>'btn btn-warning'])?>