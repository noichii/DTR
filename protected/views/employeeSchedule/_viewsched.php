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
			if($e['mon'] == null && $e['tue'] == null && $e['wed'] == null && $e['thur'] == null && $e['fri'] == null && $e['sat'] == null && $e['sun'] == null){
			#if($e['status'] == 0){
				$e['mon']='ERROR IN SCHEDULE';
				$e['tue']='ERROR IN SCHEDULE';
				$e['wed']='ERROR IN SCHEDULE';
				$e['thur']='ERROR IN SCHEDULE';
				$e['wed']='ERROR IN SCHEDULE';
				$e['fri']='ERROR IN SCHEDULE';
				$e['sat']='ERROR IN SCHEDULE';
				$e['sun']='ERROR IN SCHEDULE';
			}

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
				'es_id' => $e['es_id'],				
				'conflict' => $e['conflict'],				
				);
		}
	?>

<br>
<table class="table table-hover">
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
$thisid = null;
$schedfill = 0;

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
						$thisid = $value['es_id'];
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
			$cur = $checksched;
			if($checksched == null){
				$cur = "no schedule";
			}else{
				$cur = $checksched;
			}
			if(strlen($cur) > 13){
				$cur = substr($cur, 13);
			}
			if($cur == 'ERROR IN SCHEDULE'){
				$currD .= "<td><input type='button' onclick='errsched(".$emp['id'].",".$thisid.")' value='".$cur."' /></td>"; //schedule
			}else{
				$currD .= "<td>".$cur."</td>"; //schedule
			}

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
$data = "";

		echo $empname = "<tr class='".$emp['department_id']."'><td>";
	##Modal
	 $this->widget(
		'bootstrap.widgets.TbButton',
			array(
				'label' => $emp['firstname'].", ".$emp['lastname'],
				'size' => 'small',
				'htmlOptions'=> array(
				'onclick'=>'getvalue('.$emp['id'].',\'\');'
			), ));
	##End Modal
echo	"</td>".$currD."</tr>";
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
																deptid = 0;
												}else{
																$('.<?php echo $deptF["id"]?>').hide();
												}
								}
				</script>
				<?php
				  }
					?>

<script>

function errsched(id,es_id){
	var r=confirm("Schedule are not created yet. Do you want to add the schedule?");
	if (r != true){
		getvalue(id,es_id);
	}else{
		window.location.href="../DTR/index.php?r=schedule/create";
  }
	
}

function getvalue(id,es_id){

				$('#myModal').modal();
				$('#myModal .saveBtn').click(function(){

							var sd = document.getElementById("start_date").value;
							var ed = document.getElementById("end_date").value;
							var st = document.getElementById("start_time-start_time-start_time-start_time").value;	
							var et = document.getElementById("end_time-end_time-end_time-end_time").value;

							var wm = document.getElementById("wmon").checked;
							var wt = document.getElementById("wtue").checked;
							var ww = document.getElementById("wwed").checked;
							var wth= document.getElementById("wthur").checked;
							var wf = document.getElementById("wfri").checked;
							var wsa= document.getElementById("wsat").checked;
							var wsu= document.getElementById("wsun").checked;

							var rm = document.getElementById("rmon").checked;
							var rt = document.getElementById("rtue").checked;
							var rw = document.getElementById("rwed").checked;
							var rth= document.getElementById("rthur").checked;
							var rf = document.getElementById("rfri").checked;
							var rsa= document.getElementById("rsat").checked;
							var rsu= document.getElementById("rsun").checked;

							var msg = "";
							countw = 0;
							countr = 0;
							var allw = '';
							var allr = '';

							if(sd == '' || sd == null){
									msg = msg+"*Start Date must be filled <br>";
							}
							if(ed == '' || ed == null){
									msg = msg + "*End Date must be filled <br>";
							}
							if(st == '' || st == null){
									msg = msg + "*Start Time must be filled <br>";
							}
							if(et == '' || et == null){
									msg = msg + "*End Time must be filled <br>";
							}
							if(wm == true){countw ++; allw = allw + 'mon,';}
							if(wt == true){countw ++; allw = allw + 'tue,';}
							if(ww == true){countw ++; allw = allw + 'wed,';}
							if(wth== true){countw ++; allw = allw + 'thur,';}
							if(wf == true){countw ++; allw = allw + 'fri,';}
							if(wsa== true){countw ++; allw = allw + 'sat,';}
							if(wsu== true){countw ++; allw = allw + 'sun,';}

							if(rm == true){countr ++; allr = allr + 'mon,';}
							if(rt == true){countr ++; allr = allr + 'tue,';}
							if(rw == true){countr ++; allr = allr + 'wed,';}
							if(rth== true){countr ++; allr = allr + 'thur,';}
							if(rf == true){countr ++; allr = allr + 'fri,';}
							if(rsa== true){countr ++; allr = allr + 'sat,';}
							if(rsu== true){countr ++; allr = allr + 'sun,';}
							
							if(countw != 5){
								msg = msg + "*Please Select 5 days of work<br>";
							}

							if(countr != 2){
								msg = msg + "*Please Select 2 days of Rest<br>";
							}

							if (msg != ""){
								 document.getElementById("err").innerHTML = msg; 
								 return false;
							}else{
								addvalue(id,sd,ed,st,et,allr,allw,es_id);
							}

				});
}

	function addvalue(id,sd,ed,st,et,allr,allw,es_id){
		$.ajax({
			type: "GET",
			url: "../DTR/index.php?r=employeeSchedule/addfield",
			data: "id=" + id + "&sd=" + sd + "&ed=" + ed + "&st=" + st + "&et=" + et + "&getStart=" + $('#getstartd').val() + "&getEnd=" + $('#getendd').val() + "&allr=" + allr + "&allw=" + allw + "&es_id=" + es_id,//Passing the values to the php page
			success: function (html) {
			//REload page
			thisD = "<?php echo $startDate?>";
			thisE = "<?php echo $endDate?>";
			window.location.href="../DTR/index.php?r=employeeSchedule/viewsched&startDate=" + thisD + "&endDate=" + thisE;
			},
			});
	}

</script>


<?php
$this->renderPartial('modal');
?>
