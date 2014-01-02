<?php
/* @var $this EmployeeScheduleController */
/* @var $model EmployeeSchedule */

$this->breadcrumbs=array(
	'Employee Schedules'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List EmployeeSchedule', 'url'=>array('index')),
	array('label'=>'Manage EmployeeSchedule', 'url'=>array('admin')),
);
?>

<h2>Employees List of Schedule</h2>

<?php $this->renderPartial('_viewsched', array(
	'model'=>$model,
	'emps_lists'=>$emps_lists,
	'startDate'=>$startDate,
	'endDate'=>$endDate,
	'employees'=>$employees,
	'alert'=>$alert,
	'department'=>$department,
	)); ?>
