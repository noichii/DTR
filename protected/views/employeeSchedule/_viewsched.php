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


	<?php echo $alert;?>

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
<br>
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
				echo "<input type='checkbox' id='$varCheckname' onclick='$varname()' checked>";
				echo " ".$dept['name']."<br>";
				endforeach;
				?>

<br>
		<?php echo CHtml::submitButton('Get Schedule',array('class' => 'btn btn-primary')); ?>
<br>
	<?php
	if($emps_lists != null){ 	#starttable
		$lists = array();
		foreach($emps_lists as $e){
			$lists[] = array(
				'id' => $e['id'],
				'lname' => $e['lastname'],
				'fname' => $e['firstname'],
				'start_date' => $e['start_date'],
				'end_date' => $e['end_date'],
				'mon' => ($e['mon'] == null ? $varday='RD' : $varday=$e['mon']),
				'tue' => ($e['tue'] == null ? $varday='RD' : $varday=$e['tue']),
				'wed' => ($e['wed'] == null ? $varday='RD' : $varday=$e['wed']),
				'thur' => ($e['thur'] == null ? $varday='RD' : $varday=$e['thur']),
				'fri' => ($e['fri'] == null ? $varday='RD' : $varday=$e['fri']),
				'sat' => ($e['sat'] == null ? $varday='RD' : $varday=$e['sat']),
				'sun' => ($e['sun'] == null ? $varday='RD' : $varday=$e['sun']),
				);
		}
#		echo "<pre>";
#		print_r($lists);
#		echo "</pre>";
	?>

<br>
<table border=1>
<?php
$checkdate = null;
$late = null;
$ut = null;
$check = '';
$currDate ='';
$currD ='';
$empname ='';
$io = null;
$totalLates = 0;
$totalUnder = 0;

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

			foreach($checkinout as $inouts):
				if($inouts['user_id'] == $emp['id']){
					if(strtotime($chkins) == strtotime($inouts['date'])){
						$checkdate = date('H:i',strtotime($inouts['checkin'])).' - '.date('H:i',strtotime($inouts['checkout']));
					}
				}
			endforeach;//foreach inout
			$currD .= "<td>".$checksched."</td>"; //schedule
			
			$io .= "<td>".$checkdate."</td>"; //checkin

			#CHECK IF LATE
			$checkifLate = (strtotime(substr($checkdate, 0, 5)) - strtotime(substr($checksched, 0, 5))) / 60;
			if($checksched != null && $checkdate != null){
				if(strtotime($checkifLate) <= 0 && $checksched != 'RD'){
					$late .= "<td>".$checkifLate."</td>";
					$totalLates = $totalLates + $checkifLate;
				}else{
					$late .= "<td></td>";
				}
			}else{
				$late .= "<td></td>";
			}

			#CHECK IF UNDERTIME
			$checkifUnder = (strtotime(substr($checksched, 8)) - strtotime(substr($checkdate, 8))) / 60;
			if($checksched != null && $checkdate != null){
				if(strtotime($checkifUnder) <= 0 && $checksched != 'RD'){
					$ut .= "<td>".$checkifUnder."</td>";
					$totalUnder = $totalUnder + $checkifUnder;
				}else{
					$ut .= "<td></td>";
				}
			}else{
				$ut .= "<td></td>";
			}

			$chkins = date('Y-m-d', strtotime('+1 day', strtotime($chkins)));
			$checksched ='';
		}
	}
		echo $empname = "<tr class='".$emp['department_id']."'><td>".CHtml::link($emp['firstname'].", ".$emp['lastname'],array('employeeSchedule/empSched','id'=>$emp['id']))."</td>".$currD."</tr>";
		$currD = null;
		$io = null;
		$late = null;
		$ut = null;
		$totalLates = null;
		$totalUnder = null;
		$checksched = null;
		$checkdate = null;

	endforeach; //foreach employees
	} #endtable
	else{
		echo "No schedule yet.";
	}
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
								$('.<?php echo $deptF['id']?>').show();
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
