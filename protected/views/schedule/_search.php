<?php
/* @var $this ScheduleController */
/* @var $model Schedule */
/* @var $form CActiveForm */
?>

<div class="wide form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>

	<div class="row">
		<?php echo $form->label($model,'id'); ?>
		<?php echo $form->textField($model,'id'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'checkin'); ?>
		<?php echo $form->textField($model,'checkin'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'checkout'); ?>
		<?php echo $form->textField($model,'checkout'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'days'); ?>
		<?php echo $form->textField($model,'days',array('size'=>60,'maxlength'=>255)); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Search'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->