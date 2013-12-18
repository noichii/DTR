<?php
/* @var $this CheckinoutController */
/* @var $model Checkinout */

$this->breadcrumbs=array(
	'Checkinouts'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List Checkinout', 'url'=>array('index')),
	array('label'=>'Manage Checkinout', 'url'=>array('admin')),
);
?>

<h1>Create Checkinout</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>