<?php
$form = \yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'password_hash')->passwordInput();//旧密码
echo $form->field($model,'password_h')->passwordInput();//新密码
echo $form->field($model,'password_ha')->passwordInput();//确认新密码
echo '<button type="submit" class="btn btn-primary">提交</button>';
\yii\bootstrap\ActiveForm::end();