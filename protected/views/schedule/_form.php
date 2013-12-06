<?php
/* @var $this ScheduleController */
/* @var $model Schedule */
/* @var $form CActiveForm */
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'schedule-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'checkin'); ?>
    <?php 
      $this->widget(
    'bootstrap.widgets.TbTimePicker',
      array(
        'model' => $model,
        'attribute' => 'checkin',
        'options' => array(
            'showMeridian' => false
        )
      )
    );
    ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'checkout'); ?>
    <?php 
      $this->widget(
    'bootstrap.widgets.TbTimePicker',
      array(
        'model' => $model,
        'attribute' => 'checkout',
        'options' => array(
            'showMeridian' => false
        )
      )
    );
    ?>
	</div>

	<div class="row">
		  <?php echo $form->labelEx($model,'days'); ?></td>
		  <?php echo $form->checkBox($model,'days',array('name'=>'day[]','value'=>'M')) . ' Monday'; ?><br>
		  <?php echo $form->checkBox($model,'days',array('name'=>'day[]','value'=>'T')) . ' Tuesday'; ?><br>
		  <?php echo $form->checkBox($model,'days',array('name'=>'day[]','value'=>'W')) . ' Wednesday'; ?><br>
		  <?php echo $form->checkBox($model,'days',array('name'=>'day[]','value'=>'Th')) . ' Thursday'; ?><br>
		  <?php echo $form->checkBox($model,'days',array('name'=>'day[]','value'=>'F')) . ' Friday'; ?><br>
		  <?php echo $form->checkBox($model,'days',array('name'=>'day[]','value'=>'Sa')) . ' Saturday'; ?><br>
		  <?php echo $form->checkBox($model,'days',array('name'=>'day[]','value'=>'Su')) . ' Sunday'; ?><br>
	</div>

  <br>

	<div class="row">
		<?php #echo $form->labelEx($model,'days'); ?>
		<?php #echo $form->textField($model,'days',array('size'=>60,'maxlength'=>255)); ?>
		<?php #echo $form->error($model,'days'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->
