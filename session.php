<?php
session_start();

if($_GET['logout']=="1"){
 session_destroy();
}

if(!empty($_GET['login'])){
 $_SESSION['login']= $_GET['login'];
}

echo "<pre>";
print_r($_SESSION);
echo "</pre>";


/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
