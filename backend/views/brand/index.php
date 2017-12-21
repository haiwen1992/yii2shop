<table id="table" class="table table-bordered">
    <tr>
        <th>id</th>
        <th>名称</th>
        <th>简介</th>
        <th>LOGO图片</th>
        <th>排序</th>
        <th>状态</th>
        <th>操作</th>
    </tr>
    <?php foreach ($brand as $brands):?>
        <tr>
            <td><?=$brands->id?></td>
            <td><?=$brands->name?></td>
            <td><?=$brands->intro?></td>
            <td><img src="<?=$brands->logo?>" width="100px"  /></td>
            <td><?=$brands->sort?></td>
            <td><?=$brands->status==-1?'删除':''?>
                <?=$brands->status==0?'隐藏':''?>
                <?=$brands->status==1?'正常':''?>
            </td>

            <td>
                <?=\yii\bootstrap\Html::a('修改',['brand/edit','id'=>$brands->id],['class'=>'btn btn-warning'])?>
                <?=\yii\bootstrap\Html::a('删除',['brand/delete','id'=>$brands->id],['class'=>'btn btn-warning'])?>
            </td>
        </tr>
    <?php endforeach;?>

</table>
<?=\yii\bootstrap\Html::a('添加',['brand/add'],['class'=>'btn btn-warning'])?>
<?php
/*
 * @var $this \yii\web\View
 */
$url = \Yii\helpers\Url::to(['Brand/delete']);
$js=<<<JS
   $("#table").on('click','.btn-warning',function(){
       var tr = $(this).closest('tr');
           tr.remove();
           $.get("$url",{id:tr.attr('id')},function(){   
           });
   });
JS;
$this->registerJs($js);
?>





