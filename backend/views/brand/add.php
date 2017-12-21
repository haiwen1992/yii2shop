<?php
$form = \yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'name')->textInput();//名称
echo $form->field($model,'intro')->textarea();//简介
echo $form->field($model,'imgFile')->fileInput()->label('图片');
echo"<img width='200px' class='img-responsive img-circle' src='$model->logo'/>";
echo $form->field($model,'sort')->textInput();
echo $form->field($model,'status',['inline'=>1])->radioList([-1=>'删除',0=>'隐藏',1=>'正常']);
echo '<button type="submit" class="btn btn-primary">提交</button>';
\yii\bootstrap\ActiveForm::end();