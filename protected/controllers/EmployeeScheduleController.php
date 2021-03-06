<?php

class EmployeeScheduleController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/column2';

	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
			'postOnly + delete', // we only allow deletion via POST request
		);
	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array('index','view'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('create','update','viewsched','empsched','manpower','report','upload','addfield','mod'),
				'users'=>array('@'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('admin','delete','viewsched','testview'),
				'users'=>array('admin'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
		$this->render('view',array(
			'model'=>$this->loadModel($id),
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate(){

		$model=new EmployeeSchedule;
		$checkifExist = EmployeeSchedule::model()->findAll();
		
		$emp_s = array();
		foreach($checkifExist as $w){
			$emp_s[] = array(
				'emp_id' => $w['emp_id'],
				'start_date' => $w['start_date'],
				'end_date' => $w['end_date'],
				);
		}
		
		$alertwS = null;
		$alertwoS = null;
		$empschedId = null;
		$employee = Employee::model()->findAll();
    $emp = array();
		    foreach($employee as $emps){
					array_push($emp, $emps['id'].". ".$emps['lastname']." ".$emps['firstname']);
				}
		
		$confsched = null;
		$alert = '';
		$alertTo = null;
		$dayswithsched = null;
		$dayswithoutsched = null;

		if(isset($_POST['EmployeeSchedule']))
		{

		$employeeselect = $_POST['emp_sel'];
		$scheduleselect = $_POST['sched_sel'];
		$startD = $_POST['startDate'];
		$endD = $_POST['endDate'];
		
		if($employeeselect == null){$alertTo .= '* Employee/s Must not be Empty<br>';}
		if($scheduleselect == null){$alertTo .= '* Schedule Must not be Empty<br>';}
		if($startD == null){$alertTo .= '* Start Date Must not be Empty<br>';}
		if($endD == null){$alertTo .= '* End Date Must not be Empty<br>';}

		if($alertTo != null){
				$alert = '<div class="alert-error" style="padding:10px;">ERROR<br />'.$alertTo.'</div>';
		}else{


		$arr_sched = explode('.',$scheduleselect);
		$saveit = 0;
		$array = $employeeselect;
		$notExistName = '';

		$output = array();
		$i = 0;

		$bits = explode(',', $array);
		foreach($bits as $bit):
			if(strpos($bit,'. ') != false){
						$has_num = explode('.', $bit);
						if(count($has_num) > 1) {
										$i = $has_num[0];
										$name = $has_num[1];
						} else {
										$name = $has_num[0];
						}
						$output[$i][] = trim($name);
			} else{
						$saveit++; //checkif exist
						$notExistName .= $bit.",";
			}
		endforeach;

		if($saveit == 0){
			foreach($output as $key => $value):
				$checkOutput = Yii::app()->db->createCommand(/*'
						SELECT es.sched_id, es.emp_id, es.start_date, es.end_date, s.mon, s.tue, s.wed, s.thur, s.fri, s.sat, s.sun
						FROM employee_schedule AS es
						INNER JOIN schedule AS s ON s.id = es.sched_id
						WHERE emp_id=\''.$key.'\' AND (start_date <= \''.$startD.'\' and end_date >= \''.$endD.'\')
						'*/
						'SELECT es.sched_id, es.emp_id, es.start_date, es.end_date, s.mon, s.tue, s.wed, s.thur, s.fri, s.sat, s.sun 
						FROM employee_schedule AS es 
						INNER JOIN schedule AS s ON s.id = es.sched_id 
						WHERE emp_id=\''.$key.'\' AND (start_date <= \''.$startD.'\' and end_date >= \''.$startD.'\' or end_date <= \''.$endD.'\')
						')->queryAll();

						$day = $startD;
						while($day <= $endD){
							foreach($checkOutput as $cA){
								if($cA['start_date'] <= $day && $cA['end_date'] >= $day){
									//days with schedule
									$dayswithsched .= $day."<br>";
								}else{
									//days without schedule
									$dayswithoutsched .= $day."<br>";
								}
							}
								$day = date('Y-m-d', strtotime('+1 day', strtotime($day)));
						}
								if($dayswithsched == null){
									//the employee schedule has been save	
									$model = new EmployeeSchedule;
									$model->emp_id = $key;
									$model->sched_id = $arr_sched[0];
									$model->start_date = $startD;
									$model->end_date = $endD;
									$model->save();

									$emp_key = Employee::model()->findByPk($key);
									$alertwS .= $emp_key['lastname'].', '.$emp_key['firstname'].' has been saved <br>';
								}else{
									//cant be save
									$emp_key = Employee::model()->findByPk($key);
									$alertwoS .= $emp_key['lastname'].', '.$emp_key['firstname'].'  can\'t be saved <br> Conflict Schedule:<br>'.$dayswithsched;
								}
								$confsched .= $dayswithsched;
								$dayswithsched = null;
								$dayswithoutsched = null;

			endforeach;
			if($alertwS != null){
				$alert .= "<div class='alert-success' style='padding:10px'>SUCCESS!<br />$alertwS</div>";}
			if($alertwoS != null){
				$alert .= "<div class='alert-error' style='padding:10px;'>ERROR<br />$alertwoS</div>";}
			}else{
				$alert = '<div class="alert-error" style="padding:10px;">ERROR<br />'.$notExistName.' does not Exist. Please try again.</div>';
			}
		}
}
				$this->render('create',array(
				'employee'=>$employee,
				'model'=>$model,
				'alert'=>$alert,
				'emp'=>$emp,
				));
	}

	public function actionviewsched($startDate,$endDate)
	{
		$model=new EmployeeSchedule;
		$alert = '';
		$alertTo = null;

		$employees = Yii::app()->db->createCommand('
			SELECT e.id, e.firstname, e.lastname, e.middle_initial, e.position_id, e.department_id, dept.name
			FROM employee AS e
			LEFT JOIN department AS dept ON e.department_id = dept.id
			'
			)->queryAll();

		$department = Department::model()->findAll();	//Department
		
		$checkinout = null;
		$emps_lists = null;
		if(isset($_POST['EmployeeSchedule']))
		{
			$startDate = $_POST['startDate'];
			$endDate = $_POST['endDate'];
		}else{
			$startDate = $_GET['startDate'];
			$endDate = $_GET['endDate'];
		}

		if($startDate !=null && $endDate != null){
			if($startDate == null){ $alertTo .= '*Start Date must not be empty.<br />';}
			if($endDate == null){ $alertTo .= '*End Date must not be empty.<br />';}

			if($alertTo != null){
				$alert = '<div class="alert-error" style="padding:10px;">ERROR<br />'.$alertTo.'</div>';
			}else{

			if(strtotime($startDate) > strtotime($endDate)){
				$alert = '<div class="alert-error" style="padding:10px;">ERROR<br />Invalid Date</div>';
			}else{
						$checkinout = Yii::app()->db->createCommand('
							SELECT * FROM `checkinout` where (date >= "'.$startDate.'" && date <= "'.$endDate.'")'
							)->queryAll();  //Checkin.out

						$emps_lists=Yii::app()->db->createCommand('
														SELECT e.id, e.firstname, e.lastname, es.emp_id, es.start_date, es.end_date, s.mon, s.tue, s.wed, s.thur, s.fri, s.sat, s.sun, es.status, es.conflict, es.sched_id, es.id as "es_id"
														FROM employee AS e 
														LEFT JOIN  employee_schedule AS es  ON e.id = es.emp_id
														LEFT JOIN schedule AS s ON es.sched_id = s.id 
														WHERE (start_date <= \''.$startDate.'\''.' or start_date <= \''.$endDate.'\')'
														)->queryAll();

			}
			}
		}
		$this->render('viewsched',array(
			'model'=>$model,
			'emps_lists'=>$emps_lists,
			'startDate' => $startDate,
			'endDate' => $endDate,
			'employees' => $employees,
			'alert'=>$alert,
			'department'=>$department,
			'checkinout'=>$checkinout,
		));
	}

	public function actionempsched($id)
	{
		$model=new EmployeeSchedule;
		$alert = '';
		$alertTo = null;

		$employees = Yii::app()->db->createCommand('
			SELECT e.id, e.firstname, e.lastname, e.middle_initial, e.position_id, e.department_id, dept.name
			FROM employee AS e
			LEFT JOIN department AS dept ON e.department_id = dept.id
			WHERE e.id = '.$id
			)->queryAll();

		$department = Department::model()->findAll();	//Department

		$checkinout = null;
		$startDate = null;
		$endDate = null;
		$emps_lists = null;
		if(isset($_POST['EmployeeSchedule']))
		{
			$startDate = $_POST['startDate'];
			$endDate = $_POST['endDate'];
			if($startDate == null){ $alertTo .= '*Start Date must not be empty.<br />';}
			if($endDate == null){ $alertTo .= '*End Date must not be empty.<br />';}

			if($alertTo != null){
				$alert = '<div class="alert-error" style="padding:10px;">ERROR<br />'.$alertTo.'</div>';
			}else{

			if(strtotime($startDate) > strtotime($endDate)){
				$alert = '<div class="alert-error" style="padding:10px;">ERROR<br />Invalid Date</div>';
			}else{
						$checkinout = Yii::app()->db->createCommand('
							SELECT * FROM `checkinout` where (date >= "'.$startDate.'" && date <= "'.$endDate.'") && user_id = '.$id
							)->queryAll();  //Checkin.out

						$emps_lists=Yii::app()->db->createCommand('
														SELECT e.id, e.firstname, e.lastname, es.emp_id, es.start_date, es.end_date, s.mon, s.tue, s.wed, s.thur, s.fri, s.sat, s.sun 
														FROM employee AS e 
														LEFT JOIN  employee_schedule AS es  ON e.id = es.emp_id
														LEFT JOIN schedule AS s ON es.sched_id = s.id 
														WHERE e.id ='.$id.' and (start_date <= \''.$startDate.'\''.' or start_date <= \''.$endDate.'\')'
														)->queryAll();

			}
		}
		}else{
			
		}
		//OT
		$checkot = Yii::app()->dbol->createCommand("
					SELECT * FROM `otform` WHERE employee_id = ".$id." and (date <= '$startDate' or date <= '$endDate') and status = 3
					")->queryAll();

		//LEAVE
		$leave = Yii::app()->dbol->createCommand("
					SELECT l.id, l.employee_id, lt.name, l.leave_type_id, l.reason, l.start_date, l.end_date, l.date_filed, l.sv1, l.sv2, l.om, l.hrm, l.remarks, l.create_date, l.days_with_pay, l.days_without_pay, l.others, l.status
					FROM `leave` as l
					LEFT JOIN  leave_type AS lt  ON lt.id = l.leave_type_id
					WHERE employee_id = ".$id." and (start_date <= '$startDate' or start_date <= '$endDate')
					")->queryAll();

		$this->render('empsched',array(
			'model'=>$model,
			'emps_lists'=>$emps_lists,
			'startDate' => $startDate,
			'endDate' => $endDate,
			'employees' => $employees,
			'alert'=>$alert,
			'department'=>$department,
			'checkinout'=>$checkinout,
			'checkot' => $checkot,
			'leave' => $leave,
		));
	}

	public function actionmanpower()
	{
		$model=new EmployeeSchedule;
		$alert = '';
		$alertTo = null;

		$employees = Yii::app()->db->createCommand('
			SELECT e.id, e.firstname, e.lastname, e.middle_initial, e.position_id, e.department_id, dept.name
			FROM employee AS e
			LEFT JOIN department AS dept ON e.department_id = dept.id
			'
			)->queryAll();

		$department = Department::model()->findAll();	//Department
		

		$checkinout = null;
		$startDate = null;
		$endDate = null;
		$emps_lists = null;
		if(isset($_POST['EmployeeSchedule']))
		{
			$startDate = $_POST['startDate'];
			$endDate = $_POST['endDate'];
			if($startDate == null){ $alertTo .= '*Start Date must not be empty.<br />';}
			if($endDate == null){ $alertTo .= '*End Date must not be empty.<br />';}

			if($alertTo != null){
				$alert = '<div class="alert-error" style="padding:10px;">ERROR<br />'.$alertTo.'</div>';
			}else{

			if(strtotime($startDate) > strtotime($endDate)){
				$alert = '<div class="alert-error" style="padding:10px;">ERROR<br />Invalid Date</div>';
			}else{
						$checkinout = Yii::app()->db->createCommand('
							SELECT * FROM `checkinout` where (date >= "'.$startDate.'" && date <= "'.$endDate.'")'
							)->queryAll();  //Checkin.out

						$emps_lists=Yii::app()->db->createCommand('
														SELECT e.id, e.firstname, e.lastname, es.emp_id, es.start_date, es.end_date, s.mon, s.tue, s.wed, s.thur, s.fri, s.sat, s.sun 
														FROM employee AS e 
														LEFT JOIN  employee_schedule AS es  ON e.id = es.emp_id
														LEFT JOIN schedule AS s ON es.sched_id = s.id 
														WHERE (start_date <= \''.$startDate.'\''.' or start_date <= \''.$endDate.'\')'
														)->queryAll();

			}
		}
		}else{
			
		}

		//OT
		$checkot = Yii::app()->dbol->createCommand("
					SELECT * FROM `otform` WHERE (date <= '$startDate' or date <= '$endDate')
					")->queryAll();

		//LEAVE
		$leave = Yii::app()->dbol->createCommand("
					SELECT l.id, l.employee_id, lt.name, l.leave_type_id, l.reason, l.start_date, l.end_date, l.date_filed, l.sv1, l.sv2, l.om, l.hrm, l.remarks, l.create_date, l.days_with_pay, l.days_without_pay, l.others, l.status
					FROM `leave` as l
					LEFT JOIN  leave_type AS lt  ON lt.id = l.leave_type_id
					WHERE (start_date <= '$startDate' or start_date <= '$endDate')
					")->queryAll();

		$this->render('manpower',array(
			'model'=>$model,
			'emps_lists'=>$emps_lists,
			'startDate' => $startDate,
			'endDate' => $endDate,
			'employees' => $employees,
			'alert'=>$alert,
			'department'=>$department,
			'checkinout'=>$checkinout,
			'checkot' => $checkot,
			'leave' => $leave,
		));
	}

	public function actionreport()
	{
		$model=new EmployeeSchedule;
		$alert = '';
		$alertTo = null;

		$employees = Yii::app()->db->createCommand('
			SELECT e.id, e.firstname, e.lastname, e.middle_initial, e.position_id, e.department_id, dept.name
			FROM employee AS e
			LEFT JOIN department AS dept ON e.department_id = dept.id
			'
			)->queryAll();

		$department = Department::model()->findAll();	//Department
		

		$checkinout = null;
		$startDate = null;
		$endDate = null;
		$emps_lists = null;
		if(isset($_POST['EmployeeSchedule']))
		{
			$startDate = $_POST['startDate'];
			$endDate = $_POST['endDate'];
			if($startDate == null){ $alertTo .= '*Start Date must not be empty.<br />';}
			if($endDate == null){ $alertTo .= '*End Date must not be empty.<br />';}

			if($alertTo != null){
				$alert = '<div class="alert-error" style="padding:10px;">ERROR<br />'.$alertTo.'</div>';
			}else{

			if(strtotime($startDate) > strtotime($endDate)){
				$alert = '<div class="alert-error" style="padding:10px;">ERROR<br />Invalid Date</div>';
			}else{
						$checkinout = Yii::app()->db->createCommand('
							SELECT * FROM `checkinout` where (date >= "'.$startDate.'" && date <= "'.$endDate.'")'
							)->queryAll();  //Checkin.out

						$emps_lists=Yii::app()->db->createCommand('
														SELECT e.id, e.firstname, e.lastname, es.emp_id, es.start_date, es.end_date, s.mon, s.tue, s.wed, s.thur, s.fri, s.sat, s.sun 
														FROM employee AS e 
														LEFT JOIN  employee_schedule AS es  ON e.id = es.emp_id
														LEFT JOIN schedule AS s ON es.sched_id = s.id 
														WHERE (start_date <= \''.$startDate.'\''.' or start_date <= \''.$endDate.'\')'
														)->queryAll();

			}
		}
		}else{
			
		}

		//OT
		$checkot = Yii::app()->dbol->createCommand("
					SELECT * FROM `otform` WHERE (date <= '$startDate' or date <= '$endDate')
					")->queryAll();

		//LEAVE
		$leave = Yii::app()->dbol->createCommand("
					SELECT l.id, l.employee_id, lt.name, l.leave_type_id, l.reason, l.start_date, l.end_date, l.date_filed, l.sv1, l.sv2, l.om, l.hrm, l.remarks, l.create_date, l.days_with_pay, l.days_without_pay, l.others, l.status
					FROM `leave` as l
					LEFT JOIN  leave_type AS lt  ON lt.id = l.leave_type_id
					WHERE (start_date <= '$startDate' or start_date <= '$endDate')
					")->queryAll();

		$this->render('report',array(
			'model'=>$model,
			'emps_lists'=>$emps_lists,
			'startDate' => $startDate,
			'endDate' => $endDate,
			'employees' => $employees,
			'alert'=>$alert,
			'department'=>$department,
			'checkinout'=>$checkinout,
			'checkot' => $checkot,
			'leave' => $leave,
		));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$model=$this->loadModel($id);

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['EmployeeSchedule']))
		{
			$model->attributes=$_POST['EmployeeSchedule'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
		}

		$this->render('update',array(
			'model'=>$model,
		));
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		$this->loadModel($id)->delete();

		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
		if(!isset($_GET['ajax']))
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$dataProvider=new CActiveDataProvider('EmployeeSchedule');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new EmployeeSchedule('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['EmployeeSchedule']))
			$model->attributes=$_GET['EmployeeSchedule'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return EmployeeSchedule the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=EmployeeSchedule::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param EmployeeSchedule $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='employee-schedule-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
	public function actionTestview()
	{
					$model=new EmployeeSchedule;

					// uncomment the following code to enable ajax-based validation
					/*
						 if(isset($_POST['ajax']) && $_POST['ajax']==='employee-schedule-testview-form')
						 {
						 echo CActiveForm::validate($model);
						 Yii::app()->end();
						 }
					 */

					if(isset($_POST['EmployeeSchedule']))
					{
									$model->attributes=$_POST['EmployeeSchedule'];
									if($model->validate())
									{
													// form inputs are valid, do something here
													return;
									}
					}
					$this->render('testview',array('model'=>$model));
	}

	public function actionUpload()
	{
				$model=new EmployeeSchedule;

		$alert = null;
		$symbol = null;
		$empArr = array();
		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		/*UPLOAD*/
		$employees = Checkinout::model()->findAll();
		#echo "<pre>";
		#print_r($employees);
		#echo "</pre>";
		foreach($employees as $emp):
		$empArr[] = array(
			'id'=>$emp['id'],
			'user_id'=>$emp['user_id'],
			'name'=>$emp['name'],
			'date'=>$emp['date'],
			'checkin'=>$emp['checkin'],
			'checkout'=>$emp['checkout'],
		);
		endforeach;
		$storagename = null;
		if ( isset($_POST["submit"]) ) {

						if ( isset($_FILES["file"])) {

										//if there was an error uploading the file
										if ($_FILES["file"]["error"] > 0) {
														$alert= 'ERROR<br />Invalid file.Please check the file and try again.';
														$symbol = 'error';

										}
										else {
														//Print file details
														$alert = "<div class='alert-success' style='padding:10px'>SUCCESS!<br />
														Upload: ". $_FILES["file"]["name"] . "<br />".
														"Type: " . $_FILES["file"]["type"] . "<br />".
														"Size: " . ($_FILES["file"]["size"] / 1024) . " Kb<br />".
														"Temp file: " . $_FILES["file"]["tmp_name"] . "<br />".
														"</div>";
														$symbol = 'succ';

														//if file already exists
													/*	if (file_exists("upload/" . $_FILES["file"]["name"])) {
																		#echo $_FILES["file"]["name"] . " already exists. ";
														}
														else {
																		//Store file in directory "upload" with the name of "uploaded_file.txt"
																		#$storagename = $_FILES["file"]["name"];
																		$storagename = "uploadedfile.csv";
																		move_uploaded_file($_FILES["file"]["tmp_name"], "upload/" . $storagename);

																		echo "Stored in: " . "upload/" . $_FILES["file"]["name"] . "<br />";
														}*/
										}
										$storagename = $_FILES["file"]["tmp_name"];
						} else {
											$alert= 'ERROR<br />No file selected.';
											$symbol = 'error';
						}
		}
		$csv = array();
		if($storagename != null){
			if ( $file = fopen( $storagename , 'r' ) ) {

					$result = fgetcsv($file);
					while (($result != false)) {
					$result = fgetcsv($file);
				  if (array(null) != $result) { // ignore blank lines
				      $csv[] = $result;
				   }
					}
					echo "<pre>";
				print_r($csv);
					echo "</pre>";
			}
		}
		/*UPLOAD*/

		$this->render('upload',array(
														'model'=>$model,
														'alert'=>$alert,
														'symbol'=>$symbol,
														));
	}

	public function actionAddfield($id,$sd,$ed,$st,$et,$allr,$allw,$es_id)
	{
				$model=new EmployeeSchedule;

				// uncomment the following code to enable ajax-based validation
				/*
					 if(isset($_POST['ajax']) && $_POST['ajax']==='employee-schedule-addfield-form')
					 {
					 echo CActiveForm::validate($model);
					 Yii::app()->end();
					 }

				if(isset($_POST['EmployeeSchedule']))
				{
								$model->attributes=$_POST['EmployeeSchedule'];
								if($model->validate())
								{
												// form inputs are valid, do something here
												return;
								}
				}
				 */
				echo $id = $_GET['id'];
				echo "<br>";
				echo $sd = $_GET['sd'];
				echo "<br>";
				echo $ed = $_GET['ed'];
				echo "<br>";
				echo $st = date('H:i',strtotime($_GET['st']));
				echo "<br>";
				echo $et = date('H:i',strtotime($_GET['et']));
				echo "<br>";
				echo $allr = $_GET['allr'];
				echo "<br>";
				echo $allw = $_GET['allw'];
				echo "<br><br><br>";
				echo $es_id = $_GET['es_id'];
				echo "<br>";

				$sched_allw = '';
				$sched_rd = '';
				$arrallw = explode(",", $allw);
				$arrallw = array_slice($arrallw, 0, count($arrallw)-1);
				$arr_rd = explode(",", $allr);
				$arr_rd = array_slice($arr_rd, 0, count($arr_rd)-1);
				$stringallw = '';
				$stringrd = '';
				$key_id = null;
				$status = 0;
				$conflict = 0;

				foreach($arrallw as $aw){
					$stringallw .= $aw.$st.' - '.$et.',';
				}
				foreach($arr_rd as $ar){
					$stringrd .= $ar.'RD,';
				}

				$schedules = Schedule::model()->findAll();
				$arrscheds = array();

				foreach($schedules as $schedule):
					$arrscheds[] = array(
													'id'=>$schedule['id'],
													'mon'=>$varMon = ($schedule['mon'] == null ? $varMon='RD' : $varMon=$schedule['mon']),
													'tue'=>$vartue = ($schedule['tue'] == null ? $vartue='RD' : $vartue=$schedule['tue']),
													'wed'=>$varwed = ($schedule['wed'] == null ? $varwed='RD' : $varwed=$schedule['wed']),
													'thur'=>$varthu = ($schedule['thur'] == null ? $varthu='RD' : $varthu=$schedule['thur']),
													'fri'=>$varfri = ($schedule['fri'] == null ? $varfri='RD' : $varfri=$schedule['fri']),
													'sat'=>$varsat = ($schedule['sat'] == null ? $varsat='RD' : $varsat=$schedule['sat']),
													'sun'=>$varsun = ($schedule['sun'] == null ? $varsun='RD' : $varsun=$schedule['sun']),

					);
				endforeach;
				foreach($arrscheds as $key=>$value):
					$val = array_slice($value, 1);
					foreach($val as $day=>$time){
						if($time != 'RD'){
							$sched_allw .= $day.$time.',';
						}else{
							$sched_rd .= $day.$time.',';
						}
					}

					//Same sched
					if($sched_allw == $stringallw && $sched_rd == $stringrd){
						$key_id = $value['id'];
						$status = 1;
					}
			
				$sched_allw = '';
				$sched_rd = '';
				endforeach;

#echo "<pre>";print_r($arrscheds);echo "</pre>";
				
				//Save
				if($key_id == null){
					$key_id = 0;
				}
				$check_conflict = Yii::app()->db->createCommand(
				'SELECT es.sched_id, es.emp_id, es.start_date, es.end_date, s.mon, s.tue, s.wed, s.thur, s.fri, s.sat, s.sun 
								FROM employee_schedule AS es 
								INNER JOIN schedule AS s ON s.id = es.sched_id 
								WHERE emp_id=\''.$id.'\' AND (start_date <= \''.date('Y-m-d', strtotime($sd)).'\' and end_date >= \''.date('Y-m-d', strtotime($sd)).'\' or end_date <= \''.date('Y-m-d', strtotime($ed)).'\')'
				)->queryAll();

				if($check_conflict != null){
					//conflict
					$conflict = 1;
				}

				if($es_id == null){
				 
					$model = new EmployeeSchedule;
					$model->emp_id = $id;
					$model->sched_id = $key_id;
					$model->start_date = date('Y-m-d', strtotime($sd));
					$model->end_date = date('Y-m-d', strtotime($ed));
					$model->status = $status;
					$model->conflict = $conflict;
					$model->save();
				}
				else{
					EmployeeSchedule::model()->updateByPk($es_id, array(
						'emp_id' => $id,
						'sched_id' => $key_id,
						'start_date' => date('Y-m-d', strtotime($sd)),
						'end_date' => date('Y-m-d', strtotime($ed)),
					));
				}

				$this->render('addfield',array('model'=>$model));
	}

	public function actionMod(){
				$model=new EmployeeSchedule;
				$this->render('mod',array('model'=>$model));
	}

}

