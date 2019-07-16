<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html;charset=ISO-8859-1" />
<title>PHPSpellCheck Example - JavaScript API</title>
</head>
<body>
<form id="form1" name="form1" action="">
<textarea name="spellfield"  id="spellfield" cols="50" rows="20">This demo shows most of the functions of the JavaSCript API.  As you can see - all the features of the PHP API can be implemented using Javascript and manipulated at runtime.
</textarea>

<script type='text/javascript' src='../include.js' ></script>
<script type='text/javascript'>

// InstalationPath Automatically detected when include.js is attached to the page.

var mySpellInstance = new LiveSpellInstance()	
mySpellInstance.Fields="spellfield"
mySpellInstance.CaseSensitive = true;
mySpellInstance.CheckGrammar = true;
mySpellInstance.IgnoreAllCaps
mySpellInstance.IgnoreNumeric
mySpellInstance.Language = "English (International)";

mySpellInstance.CSSTheme="classic";
mySpellInstance.SettingsFile = "default-settings"
mySpellInstance.UserInterfaceLanguage = "en"
mySpellInstance.Delay = 888;
mySpellInstance.WindowMode = "Modal"
mySpellInstance.Strict = true;
mySpellInstance.ShowSummaryScreen = true;
mySpellInstance.ShowMeanings = true;
mySpellInstance.MeaningProvider = "http://www.thefreedictionary.com/{word}";
mySpellInstance.UndoLimit = 999;

/*
mySpellInstance.HiddenButtons = "btnAddToDict";
mySpellInstance.FormToSubmit = "form1";
mySpellInstance.CustomOpener = function(){window.open(this.url())}
mySpellInstance.CustomOpenerClose = function(){}
*/


//mySpellInstance.DrawSpellButton()
mySpellInstance.DrawSpellImageButton() 
//mySpellInstance.DrawSpellLink () 

/*
mySpellInstance.onDialogOpen = function(){alert('onDialogOpen');};
mySpellInstance.onDialogComplete = function(){alert('onDialogComplete');};
mySpellInstance.onDialogCancel = function(){alert('onDialogCancel');};
mySpellInstance.onDialogClose = function(){alert('onDialogClose');};
mySpellInstance.onChangeLanguage = function(Language){alert('onChangeLanguage: '+Language);};
mySpellInstance.onIgnore = function(Word){alert('onIgnore: '+Word);};
mySpellInstance.onIgnoreAll = function(Word){alert('onIgnoreAll: '+Word);};
mySpellInstance.onChangeWord = function(From, To) {alert('onChangeWord: '+From+" -> "+To);};
mySpellInstance.onLearnWord = function(Word){alert('onLearnWord: '+Word);};
mySpellInstance.onLearnAutoCorrect = function(From, To) {alert('onLearnAutoCorrect: '+From+" -> "+To);};
mySpellInstance.onUpdateFields = function(arrayOfFieldIds){alert('onUpdateFields: '+arrayOfFieldIds);};
*/

//ajaxSpellCheck(word, makeSuggestions)
//ajaxSpellCheckArray(arrayOfWords, makeSuggestions)
//ajaxDidYouMean(inputString)
//onSpellCheck (word, spelling, reason, suggestions)
/* Callback function for the ajaxSpellCheck method (also ajaxSpellCheckArray, see below)
Reason codes:
               + "E" - Enforced Correction
                + "B" - Word is on your banned words list
                + "C" - A CaSe Mistake
                + "X" - (infrequent) Unlicensed software
                + "S" - Spelling mistake. Not found in any dictionary and none of the above cases apply.
                + "" - Spelling was correct 
*/

mySpellInstance.onDidYouMean = function(suggestion, original){
	var message="";
	if(suggestion!=""){
	message = "Did You Mean: <a href='#' onclick='document.getElementById(\"testfield\").value=this.innerHTML;return false;'>"+suggestion+"</a>?";			
	}
	document.getElementById("dymMessages").innerHTML = message
}

 
</script>

<hr/>
<input type="button" value="Spell Check in Dialog Window" onclick="mySpellInstance.CheckInWindow()">
<input type="button" value="Spell Check in Situ" onclick="mySpellInstance.CheckInSitu()">
<input type="button" value="Turn on As-You-Type Spellchecking" onclick="mySpellInstance.ActivateAsYouType()">
<input type="button" value="Pause As-You-Type Spellchecking" onclick="mySpellInstance.PauseAsYouType()">
<hr/>
<input type="text" value="Bakc tothe furture" id="testfield">
<input type="button" value="Get Did You Mean Sugggestions" onclick="mySpellInstance.AjaxDidYouMean(document.getElementById('testfield').value);">
<div id='dymMessages'></div>
 <hr/>


 </form>
</body>
</html>
