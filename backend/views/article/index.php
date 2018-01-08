<table id="table" class="table table-bordered">
    <h1>文章列表</h1>
    <tr>
        <th>id</th>
        <th>名称</th>
        <th>简介</th>
        <th>文章分类id</th>
        <th>排序</th>
        <th>状态</th>
        <th>创建时间</th>
        <th>操作</th>
    </tr>
    <?php foreach ($article as $articles):?>
        <tr id="<?=$articles->id?>">
            <td><?=$articles->id?></td>
            <td><?=$articles->name?></td>
            <td><?=$articles->intro?></td>
            <td><?=$articles->article_category_id?></td>
            <td><?=$articles->sort?></td>
            <td><?=$articles->status==-1?'删除':''?>
                <?=$articles->status==0?'隐藏':''?>
                <?=$articles->status==1?'正常':''?>
            </td>
            <td><?=date("Y-m-d H:i:s",$articles['create_time'])?></td>
            <td>
                <?=\yii\bootstrap\Html::a('查看',['article/show','id'=>$articles->id],['class'=>'btn btn-warning'])?>
                <?=\yii\bootstrap\Html::a('修改',['article/edit','id'=>$articles->id],['class'=>'btn btn-warning'])?>
                <a id="delete" class="btn btn-warning">删除</a>
            </td>
        </tr>
    <?php endforeach;?>

</table>
<?=\yii\bootstrap\Html::a('添加',['article/add'],['class'=>'btn btn-warning'])?>
<?php
/*
 * @var $this \yii\web\View
 */
$url = \Yii\helpers\Url::to(['article/delete']);
$js=<<<JS
   $("#table").on('click','#delete',function(){
       var tr = $(this).closest('tr');
       if(confirm('删除!!!请再次确认')){
            $.get("$url",{id:tr.attr('id')},function(){
                tr.remove();
           });
       }

   });
JS;
$this->registerJs($js);
?>


