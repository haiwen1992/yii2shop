<table id="table" class="table table-bordered">
    <h1>品牌列表</h1>
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
        <tr id="<?=$brands->id?>">
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
                <a id="delete" class="btn btn-warning">删除</a>
            </td>
        </tr>
    <?php endforeach;?>

</table>
<?=\yii\bootstrap\Html::a('添加',['brand/add'],['class'=>'btn btn-warning'])?>
<?php
/*
 * @var $this \yii\web\View
 */
$url = \Yii\helpers\Url::to(['brand/delete']);
$js=<<<JS
   $("#table").on('click','#delete',function(){
       // alert('11111');
       var tr = $(this).closest('tr');
       if(confirm('请再次确认')){
            $.get("$url",{id:tr.attr('id')},function(){   
                tr.remove();
           });
       }
       
   });
JS;
$this->registerJs($js);
?>





