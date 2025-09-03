<form action="" method="post" <?= validate_form() ?>>
  <table border="0" align="center" cellpadding="0" cellspacing="0" style="margin-top:20px;">
    <tr>
      <td valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td align="center" valign="top"><span style="font-family:Trebuchet MS, Arial, Verdana; font-size:26px; color:#000; font-weight:nomal;">Welcome to <font color="#FF9900">
            <?= SITE_NAME ?>
          </font> Tours Administration!</span></td>
    </tr>
    <tr>
      <td>
        <table width="100%" border="0" cellpadding="5" cellspacing="1">
          <tr>
            <td>
              <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td width="8%" align="left"><img src="images/icons/keys.gif" width="32" height="32" /></td>
                  <td width="92%" align="left" valign="middle"><span class="txtLight">Please enter a valid username and password to gain access to the administration console.</span></td>
                </tr>
                <tr>
                  <td colspan="2" align="left">
                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                      <tr>
                        <td width="66%" align="center" valign="top">
                          <table border="0" cellpadding="7" cellspacing="0">
                            <tr>
                              <td>
                                <table width="100%" border="0" cellspacing="5" cellpadding="0">
                                  <tr>
                                    <td>&nbsp;</td>
                                    <td valign="top">&nbsp;</td>
                                  </tr>
                                  <tr>
                                    <td>
                                      <table width="100%" border="0" cellspacing="0" cellpadding="1">
                                        <tr>
                                          <td align="left" class="txtblack16"><strong>Username</strong>:</td>
                                          <td align="left">
                                            <input name="login_id" type="text" class="textfield_login" id="login_id" value="<?= $login_id ?>" size="25" alt="blank" emsg="Please enter username" />
                                          </td>
                                        </tr>
                                        <tr>
                                          <td height="8" colspan="2" align="left"></td>
                                        </tr>
                                        <tr>
                                          <td align="left" class="txtblack16"><strong>Password</strong>:</td>
                                          <td align="left">
                                            <input name="password" type="password" class="textfield_login" value="<?= $password ?>" size="25" alt="blank" emsg="Please enter password" />
                                          </td>
                                        </tr>
                                      </table>
                                    </td>
                                    <td valign="middle">
                                      <input type="image" src="images/buttons/submit_login.gif" alt="Submit" border="0" />
                                    </td>
                                  </tr>
                                </table>
                              </td>
                            </tr>
                          </table>
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</form>