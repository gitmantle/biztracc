<?php
// SBMD - Simple Backup Mysql Database script
 
$lang ='en';  //indice of the "lang_...json" file with texts
$dir = 'backup/';  //folder to store the ZIP archive with SQL backup

session_start();
$html = $bmd_re ='';

//set object of backupmysql class
include 'backupmysql.class.php';
$bk = new backupmysql($lang, $dir);

$frm ='<h1>'. $bk->langTxt('msg_bmk') .'</h1><form action="'. $_SERVER['PHP_SELF'] .'" method="post" id="frm">
<h4>'. $bk->langTxt('msg_connect') .'</h4>
'. $bk->langTxt('msg_server') .': <input type="text" name="host" value="localhost"><br>
'. $bk->langTxt('msg_user') .': <input type="text" name="user" value="root"><br>
'. $bk->langTxt('msg_pass') .': <input type="text" name="pass" value=""><br>
'. $bk->langTxt('msg_database') .': <input type="text" name="dbname"><br>
<input type="submit" value="'. $bk->langTxt('msg_send') .'">
</form>';

//if not form send, set form wiith fields for connection data
if((!isset($_POST) || count($_POST) ==0) && !isset($_SESSION['bmd_re'])){
  if(isset($_SESSION['mysql'])) unset($_SESSION['mysql']);
  if(isset($_SESSION['bmd_re'])) unset($_SESSION['bmd_re']);
  $html = $frm;
}
else if(isset($_POST['host']) && isset($_POST['user']) && isset($_POST['pass']) && isset($_POST['dbname'])){
  $_POST = array_map('trim', array_map('strip_tags', $_POST));
  $_SESSION['mysql'] = ['host'=>$_POST['host'], 'user'=>$_POST['user'], 'pass'=>$_POST['pass'], 'dbname'=>$_POST['dbname']];
}

//if session with data for connecting to MySQL database (MySQL server, user, password, database name)
if(isset($_SESSION['mysql'])){
  @set_time_limit(600);

  $bk->setMysql($_SESSION['mysql']);  //set connection data

  //if form with tables to backup, create ZIP archive with backup. Else: restore backup, or Delete ZIP
  if(isset($_POST['tables']) || isset($_POST['bmd_zip'])){
    if(isset($_POST['tables'])) {
      $tables = array_map('strip_tags', $_POST['tables']);  //store tables in object
      $bmd_re = $bk->saveBkZip($tables);
    }
    else if(isset($_POST['bmd_zip'])){
      if(isset($_POST['file'])){
        $_POST['file'] = trim(strip_tags($_POST['file']));
        if($_POST['bmd_zip'] =='res_file') $bmd_re = $bk->restore($_POST['file']);
        else if($_POST['bmd_zip'] =='get_file') $bmd_re = $bk->getZipFile($_POST['file']);  //when to get ZIP file
        else if($_POST['bmd_zip'] =='del_file')$bmd_re = $bk->delFile($_POST['file']);  //when to delete ZIP
      }
      else $bmd_re = $bk->langTxt('er_sel_file');
    }

    //Keep response, Refresh to not send form-data again if refreshed
    $_SESSION['bmd_re'] = $bmd_re;
    header('Location: '. $_SERVER['REQUEST_URI']);
    exit;
  }

  //get response after refresh, delete that session
  if(isset($_SESSION['bmd_re'])){
    $bmd_re = $_SESSION['bmd_re'];
    unset($_SESSION['bmd_re']);
  }

  $html = '<h1>'. sprintf($bk->langTxt('msg_conn_to'), $_SESSION['mysql']['dbname']) .'</h1><div id="bmd_re">'. $bmd_re .'</div>';

  $tables = $bk->getListTables();  //array with [f, er] (form, error)

  //if not error when get to show tables, add form with checkboxes, else $frm
  if($tables['er'] =='') $html .= '<a href="'. $_SERVER['PHP_SELF'] .'" title="'. $bk->langTxt('msg_conn_other') .'" id="a_cd">&bull; '. $bk->langTxt('msg_conn_other') .'</a><h2 id="tab_frm_cht" class="tabvi">'. $bk->langTxt('msg_show_tables') .'</h2><h2 id="tab_frm_zip">'. $bk->langTxt('msg_show_files') .'</h2><br>'. $tables['f'] . $bk->getListZip($dir);
  else $html ='<div id="bmd_re">'. $tables['er'] .'</div>'. $frm;
}

$html .= '<div id="mp">Simple Backup MySQL Database PHP Script<br>From: <a href="http://coursesweb.net/" title="CoursesWeb.net">CoursesWeb.net</a></div>';

//set texts that will be added in JS (in bmd_lang)
$lang_js = '{
"msg_when_del":"'. str_replace('"', '\"', $bk->langTxt('msg_when_del')) .'",
"msg_loading":"'. str_replace('"', '\"', $bk->langTxt('msg_loading')) .'"
}';

header('Content-type: text/html; charset=utf-8');
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8" />
<title>Simple Backup MySQL Database</title>
<style>
<!--
body {
margin:0;
background:#fbfbfe;
padding:0;
text-align:center;
font-size:1em;
font-family:Calibri;
}
h1 {
margin:2px auto;
text-decoration:underline;
font-size:1.4em;
}
h1 span {
color:#0000eb;
letter-spacing:1px;
}
h3 {
margin:0 auto 8px auto;
font-size:1.1em;
text-align:left;
padding-left:2%;
}
h4 {
margin:0 auto 8px auto;
font-size:1.1em;
}
label {cursor:pointer;}
label:hover {
background:#fefefe;
text-decoration:underline;
color:#e00;
}
#frm {
width:330px;
margin:1em auto;
background:#eeeefb;
border:1px solid #33f;
border-radius:.7em;
line-height:150%;
padding:2px 4px 5px 4px;
font-size:1.1em;
}
#frm input:nth-of-type(2){
margin-left:21px;
}
#frm input:nth-of-type(3){
margin-left:33px;
}
#frm input:nth-of-type(4){
margin-left:35px;
}

