<?php
/**
 * @var $this \yii\web\View
 */
$form = \yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'name')->textInput();//名称
echo $form->field($model,'parent_id')->hiddenInput();//上级分类id
//=====================插件============
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
		    $("#goodscategory-parent_id").val(treeNode.id)
		}
	}
        };
        // zTree 的数据属性，深入使用请参考 API 文档（zTreeNode 节点数据详解）
        var zNodes = {$nodes};
      
            zTreeObj = $.fn.zTree.init($("#treeDemo"), setting, zNodes);
            //展开所有节点
            zTreeObj.expandAll(true);
            //在修改时直接回显节点信息 默认选中
            var node = zTreeObj.getNodeByParam('id','$model->parent_id',null);
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
echo $form->field($model,'intro')->textInput();//简介
echo '<button type="submit" class="btn btn-primary">提交</button>';
\yii\bootstrap\ActiveForm::end();