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
				'actions'=>array('create','update','viewsched','empsched'),
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
		$employee = Employee::model()->findAll();
    $emp = array();
		    foreach($employee as $emps){
					array_push($emp, $emps['id'].". ".$emps['lastname']." ".$emps['firstname']);
				}
		
		$alert = '';
		$alertTo = null;
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
						$saveit++;
						$notExistName .= $bit.",";
			}
		endforeach;
		if($saveit == 0){
			foreach($output as $key => $value):
						$model = new EmployeeSchedule;
						$model->emp_id = $key;
						$model->sched_id = $arr_sched[0];
						$model->start_date = $startD;
						$model->end_date = $endD;
						$model->save();
			endforeach;
			$alert = "<div class='alert-success' style='padding:10px'>SUCCESS!<br />Employee Schedule has been Created.</div>";
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

	public function actionviewsched()
	{
		$model=new EmployeeSchedule;
		$alert = '';

		$employees = Yii::app()->db->createCommand('
			SELECT e.id, e.firstname, e.lastname, e.middle_initial, e.position_id, e.department_id, dept.name
			FROM employee AS e
			LEFT JOIN department AS dept ON e.department_id = dept.id
			')->queryAll();

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);
		#$emps_lists = Employee::model()->findAll();

		$department = Department::model()->findAll();
		$startDate = null;
		$endDate = null;
		$emps_lists = null;
		if(isset($_POST['EmployeeSchedule']))
		{
			$startDate = $_POST['startDate'];
			$endDate = $_POST['endDate'];
			if(strtotime($startDate) > strtotime($endDate)){
				$alert = '<div class="alert-error" style="padding:10px;">ERROR<br />Invalid Date</div>';
			}else{
						$emps_lists=Yii::app()->db->createCommand('
														SELECT e.id, e.firstname, e.lastname, es.emp_id, es.start_date, es.end_date, s.mon, s.tue, s.wed, s.thur, s.fri, s.sat, s.sun 
														FROM employee AS e 
														LEFT JOIN  employee_schedule AS es  ON e.id = es.emp_id and es.start_date >= "'.$startDate.'" and es.end_date <= "'.$endDate.'"
														LEFT JOIN schedule AS s ON es.sched_id = s.id 
														')->queryAll();
			}
		}else{
			
		}
		$this->render('viewsched',array(
			'model'=>$model,
			'emps_lists'=>$emps_lists,
			'startDate' => $startDate,
			'endDate' => $endDate,
			'employees' => $employees,
			'alert'=>$alert,
			'department'=>$department,
		));
	}

	public function actionempsched($id)
	{
		$model=new EmployeeSchedule;
		$alert = '';

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
			if(strtotime($startDate) > strtotime($endDate)){
				$alert = '<div class="alert-error" style="padding:10px;">ERROR<br />Invalid Date</div>';
			}else{
						$checkinout = Yii::app()->db->createCommand('
							SELECT * FROM `checkinout` where (date >= "'.$startDate.'" && date <= "'.$endDate.'") && user_id = '.$id
							)->queryAll();  //Checkin.out

						$emps_lists=Yii::app()->db->createCommand('
														SELECT e.id, e.firstname, e.lastname, es.emp_id, es.start_date, es.end_date, s.mon, s.tue, s.wed, s.thur, s.fri, s.sat, s.sun 
														FROM employee AS e 
														LEFT JOIN  employee_schedule AS es  ON e.id = es.emp_id and es.start_date >= "'.$startDate.'" and es.end_date <= "'.$endDate.'"
														LEFT JOIN schedule AS s ON es.sched_id = s.id 
														WHERE e.id ='.$id
														)->queryAll();

			}
		}else{
			
		}
		$this->render('empsched',array(
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
}
