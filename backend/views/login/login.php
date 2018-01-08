<?php
$form = \yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'usernme')->textInput();
echo $form->field($model,'usernme')->passwordInput();
echo \yii\bootstrap\Html::submitButton('登陆');
\yii\bootstrap\ActiveForm::end();