#a_cd {
display:block;
margin:.5em 5% .5em 2px;
text-align:right;
font-size:1.1em;
color:#0000eb;
}
#a_cd:hover {
text-decoration:none;
color:#0000b0;
}

#bmd_re {
background:#fbfbcb;
border:1px solid #ef0000;
padding:3px;
font-weight:500;
color:#fb0000;
}
#bmd_re:empty { display:none;}
#bmd_load {
position:fixed;
left:0;
top:0;
margin:0;
width:100%;
height:100%;
background:#fafabe;
opacity:.9;
padding:15% 1% 1% 1%;
font-size:28px;
font-weight:600;
letter-spacing:1px;
z-index:9999;
}
#bmd_load span {
background:#fefefe;
border-radius:5px;
padding:3px;
line-height:155%;
}

h2 {
display:inline-block;
margin:0 5px 15px 5px;
border:1px solid #adadda;
background-color:#eaeafe;
padding:2px 3px;
font-size:1.13em;
letter-spacing:1px;
cursor:pointer;
border-radius:.5em;
}
h2.tabvi{
background-color:#cffbcf;
border:1px solid #b0f1be;
border-radius:1px;
}
h2:hover{
border:1px dotted #adadda;
background-color:#f9f9b8;
text-decoration:underline;
color:#0102d8;
}

input[type=submit], input[type=button] {
display:inline-block;
margin:8px 3px;
font-weight:600;
letter-spacing:1px;
cursor:pointer;
}

#frm_cht {
display:inline-block;
background:#efeffc;
padding:0 2px 5px 2px;
}
#frm_cht #ch_all {
font-weight:700;
letter-spacing:1px;
text-decoration:underline;
}
#frm_cht .ch_tables {
display:inline-block;
margin:8px 1px;
text-align:left;
vertical-align:top;
}
#frm_cht .ch_tables label {
display:block;
}

#frm_zip {
display:none;
background:#f9f9c0;
padding:0 4px 5px 2px;
}
#frm_zip label {
display:block;
margin:3px 1%;
text-align:left;
}

#mp {
margin:2% auto 20px auto;
font-style:oblique;
font-size:13px;
}
//-->
</style>
</head>
<body>
<?php echo $html; ?>
<script>
// <![CDATA[
var bmd_lang = <?php echo $lang_js; ?>  //object with texts from $lang

//display loading message
function bmdLoading(){
  document.querySelector('body').insertAdjacentHTML('beforeend', '<div id="bmd_load"><span>'+ bmd_lang.msg_loading +'</span></div>');
}

//if clicked on #ch_all, check all tables in .ch_tables
var ch_all = document.getElementById('ch_all');
if(ch_all){
  var ch_btn = ch_all.querySelector('input');
  var tables = document.querySelectorAll('#frm_cht .ch_tables input');
  ch_all.addEventListener('click', function(){
    if(tables){
      for(var i=0; i<tables.length; i++) tables[i].checked = ch_btn.checked;
    }
  });
}

//if #frm_zip
var frm_zip = document.getElementById('frm_zip');
if(frm_zip){
  var dir_bk = document.getElementById('dir_bk').value +'/';
  var zip_files = document.querySelectorAll('#frm_zip .zip_files');

  //get buttons in #frm_zip, register click to submit form according to button
  if(zip_files){
    var btn_zip = document.querySelectorAll('#frm_zip #res_file, #frm_zip #get_file, #frm_zip #del_file');
    for(var i=0; i<btn_zip.length; i++){
      btn_zip[i].addEventListener('click', function(e){
        for(var i2=0; i2<zip_files.length; i2++){
          if(zip_files[i2].checked === true){
            var conf_del = (e.target.id =='del_file') ? window.confirm(bmd_lang.msg_when_del) : true;
            if(conf_del){
              frm_zip['bmd_zip'].value = e.target.id;
              if(e.target.id !='get_file') bmdLoading();  //show Loading if not get_file request
              frm_zip.submit();
            }
            break;
          }
        }
      });
    }
  }

  /* Tabs effect ( http://coursesweb.net/ ) */

  var h2tabs = document.querySelectorAll('h2');
  var frm_cht = document.getElementById('frm_cht');
  // sets active tab, hides tabs content and shows content associated to current active tab
  // receives the id of active tab
  function tabsCnt(tab_id) {
    document.querySelector('h2.tabvi').removeAttribute('class');
    document.getElementById(tab_id).setAttribute('class', 'tabvi');
    frm_zip.style.display = 'none';
    frm_cht.style.display = 'none';
    document.getElementById(tab_id.replace('tab_', '')).style.display = 'inline-block';
  }

  // registers click for tabs
  for(var i=0; i<h2tabs.length; i++) h2tabs[i].addEventListener('click', function(){
    tabsCnt(this.id);
  });

  //on submit forms, show Loading
  frm_cht.addEventListener('submit', bmdLoading);
  frm_zip.addEventListener('submit', bmdLoading);
}
// ]]>
</script>
</body>
</html>