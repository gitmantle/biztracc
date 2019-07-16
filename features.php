<?php
session_start();

$thisyear = date('Y');

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description" content="bizTracc">
<meta name="author" content="Murray Russell">
<title>bizTracc Features</title>
<link href="includes/css/bootstrap.min.css" rel="stylesheet">
<link href="includes/css/custom.css" rel="stylesheet">
</head>
<body style="padding-top: 5px;">

<div class="container">
<div class="jumbotron">
  <h2>bizTracc - Full Feature List</h2>
</div>
<div class="col-sm-12">
  <div class="panel panel-biztracc">
    <div class="panel-heading">Client Relationship Management</div>
    <div class="panel-body"> 
    <p>Administer Members. Members could be Prospective Clients or Current Clients of any one of your related companies (if you have more than one).</p>
    <ul>
    	<li>Details Held</li>
    	<ul>
        	<li>Names, dates of birth, occupation, status, type, gender</li>
            <li>Association (parents, children, lawyer, accountant etc.)</li>
            <li>Communications (Multiple phone, fax, mobile numbers and email addresses)</li>
            <li>Multiple addresses (Home, Work, Billing, Delivery etc.)</li>
            <li>Quotes and facility to convert quote to Sales Order</li>
            <li>Workflow - track sales process</li>
            <li>Notes</li>
            <li>Save documents against member record</li>
            <li>Emails. Send and record email correspondence with member</li>
            <li>Complaints - track any complaints made by member and resolution</li>
            <li>Accounting Categories - Direct Debit, Paid in Advance or other required categories</li>
            <li>Financials - which of your companies the member is a debtor or creditor of and current balances</li>
    	</ul>
        <li>Other Functions</li>
        <ul>
            <li>Incremental search on name</li>
            <li>Search on partial phone number</li>
            <li>Filter against multiple criteria</li>
            <li>Output filtered list for mailing or add to a Sales Campaign</li>   
        </ul>
        <li>Sales Campaigns</li>
        <ul>
        	<li>Create and administer multiple different campaigns</li>
            <li>Set up indivisual campaigns to be run in house or by third party marketers</li>
            <li>Facilities to track progress of campaign against set criteria</li>
            <li>Facilities to automatically email relevant sales representatives the results of calls to clients</li>
            <li>Produce statistical reports on campaigns</li>
        </ul>
        <li>Complaints Register</li>
        <ul>
        	<li>Note complaints from clients</li>
            <li>Track resolution process</li>
        </ul>
    </ul>
    </p>
      <p> </p>
    </div>
  </div>
</div>


<div class="col-sm-12">
  <div class="panel panel-biztracc">
    <div class="panel-heading">Financial Management</div>
    <div class="panel-body"> 
    <p></p>
    <ul>
    	<li>General Ledger</li>
    	<ul>
        	<li>Multiple branches/cost centres per company</li>
            <li>Multiple sub-accounts per account</li>
            <li>Sales Orders, Delivery Notes, Invoices</li>
            <li>Credit Notes, Receipts</li>
            <li>Allocate and unallocate receipts against invoices</li>
            <li>Balance Sheets, Profit and Loss, Trial Balance consolidated or by branch/cost centre</li>
            <li>Trading tax report</li>
            <li>Standard Transactions - debit and credit any account in the system (GL, DR, CR and Fixed Asset)</li>
            <li>Journal transactions (one or more debits split across one or more credits)</li>
            <li>Recurring transactions</li>
            <li>Requisition stock to own use</li>
            <li>Customisable reports and accounting documents (invoices etc.)</li>
    	</ul>
        <li>Debtors Ledger and Sales Transactions</li>
        <ul>
        	<li>Select debtors from members</li>
            <li>Multiple sub-accounts per account</li>
            <li>Debtors reports, aged balances etc.</li>
            <li>Sales Orders, Delivery Notes, Invoices</li>
            <li>Credit Notes, Receipts</li>
            <li>Allocate and unallocate receipts against invoices</li>
        </ul>
        <li>Creditors Ledger and Purchases Transactions</li>
        <ul>
        	<li>Select creditors from members</li>
            <li>Multiple sub-accounts per account</li>
            <li>Creditors reports, aged balances etc.</li>
            <li>Purchase Orders, Goods Received, Goods Returned</li>
            <li>Payments</li>
            <li>Allocate and unallocate payments against goods received notes</li>
        </ul>
        <li>Stock Control</li>
        <ul>
        	<li>Group and categorise stock items</li>
            <li>Track serial numbers (if relevant)</li>
            <li>Track stock levels in multiple locations (if relevant)</li>
            <li>Reports and stock take lists</li>
        </ul>
        <li>Fixed Assets</li>
        <ul>
        	<li>Administer an asset register</li>
            <li>One button depreciation</li>
            <li>Accounts automatically updated</li>
        </ul>
    </ul>
    </p>
      <p> </p>
    </div>
  </div>
