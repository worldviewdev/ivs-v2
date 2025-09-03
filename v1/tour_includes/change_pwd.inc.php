<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td id="pageHead">
      <div id="txtPageHead">Change Password</div>
    </td>
  </tr>
</table>
<br />
<form action="" method="post" name="form1" id="form1" <?= validate_form() ?>>
  <table width="258" cellspacing="0" cellpadding="5" border="0" align="center" id="tablesan" class="tablesan">
    <tr>
      <td width="120" class="txtblack14">Old Password:</td>
      <td>
        <input type="password" name="old_password" value="<?= $old_password ?>" class="textfield_edit" style="width:250px;" alt="blank" emsg="Please enter old password">
      </td>
    </tr>
    <tr>
      <td class="txtblack14">New Password:</td>
      <td>
        <input type="password" name="password" value="<?= $repassword ?>" class="textfield_edit" style="width:250px;" alt="blank" emsg="Please enter new password">
      </td>
    </tr>
    <tr>
      <td nowrap="nowrap" class="txtblack14">Confirm Password:</td>
      <td>
        <input type="password" name="repassword" value="<?= $repassword ?>" class="textfield_edit" style="width:250px;" alt="blank" emsg="Please enter confirm password">
      </td>
    </tr>
    <tr>
      <td class="label">&nbsp;</td>
      <td>
        <input type="image" name="imageField" src="images/buttons/submit.gif" />
      </td>
    </tr>
  </table>
</form>