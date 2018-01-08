<form class="form-inline".>
    <div class="form-group">
        <label class="sr-only" for="exampleInputEmail3">商品名:</label>
        <input type="text" name="name" class="form-control"  placeholder="商品名称">
    </div>
    <div class="form-group">
        <label class="sr-only" for="exampleInputPassword3">货号:</label>
        <input type="text" name="sn" class="form-control"  placeholder="货号">
    </div>
    <button type="submit" class="btn btn-default">搜索</button>
</form>
<table id="table" class="table ">

    <tr>
        <th>id</th>
        <th>商品名称</th>
        <th>货号</th>
        <th>LOGO图片</th>
        <th>商品分类id</th>
        <th>品牌分类</th>
        <th>市场价格</th>
        <th>商品价格</th>
        <th>库存</th>
        <th>是否在售</th>
        <th>状态</th>
        <th>排序</th>
        <th>添加时间</th>
        <th>浏览次数</th>
        <th>操作</th>
    </tr>

    <?php foreach ($good as $goods):?>
        <tr>
            <td><?=$goods->id?></td>
            <td><?=$goods->name?></td>
            <td><?=$goods->sn?></td>
            <td><img src="<?=$goods->logo?>" width="100px"  /></td>
            <td><?=$goods->goods_category_id?></td>
            <td><?=$goods->brand_id?></td>
            <td><?=$goods->market_price?></td>
            <td><?=$goods->shop_price?></td>
            <td><?=$goods->stock?></td>
            <td>
                <?=$goods->is_on_sale==1?'在售':''?>
                <?=$goods->is_on_sale==0?'下架':''?>
            </td>
            <td>
                <?=$goods->status==1?'正常':''?>
                <?=$goods->status==0?'回收站':''?>
             </td>
            <td><?=$goods->sort?></td>
            <td><?=date("Y-m-d H:i:s",$goods['create_time'])?></td>
            <td><?=$goods->view_times?></td>

            <td>
     <?=\yii\bootstrap\Html::a('描述',['goods/show','id'=>$goods->id],['class'=>'btn btn-warning'])?>
     <?=\yii\bootstrap\Html::a('修改',['goods/edit','id'=>$goods->id],['class'=>'btn btn-warning'])?>
     <?=\yii\bootstrap\Html::a('删除',['goods/delete','id'=>$goods->id],['class'=>'btn btn-warning'])?>
     <?=\yii\bootstrap\Html::a('相册',['goods/shopp','id'=>$goods->id],['class'=>'btn btn-warning'])?>
<!--                <a id="delete" class="btn btn-warning">删除</a>-->
            </td>
        </tr>
    <?php endforeach;?>
    <?=\yii\bootstrap\Html::a('添加',['goods/add'],['class'=>'btn btn-warning'])?>
    <div>
        <div></div>

    </div>
</table>

<?=\yii\widgets\LinkPager::widget([
        'pagination'=>$pager
])?>