</div>

<div class="col-sm-12">
  <div class="panel panel-biztracc">
    <div class="panel-heading">Logging Truck Operations Management</div>
    <div class="panel-body"> 
    <p>Designed to be used in conjuction with iPads in the truck cabs. The iPads are programmed to communicate with the web site when they sense a stong enough signal. Until then their data is stored locally. All financial detail automaticlaly flows through to the Financial Accounts</p>
        <ul>
        	<li>Enter data about collected load into the iPad</li>
            <li>By making each truck and trailer a separate cost centre obtain profit and loss per vehicle</li>
            <li>Running costs and income automatialy keep accounts up to date</li>
            <li>Track the location and routes of vehicles in real time or historically</li>
            <li>Incident register maintained from data entered from the trucks and administered at base</li>
            <li>Track tyres by serial number</li>
            <li>Full serviced and repair records for each vehicle</li>
        </ul>
      <p> </p>
      
    </div>
  </div>
</div>


<div class="col-sm-12">
  <div class="panel panel-biztracc">
    <div class="panel-heading">Chronic Medicine Distribution Management</div>
    <div class="panel-body"> 
    <p>All financial detail automaticlaly flows through to the Financial Accounts</p>
    	<li>Details of Members Held</li>
    	<ul>
        	<li>Names, dates of birth, occupation, status, type, gender</li>
            <li>Communications (Multiple phone, fax, mobile numbers and email addresses)</li>
            <li>Multiple addresses (Home, Work, Billing, Delivery etc.)</li>
            <li>Doctors particulars</li>
            <li>Results of Clinical Tests</li>
            <li>Financials - which of your companies the member is a debtor and current balances</li>
            <li>Similar details for Suppliers</li>
    	</ul> 
    	<li>Administer Depot Details</li>
    	<ul>
        	<li>Name, location and contact person</li>
            <li>Route Identifier</li>
    	</ul> 
    	<li>Distribution Process</li>
    	<ul>
        	<li>Generate distribution list of members who have the relevant credit</li>
            <li>Automatically checks sufficient stock to meet distribution requirements</li>
            <li>Generates emails to notify members who have insufficient funds for their next delivery</li>
            <li>Automatically generte invoices for deliveries against member's credit</li>
            <li>Prints distribution lists per member within relevant depot</li>
            <li>Prints depot address labels</li>
            <li>Prints dosage instrucitons for members to go with delivery</li>
    	</ul> 
    	<li>Stock Control</li>
    	<ul>
        	<li>Maintains stock lists</li>
            <li>Allows for link to generic alternative medicines</li>
            <li>Same stock reports as Financial Management</li>
    	</ul> 
     <p> </p>
      
    </div>
  </div>
</div>



<div class="col-sm-12">
  <p>&nbsp;</p>
  <p>&nbsp;</p>
  <p>&nbsp;</p>
</div>
<div class="navbar navbar-inverse navbar-fixed-bottom" role="navigation">
  <div class="container">
    <div class="navbar-text pull-left">
      <p>&copy; Murray Russell 2000 - <?php echo $thisyear; ?></p>
    </div>
  </div>
</div>
<!-- Bootstrap core JavaScript
    ================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
<script src="includes/js/bootstrap.min.js"></script>
</body>
</html>