<?php
// Include class 
include_once '../jqformconfig.php'; 
include_once $CODE_PATH.'jqForm.php'; 
// Create instance 
$newForm = new jqForm('newForm',array('method' => 'post', 'id' => 'newForm'));
// Set url
$newForm->setUrl($SERVER_HOST.$SELF_PATH.'defaultnodb.php');
// Set Form header 
// Set Form Footer 
// Set parameters 
$jqformparams = array();
// Set SQL Command, table, keys 
$newForm->serialKey = true;
// Set Form layout 
$newForm->setView('horizontal');
$newForm->setStyling('bootstrap');

// Add elements
$newForm->addElement('OrderID','hidden', array('label' => 'OrderID', 'id' => 'newForm_OrderID'));
$newForm->addElement('ShipName','text', array('label' => 'Name', 'maxlength' => '40', 'style' => '', 'id' => 'newForm_ShipName'));
$newForm->addElement('ShipAddress','text', array('label' => 'Address', 'maxlength' => '60', 'style' => '', 'id' => 'newForm_ShipAddress'));
$newForm->addElement('ShipCity','text', array('label' => 'City', 'maxlength' => '15', 'size' => '20', 'id' => 'newForm_ShipCity'));
$newForm->addElement('ShipPostalCode','text', array('label' => 'PostalCode', 'maxlength' => '10', 'size' => '20', 'id' => 'newForm_ShipPostalCode'));
$newForm->addElement('ShipCountry','text', array('label' => 'Country', 'maxlength' => '15', 'size' => '20', 'id' => 'newForm_ShipCountry'));
$newForm->addElement('Freight','number', array('label' => 'Freight', 'id' => 'newForm_Freight'));
$newForm->addElement('details','textarea', array('label' => 'Shipping details', 'rows' => '4', 'style' => '', 'id' => 'newForm_details'));
$newForm->addElement('express','checkbox', array('label' => 'Express ', 'id' => 'newForm_express'));
$newForm->addElement('Gender','radio', array('label' => 'Gender', 'value' => 'Male', 'text' => 'Male', 'id' => 'newForm_Gender'));
$newForm->addElement('Gender','radio', array('value' => 'Female', 'text' => 'Female', 'id' => 'newForm_Gender'));
$elem_12[]=$newForm->createElement('newSubmit','submit', array('value' => 'Submit'));
$newForm->addGroup("newGroup",$elem_12, array('style' => '', 'id' => 'newForm_newGroup'));
// Add events
// Add ajax submit events
$newForm->setAjaxOptions( array('iframe' => false,
'forceSync' =>false) );
// Demo mode - no input 
$newForm->demo = true;
$newForm->demo = true;
// get the post and send it back
if($newForm->oper == 'save') {
	$str = "";
	foreach($_POST as $key => $val)
	{
		$str .= $key." = ".$val."<br/>";
	}
	echo $str;
	exit;
}

// Render the form 
echo $newForm->renderForm($jqformparams);
?>