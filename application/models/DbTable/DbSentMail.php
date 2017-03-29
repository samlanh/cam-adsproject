<?php
/**
 * @Date 20/03/2017
 * @author vandy
 * @version 1.0
 */
class Application_Model_DbTable_DbSentMail extends Zend_Db_Table_Abstract
{
    protected $_name = '';
    
    public function verifyAccount(){
    	
    }
    public function sentCodeVerifyAcc($data){
    	$db = $this->getAdapter();
    	$to_Email=$data['email'];
    	$to_Email_admin       = 'support@camapp.com'; //sender
    	
    	
    	$string_note="Please note that this email address was linked to a new www.cam-app.com account. If you don't know anything about it, please just ignore this email and we won't bother you again.";
    	$htmlContent ='
    				<table width="629" align="center" cellpadding="0" cellspacing="0" border="0" bgcolor="#fdfdfd" style="border-bottom:1px solid #dde5f1; border-radius:6px 6px 0 0; background: url('."'header-email.jpg'".') no-repeat; height: 100px;">                                                            
							<tbody>
								<tr>                                                                  
									<td width="629" align="left" style="min-width:629px;">                                               
											
									</td>
								</tr>
							</tbody>
						</table> ';
    	$htmlContent.='<table width="629" align="center" cellpadding="0" cellspacing="0" border="0" style=" background: #eff3f7;">
							<tbody>
								<tr>
									<td colspan="3" height="25" style="font-size:0;line-height:0;">&nbsp;</td>
								</tr>                                                                                                
								<tr>
									<td width="30" class="wz"></td>	
									<td style="color: #727e8d;font-family: '."'Kh Battambang','Khmer OS Battambang'".',calibri,Arial,Helvetica,sans-serif; font-size: 13px;font-weight: lighter;text-align: left;line-height: 23px;">
										Hi Mr '.$data['user_name'].',<br>Welcome to  <a href="http://www.cam-app.com/" target="_blank" style="text-decoration: none;color: #0cb689;font-weight: bold;" >www.cam-app.com</a> marketplace for buying and selling websites.
										<br />Please enter the following code <span style="text-decoration: none;color: #0cb689;font-weight: bold; font-size: 24px;" >'.$data['code_random'].'</span> to verify your account.
										<br />
										<br />
										We look forward to having you on board.
										<br />
										<br />
										'.$string_note.'
										<br />
										<br />
										Kind Regards, 
										<br />
										The Cam App Supporting Team
									</td>
									<td width="30"></td>
								</tr>
								<tr>
									<td colspan="3" height="10" style="font-size:0;line-height:0;">&nbsp;</td>
								</tr>                                                                                          
							</tbody>
						</table> ';
    	$htmlContent.='<table width="629" align="center" cellpadding="0" cellspacing="0" border="0" bgcolor="#fdfdfd" style="border-bottom:1px solid #dde5f1; border-radius:0 0 6px 6px; height: 50px;background: #0cb689;">                                                            
							<tbody>
								<tr>                                                                  
									<td width="629" align="left" style="min-width:629px;">                                               
											<a href="http://www.cam-app.com/" target="_blank" style="text-decoration: none;color: #fff;font-weight: bold; padding-left: 30px;"  >www.cam-app.com</a> <span style="color: #ececec; font-size: 12px;">- The marketplace for buying and selling websites in Cambodia.</span>
									</td>
								</tr>
							</tbody>
						</table> ';
    	$subject        = 'Verify Account'; //Subject line for emails
    	 
    	$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= "Content-type:text/html; charset=UTF-8" . "\r\n";
    	 
    	$headers .= 'From: '.$to_Email_admin."\r\n".
    			'Reply-To: '.$to_Email."\r\n" .
    			'X-Mailer: PHP/' . phpversion();
    	
    	$sentMail = @mail($to_Email_admin, $subject, $htmlContent .'<br />', $headers, "-f " . $to_Email_admin);
    	$sentMail = @mail($to_Email, $subject, $htmlContent, $headers, "-f " . $to_Email_admin);
    	
    	if(!$sentMail)  {
    		$return="HTTP/1.1 500 Could not send mail! Sorry..";
    	} else {
    		$return='Hi '.$data['user_name'] .', Thank you for your register! ';
    		$return.='Your email has been delivered.If do not delivery contact admin! ';
    	}
    	return $return;
    	 
    }
}


