<!--dom结构部分-->
<div id="uploader-demo">
    <a href="<?=\yii\helpers\Url::to(['goods/shopp'])?>" class="btn btn-warning" aria-hidden="true">列表页</a>
    <!--用来存放item-->
    <div id="fileList" class="uploader-list"></div>
    <div id="filePicker">选择图片</div>
</div>
<?php foreach ($model as $models):?>
<div style="background-color: ">
    <div id="<?=$models->id ?>" class="item" style="border: hidden">
        <br/>
        <img src="<?=$models->path ?>" width="40%">
        <span style="width: 2px"><a id="<?=$models->id ?>"  class="btn btn-info del">删除</a></span>
    </div>
</div>
<?php endforeach;?>

<?php

//===================加载文件
$this->registerCssFile('@web/webuploader/webuploader.css');
$this->registerJsFile('@web/webuploader/webuploader.js',[
    //依赖关系
    'depends'=>\yii\web\JqueryAsset::className()
]);

$url = \yii\helpers\Url::to(['goods/picture']);
$ur = \yii\helpers\Url::to(['goods/del']);
$js =
    <<<js
// 初始化Web Uploader
var uploader = WebUploader.create({

    // 选完文件后，是否自动上传。
    auto: true,

    // swf文件路径
    swf: 'webuploader/Uploader.swf',

    // 文件接收服务端。
    server: '$url?id=$goods_id',

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
    var html='<img src="'+response.url+'" width="40%">';
    $(".item").last().append(html);
     });
   $(".itme").on('click','.del',function(){
       var div = $(this).closest('div');
       var id =$(this).attr('id');
       $.get('$ur',{id:id},function() {
         div.remove();
      
   })
});
js;
$this->registerJs($js);

//====================
