<?php
// Include class 
include_once '../jqformconfig.php'; 
include_once $CODE_PATH.'jqForm.php'; 
// Create instance 
$newForm = new jqForm('newForm',array('method' => 'post', 'id' => 'newForm'));
// Set url
$newForm->setUrl($SERVER_HOST.$SELF_PATH.'bootstrapinline.php');
// Set Form header 
// Set Form Footer 
// Set parameters 
$jqformparams = array();
// Set SQL Command, table, keys 
$newForm->serialKey = true;
// Set Form layout 
$newForm->setView('inline');
$newForm->setStyling('bootstrap');

// Add elements
$newForm->addElement('UserName','text', array('label' => 'Name', 'id' => 'newForm_UserName'));
$newForm->addElement('Password','password', array('label' => 'Password', 'id' => 'newForm_Password'));
$newForm->addElement('newSubmit','submit', array('value' => 'Submit'));

// Add events
// Add ajax submit events
$newForm->setAjaxOptions( array('iframe' => false,
'forceSync' =>false) );
// Demo mode - no input 
$newForm->demo = true;
$newForm->demo = true;
// get the post and send it back

// Render the form 
echo $newForm->renderForm($jqformparams);
?>