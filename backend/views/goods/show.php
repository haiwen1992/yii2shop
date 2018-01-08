<table id="table" class="table table-bordered">
    <tr>
        <th>商品id</th>
        <th>详描述情</th>
    </tr>
        <tr>
            <td><?=$model->goods_id?></td>
            <td><?=$model->content?></td>
        </tr>
    <?=\yii\bootstrap\Html::a('返回',['article/index'],['class'=>'btn btn-warning'])?>
</table>
