<?php
/**
 * @var $this Yii\web\View
 */
$form = \yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'name')->textInput();//名称
echo $form->field($model,'intro')->textarea();//简介
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
$url = \yii\helpers\Url::to(['brand/upload']);
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
        mimeTypes: 'image/*'
    }
});
// 文件上传成功，给item添加成功class, 用样式标记上传成功。
uploader.on( 'uploadSuccess', function( file,response) {
    // $( '#'+file.id ).addClass('upload-state-done');
    console.log(response);
    //回显图片
    $('#img').attr('src',response.url);
    //将上传成功图片地址写入logo字段
    $('#brand-logo').val(response.url);
});
js;
$this->registerJs($js);

//====================





//echo $form->field($model,'imgFile')->fileInput()->label('图片');
//echo"<img width='200px' class='img-responsive img-circle' src='$model->logo'/>";
echo $form->field($model,'sort')->textInput();
echo $form->field($model,'status',['inline'=>1])->radioList([0=>'隐藏',1=>'正常']);
echo '<button type="submit" class="btn btn-primary">提交</button>';
\yii\bootstrap\ActiveForm::end();