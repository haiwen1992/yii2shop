<table id="table" class="table table-bordered">
    <h1>菜单列表</h1>
    <tr>
        <th>id</th>
        <th>名称</th>
        <th>地址(路由)</th>
        <th>排序</th>
        <th>操作</th>
    </tr>
    <?php foreach ($menu as $menus):?>
        <tr id="<?=$menus->id?>">
            <td><?=$menus->name?></td>
            <td><?=$menus->superior_menu?></td>
            <td><?=$menus->route?></td>
            <td><?=$menus->sort?></td>
            <td>
                <?=\yii\bootstrap\Html::a('修改',['menu/edit','id'=>$menus->id],['class'=>'btn btn-warning'])?>
                <?=\yii\bootstrap\Html::a('删除',['menu/delete','id'=>$menus->id],['class'=>'btn btn-warning'])?>
            </td>
        </tr>
    <?php endforeach;?>

</table>
<?=\yii\bootstrap\Html::a('添加',['menu/add'],['class'=>'btn btn-warning'])?>