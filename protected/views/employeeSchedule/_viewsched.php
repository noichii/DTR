<?php
/* @var $this EmployeeScheduleController */
/* @var $model EmployeeSchedule */
/* @var $form CActiveForm */
?>

<div class="form">

<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm', array(
	'id'=>'employee-schedule-form',
	// Please note: When you enable ajax validation, make sure the corresponding
	// controller action is handling ajax validation correctly.
	// There is a call to performAjaxValidation() commented in generated controller code.
	// See class documentation of CActiveForm for details on this.
	'enableAjaxValidation'=>false,
)); ?>


	<p class="note">Fields with <span class="required">*</span> are required.</p>
	<?php echo $alert;?>

<br>
<?php
echo $form->datepickerRow($model,'start_date',array(
												'name'=>'startDate',
												'value'=>date("Y-m-d"),                                                                             
												'options' => array(                                                                             
																'language' => 'en',
																 'autoclose'=>true,
																'format'=>'yyyy-mm-dd',                                                                             
																),                                                                                              
												'prepend' => '<i class="icon-calendar"></i>'        ));                                            
																																																							       
echo $form->datepickerRow($model,'end_date',array(
												'name'=>'endDate',
												'value'=>date("Y-m-d"),                                                                             
												'options' => array(                                                                             
																'language' => 'en',                                                                           
																 'autoclose'=>true,
																'format'=>'yyyy-mm-dd',                                                                             
																),                                                                                              
												'prepend' => '<i class="icon-calendar"></i>'        ));                                            
																																																							       
?>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Get Schedule'); ?>
	</div>
<br>
	<?php
	if($emps_lists != null){
		$lists = array();
		foreach($emps_lists as $e){
			$lists[] = array(
				'id' => $e['id'],
				'lname' => $e['lastname'],
				'fname' => $e['firstname'],
				'start_date' => $e['start_date'],
				'end_date' => $e['end_date'],
				'mon' => $e['mon'],
				'tue' => $e['tue'],
				'wed' => $e['wed'],
				'thur' => $e['thur'],
				'fri' => $e['fri'],
				'sat' => $e['sat'],
				'sun' => $e['sun'],
				);
		}
	}
	?>

<?php 
	$script = array();
	foreach($department as $dep){
		$script[] = $dep;
	}

	$ctr = 0;
	$varname ="";
	foreach($script as $dept):
		$ctr++;
		$varname = "varname".$ctr;
		$varCheckname = "varname".$ctr."check";
		echo "<input type='checkbox' id='$varCheckname' onclick='$varname()'>";
		echo " ".$dept['name']."<br>";
	endforeach;
?>
<br>
<table border=1>
<?php
$check = '';
$currDate ='';
$currD ='';
$empname ='';
	if($startDate != '' || $endDate != ''){
		$chkin = $startDate;
		$chkout = $endDate;
			while(strtotime($chkin) <= strtotime($chkout)){
				$currDate .= "<td>".date('M-d',strtotime($chkin))."<br>".date('D',strtotime($chkin))."</td>";
				$chkin = date('Y-M-d', strtotime('+1 day', strtotime($chkin)));
			}
	echo "<tr><td>Name</td>".$currDate."</tr>";
	}

	foreach($employees as $emp):

	if($startDate != '' || $endDate != ''){
		$chkins = $startDate;
		$chkouts = $endDate;
			$checksched ='';
		while(strtotime($chkins) <= strtotime($chkouts)){

			foreach($lists as $key => $value):
				if($value['id'] == $emp['id']){
						if(strtotime($value['start_date']) <= strtotime($chkins) && strtotime($value['end_date']) >= strtotime($chkins)){
							$day = date('D',strtotime($chkins));	
							if($day == 'Mon'){
								$checksched .= $value['mon'];	
							}else if($day == "Tue"){
								$checksched .= $value['tue'];	
							}else if($day == "Wed"){
								$checksched .= $value['wed'];	
							}else if($day == "Thu"){
								$checksched .= $value['thur'];	
							}else if($day == "Fri"){
								$checksched .= $value['fri'];	
							}else if($day == "Sat"){
								$checksched .= $value['sat'];	
							}else if($day == "Sun"){
								$checksched .= $value['sun'];	
							}else{
								$checksched .= '';
							}
						}else{
						}
					}
			endforeach; //foreach lists
			$currD .= "<td>".$checksched."</td>";
			$checksched ='';

			$chkins = date('Y-m-d', strtotime('+1 day', strtotime($chkins)));
		}
	}
		echo $empname = "<tr class='".$emp['department_id']."'><td>".CHtml::link($emp['firstname'].", ".$emp['lastname'],array('employeeSchedule/empSched','id'=>$emp['id']))."</td>".$currD."</tr>";
		$currD = null;

	endforeach; //foreach employees
?>
		
</table>



	<div class="row">
		<?php echo $form->labelEx($model,''); ?>
		<?php echo $form->hiddenField($model,'emp_id'); ?>
		<?php echo $form->error($model,'emp_id'); ?>
	</div>


<?php $this->endWidget(); ?>

</div><!-- form -->


<?php
	$varname = '';
	$ctr ='';
	foreach($script as $deptF){
		$ctr ++;
		$varname = "varname".$ctr;
?>
	<script>
		$('.<?php echo $deptF['id']?>').hide();
	</script>
<?php
	}
?>
<?php
	$varname = '';
	$ctr ='';
	foreach($script as $deptF){
		$ctr ++;
		$varname = "varname".$ctr;
		$varCheckname = "varname".$ctr."check";
?>
	<script>
	function <?php echo $varname.'()'?>{
		if(document.getElementById('<?php echo $varCheckname?>').checked){
			$('.<?php echo $deptF["id"]?>').show();
			}else{
			$('.<?php echo $deptF["id"]?>').hide();
			}
	}
	</script>
<?php
	}
?>
