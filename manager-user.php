<?php
/**
*  This file is a class of ReporterGUI
*
*  @author itpao25
*  ReporterGUI Web Interface  Copyright (C) 2015
*
*  This program is free software; you can redistribute it and/or
*  modify it under the terms of the GNU General Public License as
*  published by the Free Software Foundation; either version 2 of
*  the License, or (at your option) any later version.
*
*  This program is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  @package ReporterGUI
*/

require_once("inc/heart.inc.php");
$RGWeb->getHeader("Users");

if($RGWeb->getGroup->isAdmin() == false) {
  die($RGWeb->getUtily->messageNoPermission());
}

/**
*  Edit user:
*  - Password
*  - Username
*  - Group
* @param id ? user
* @param action
*/
print "<div class=\"container\">";
if(isset($_GET['id'])):

  if($RGWeb->getGroup->isUserIDExits($_GET['id']) == false) die($RGWeb->getUtily->userNotExists());
  $user = $RGWeb->getGroup->getDetailsUser($_GET['id']);

  print "<h2 class=\"h2-head\"><i class=\"fa fa-user\"></i> User {$user[1]}</h2>";

  print "<div class=\"row\" >";
  print "<div class=\"colonna_50\" >";
  ?>
<table style="width:100%">
  <tr>
    <td class="bordato" ><b>ID</b></td>
    <td class="bordato" ><?= $user[0] ?></td>
	</tr>
  <tr>
    <td class="bordato" ><b>Last login</b></td>
    <td class="bordato" ><?= $user[2] ?></td>
	</tr>
  <tr>
    <td class="bordato" ><b>Last IP</b></td>
    <td class="bordato" ><?= $user[3] ?></td>
	</tr>
  <tr>
    <td class="bordato" ><b>Group</b></td>
    <td class="bordato" ><?= $user[4] ?></td>
	</tr>
</table>
<br />
  <?php
  print "<a href=\"manager-user.php?id={$user[0]}&action=delete&key={$RGWeb->getKeyID()}\"><button class=\"button-primario\"><i class=\"fa fa-times-circle\"></i> Delete user</button></a>";
  print "</div>";
  print "<div class=\"colonna_50\" ><div class=\"box_cont\">";
  ?>
  <div class="box-informazioni-green">
    Edit profile
  </div>
  <br />
  <form id="edit-user" action="manager-user.php?id=<?= $user[0] ?>" method="post" >
    <!-- Edit password -->
    <h4 style="margin: 0px; border-bottom: 1px solid #E6E6E6;margin-bottom: 10px;" >Password:</h4>
    <input name="editUser-changepassword" style="margin-bottom: 3px;" type="password" placeholder="New password" />
    <input name="editUser-changepassword2" type="password" placeholder="Retype password" />
    <br />
    <!-- Edit group -->
    <h4 style="margin: 0px; border-bottom: 1px solid #E6E6E6;margin-bottom: 10px;" >User group:</h4>
    <?= $RGWeb->getGroup->getSelectOptionEdit($user[0]); ?>
    <br />
    <!-- Salvataggio delle modifiche -->
    <input name="editUser-submit" type="submit" class="button-primario" value="Save" />
  </form>
  <script type="text/javascript">
    $("form").submit(function() {
      var url = "manager-user.php?id=<?= $user[0] ?>";
        $.ajax({
           type: "POST",
           url: url,
           data: $("form").serialize(),
           success: function(data)
           {
            // alert(data);
            $('html').html(data.replace(/<html>(.*)<\/html>/, "$1"));
           }
         });
         return false;
    });
  </script>
  <?php

  if(isset($_GET['action'])):

    $a = $_GET['action'];
    switch ($a) {
      case 'delete':

        // Delete user
        // Controllo prima se la chiave key GET è valida / impostata

        // Check user is not founder (ID 1)
        if($user[0] == 1) {
          print "<br /><div class=\"messaggio-errore\">You can not delete this user!</div>";
          print "<meta http-equiv=\"refresh\" content=\"1; URL=manager-user.php?id={$user[0]}\">";
          return;
        }
        if(isset($_GET['key'])):
          if($RGWeb->checkKeyID($_SESSION['rg_username'], $_GET['key']))
          {
            $RGWeb->deleteUser($user[0]);
            echo "<br /><div class='container messaggio-success'>User {$user[1]} (ID: {$user[0]}) was deleted successfully!</div>";
            echo "<meta http-equiv=\"refresh\" content=\"1; URL=users.php\">";
          }
        endif;
        break;
    }

  elseif(isset($_POST['editUser-changepassword']) &&
  isset($_POST['editUser-changepassword2']) &&
  isset($_POST['editUser-group'])):

    $newpassword = $_POST['editUser-changepassword'];
    $newpassword2 = $_POST['editUser-changepassword2'];
    $group = $_POST['editUser-group'];

    // Change password
    if(trim($newpassword) != "" && trim($newpassword2) != "")
    {
      if(trim($newpassword) == trim($newpassword2)):
          $RGWeb->getGroup->changePassword($user[0], $newpassword);
      else:
          print "<br /><div class='container messaggio-errore'>The passwords do not match!</div>";
      endif;
    }
    // Change group user
    if($RGWeb->getGroup->getGroupID($group) != 0) {
      $RGWeb->getGroup->changeGroup($user[0], $group);
    }

  endif;

else:
  print $RGWeb->getUtily->userNotExists();
endif;

print "</div>";
print "</div>";
print "</div>";
print "</div>";

$RGWeb->getFooter();
 ?>