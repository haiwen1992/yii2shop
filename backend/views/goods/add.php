<?php
/**
 * @var $this Yii\web\View
 */
$form = \yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'name')->textInput();//名称
//echo $form->field($model,'sn')->textInput();//货号
//echo $form->field($mode,'content')->textarea();//商品详情描述
echo $form->field($mode,'content')->widget('kucha\ueditor\UEditor',[]);
echo $form->field($model,'logo')->hiddenInput();

//===================加载文件
$this->registerCssFile('@web/webuploader/webuploader.css');
$this->registerJsFile('@web/webuploader/webuploader.js',[
    //依赖关系
    'depends'=>\yii\web\JqueryAsset::className()
]);
echo
<<<HTML
<!--dom结构部分-->
<div id="uploader-demo">
    <!--用来存放item-->
    <div id="fileList" class="uploader-list"></div>
  
    <div><img id="img" src="$model->logo" alt=""></div>
     
    <div id="filePicker">选择图片</div>
</div>
<img id="img"/>
HTML;
$url = \yii\helpers\Url::to(['goods/uploader']);
$js =
    <<<js
// 初始化Web Uploader
var uploader = WebUploader.create({

    // 选完文件后，是否自动上传。
    auto: true,

    // swf文件路径
    swf: 'webuploader/Uploader.swf',

    // 文件接收服务端。
    server: '{$url}',

    // 选择文件的按钮。可选。
    // 内部根据当前运行是创建，可能是input元素，也可能是flash.
    pick: '#filePicker',

    // 只允许选择图片文件。
    accept: {
        title: 'Images',
        extensions: 'gif,jpg,jpeg,bmp,png',
        mimeTypes: 'image/gif,image/jpeg,image/png,image/jpg,image/bmp'
    }
});
// 文件上传成功，给item添加成功class, 用样式标记上传成功。
uploader.on( 'uploadSuccess', function( file,response) {
    // $( '#'+file.id ).addClass('upload-state-done');
    console.log(response);
    //回显图片
    $('#img').attr('src',response.url);
    //将上传成功图片地址写入logo字段
    $('#goods-logo').val(response.url);
});
js;
$this->registerJs($js);

//====================


echo $form->field($model,'goods_category_id')->hiddenInput();
//=====================无限极分类插件============
//加载插件需要的文件
$this->registerCssFile('@web/zTree/css/zTreeStyle/zTreeStyle.css');
$this->registerJsFile('@web/zTree/js/jquery.ztree.core.js',[
    'depends'=>\yii\web\JqueryAsset::className()
]);
$nodes= \backend\models\GoodsCategory::getNodes();
$js = <<<JS
var zTreeObj;
        // zTree 的参数配置，深入使用请参考 API 文档（setting 配置详解）
        var setting = {
            data: {
		simpleData: {
			enable: true,
			idKey: "id",
			pIdKey: "parent_id",
			rootPId: 0
		}
	},
	callback: {
		onClick: function(event,treeId,treeNode){
		    //节点被点击 获取节点的id 赋值$('#goodscategory-parent_id')
		    $("#goods-goods_category_id").val(treeNode.id)
		}
	}
        };
        // zTree 的数据属性，深入使用请参考 API 文档（zTreeNode 节点数据详解）
        var zNodes = {$nodes};
      
            zTreeObj = $.fn.zTree.init($("#treeDemo"), setting, zNodes);
            //展开所有节点
            zTreeObj.expandAll(true);
            //在修改时直接回显节点信息 默认选中
            var node = zTreeObj.getNodeByParam('id','$model->goods_category_id',null);
            zTreeObj.selectNode(node);
JS;
$this->registerJs($js);

echo
<<<HTML
<div>
    <ul id="treeDemo" class="ztree"></ul>
</div>
HTML;
//========================

echo $form->field($model,'brand_id')->dropDownList($a);//品牌分id
echo $form->field($model,'market_price')->textInput();//市场价格
echo $form->field($model,'shop_price')->textInput();//商品价格
echo $form->field($model,'stock')->textInput();//库存
echo $form->field($model,'is_on_sale',['inline'=>1])->radioList([0=>'下架',1=>'在售']);//是否售
echo $form->field($model,'status',['inline'=>1])->radioList([0=>'回收',1=>'正常']);//是否售
echo $form->field($model,'sort')->textInput();//排序
//echo $form->field($model,'create_time')->textInput();//添加时间
echo $form->field($model,'view_times')->textInput();//浏览次数
echo '<button type="submit" class="btn btn-primary">提交</button>';
\yii\bootstrap\ActiveForm::end();