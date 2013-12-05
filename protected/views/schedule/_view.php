<?php
/* @var $this ScheduleController */
/* @var $data Schedule */
?>

<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->id), array('view', 'id'=>$data->id)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('checkin')); ?>:</b>
	<?php echo CHtml::encode($data->checkin); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('checkout')); ?>:</b>
	<?php echo CHtml::encode($data->checkout); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('days')); ?>:</b>
	<?php echo CHtml::encode($data->days); ?>
	<br />


</div>
