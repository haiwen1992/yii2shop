<?php
/**
 * @var $this Yii\web\View
 */
$form = \yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'username')->textInput();//名称
echo $form->field($model,'password_hash')->passwordInput();//简介
echo $form->field($model,'email')->textInput();
//echo $form->field($model,'last_login_time')->textInput();
//echo $form->field($model,'last_login_ip')->textInput();
echo $form->field($model,'role')->checkboxList($ar);
echo '<button type="submit" class="btn btn-primary">提交</button>';
\yii\bootstrap\ActiveForm::end();