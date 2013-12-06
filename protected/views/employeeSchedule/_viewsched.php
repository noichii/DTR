<?php
/* @var $this EmployeeScheduleController */
/* @var $model EmployeeSchedule */
/* @var $form CActiveForm */
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'employee-schedule-form',
	// Please note: When you enable ajax validation, make sure the corresponding
	// controller action is handling ajax validation correctly.
	// There is a call to performAjaxValidation() commented in generated controller code.
	// See class documentation of CActiveForm for details on this.
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

<br>
<?php
$this->widget('zii.widgets.jui.CJuiDatePicker', array(
    'name'=>'startDate',
			'options'=>array(
				'showAnim'=>'fold',
			),
				'htmlOptions'=>array(
					'style'=>'height:20px;'
				),
));
$this->widget('zii.widgets.jui.CJuiDatePicker', array(
    'name'=>'endDate',
			'options'=>array(
				'showAnim'=>'fold',
			),
				'htmlOptions'=>array(
					'style'=>'height:20px;'
				),
));
?>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Get Date'); ?>
	</div>
<br><br>

<table border=1>
<?php
$currDate ='';
$currD ='';
$empname ='';
	if($startDate != '' || $endDate != ''){
		$chkin = $startDate;
		$chkout = $endDate;
			while(strtotime($chkin) <= strtotime($chkout)){
				$currDate .= "<td>".date('M-d',strtotime($chkin))."</td>";
				$chkin = date('M-d', strtotime('+1 day', strtotime($chkin)));
				//CHECKING employee with schedule
			}
	}
	echo "<tr><td>Name</td>".$currDate."</tr>";
	foreach($emps_lists as $emp){
		if($startDate != '' || $endDate != ''){
						$chkins = $startDate;
						$chkouts = $endDate;
						while(strtotime($chkins) <= strtotime($chkouts)){
							if((strtotime($emp['start_date']) <= strtotime($chkins)) && (strtotime($emp['end_date']) >= strtotime($chkins))) {
								$check = date('h:i A',strtotime($emp['checkin']))."-".date('h:i A',strtotime($emp['checkout']));	
							}else{
								$check = "";
							}
									  $currD .= "<td>".$check."</td>";
										$chkins = date('Y-m-d', strtotime('+1 day', strtotime($chkins)));
										//CHECKING employee with schedule
						$check = null;
						}
	}
		echo $empname = "<tr><td>".$emp['firstname'].", ".$emp['lastname']."</td>".$currD."</tr>";
		$currD = null;
	}
		#echo $empname;
?>
		
</table>

	<div class="row">
		<?php echo $form->labelEx($model,''); ?>
		<?php echo $form->hiddenField($model,'week_id'); ?>
		<?php echo $form->error($model,'week_id'); ?>
	</div>


<?php $this->endWidget(); ?>

</div><!-- form -->
