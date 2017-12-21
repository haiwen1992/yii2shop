<table id="table" class="table table-bordered">
    <tr>
        <th>id</th>
        <th>名称</th>
        <th>简介</th>
        <th>排序</th>
        <th>状态</th>
        <th>操作</th>
    </tr>
    <?php foreach ($article as $articles):?>
        <tr>
            <td><?=$articles->id?></td>
            <td><?=$articles->name?></td>
            <td><?=$articles->intro?></td>
            <td><?=$articles->sort?></td>
            <td><?=$articles->status==-1?'删除':''?>
                <?=$articles->status==0?'隐藏':''?>
                <?=$articles->status==1?'正常':''?>
            </td>

            <td>
        <?=\yii\bootstrap\Html::a('修改',['article-category/edit','id'=>$articles->id],['class'=>'btn btn-warning'])?>
        <?=\yii\bootstrap\Html::a('删除',['article-category/delete','id'=>$articles->id],['class'=>'btn btn-warning'])?>
            </td>
        </tr>
    <?php endforeach;?>

</table>
<?=\yii\bootstrap\Html::a('添加',['article-category/add'],['class'=>'btn btn-warning'])?>
<?php
/*
 * @var $this \yii\web\View
 */
$url = \Yii\helpers\Url::to(['article-category/delete']);
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


