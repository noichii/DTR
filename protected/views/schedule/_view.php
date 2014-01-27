<?php
/* @var $this ScheduleController */
/* @var $data Schedule */
?>

<br / >
<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->id), array('view', 'id'=>$data->id)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('mon')); ?>:</b>
	<?php echo CHtml::encode($data->mon); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('tue')); ?>:</b>
	<?php echo CHtml::encode($data->tue); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('wed')); ?>:</b>
	<?php echo CHtml::encode($data->wed); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('thur')); ?>:</b>
	<?php echo CHtml::encode($data->thur); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('fri')); ?>:</b>
	<?php echo CHtml::encode($data->fri); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('sat')); ?>:</b>
	<?php echo CHtml::encode($data->sat); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('sun')); ?></b>
	<?php echo CHtml::encode($data->sun); ?>
	<br />


</div>
