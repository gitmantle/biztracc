<?php
// Include class 
include_once '../../jq-config.php'; 
include_once $CODE_PATH.'jqForm.php'; 
// Create instance 
$customers = new jqForm('customers',array('method' => 'post', 'id' => 'customers'));
// Create the connection 
include_once $CODE_PATH.'DBdrivers/jqGridPdo.php'; 
$conn = new PDO(DB_DSN,DB_USER,DB_PASSWORD);
$conn->query("SET NAMES utf8");
$customers->setConnection( $conn);
// Set url
$customers->setUrl($SERVER_HOST.$SELF_PATH.'customer.php');
// Set parameters 
$CustomerID = jqGridUtils::GetParam('CustomerID','ALFKI');
$CustomerID = is_string($CustomerID) ? (string)$CustomerID : '';
$jqformparams = array($CustomerID);
// Set SQL Command, table, keys 
$customers->SelectCommand = 'SELECT CustomerID, CompanyName, Phone, PostalCode, City FROM customers WHERE CustomerID =?';
$customers->table = 'customers';
$customers->setPrimaryKeys('CustomerID');
$customers->serialKey = true;
// Set Form layout 
$customers->setColumnLayout('twocolumn');
// Add elements
$customers->addElement('CustomerID','text', array('label' => 'CustomerID', 'maxlength' => '5', 'readonly' => '1', 'size' => '7', 'id' => 'customers_CustomerID'));
$customers->addElement('CompanyName','text', array('label' => 'CompanyName', 'maxlength' => '40', 'size' => '40', 'id' => 'customers_CompanyName'));
$customers->addElement('Phone','text', array('label' => 'Phone', 'maxlength' => '24', 'size' => '25', 'id' => 'customers_Phone'));
$customers->addElement('PostalCode','text', array('label' => 'PostalCode', 'maxlength' => '10', 'id' => 'customers_PostalCode'));
$customers->addElement('City','text', array('label' => 'City', 'maxlength' => '15', 'size' => '20', 'id' => 'customers_City'));
$elem_6[]=$customers->createElement('newSubmit','submit', array('value' => 'Submit'));
$elem_6[]=$customers->createElement('newButton','button', array('value' => 'Close', 'id' => 'close_modal'));
$customers->addGroup("newGroup",$elem_6, array('style' => 'text-align:right;', 'id' => 'customers_newGroup'));
// Add events
$onclicknewButton = <<< CLICKNEWBUTTON
function(event) 
{
  if($("#ajax-dialog") ) {
    $("#ajax-dialog").remove();
  }
}
CLICKNEWBUTTON;
$customers->addEvent('close_modal','click',$onclicknewButton);
// Add ajax submit events
$success = <<< SU
function( response, status, xhr) {
if(response=='success')
{
  $("#grid").trigger("reloadGrid", [{current:true}]);
}
}
SU;
$customers->setAjaxOptions( array('dataType'=>null,
'resetForm' =>null,
'clearForm' => null,
'success' =>'js:'.$success,
'iframe' => false,
'forceSync' =>false) );
// Render the form 
echo $customers->renderForm($jqformparams);
?>