<table id="table" class="table table-bordered">
    <tr>
        <th>文章id</th>
        <th>详情</th>
    </tr>
        <tr>
            <td><?=$model->article_id?></td>
            <td><?=$model->content?></td>
        </tr>
    <?=\yii\bootstrap\Html::a('返回',['article/index'],['class'=>'btn btn-warning'])?>
</table>
