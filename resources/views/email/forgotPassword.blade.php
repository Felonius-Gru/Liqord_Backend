<table cellpadding="8" cellspacing="0" style="padding:0;width:100%!important;background:#ffffff;margin:0;background-color:#ffffff" border="0">
    <tbody>
        <tr>
            <td valign="top">
                <table cellpadding="0" cellspacing="0" style="border-radius:4px;border:1px #dceaf5 solid" border="0" align="center">
                    <tbody>
                        <tr>
                            <td colspan="3" height="6"></td>
                        </tr>
                        <tr style="line-height:0px">
                            <td width="100%" style="font-size:0px" align="center" height="1">
                                <!--<img width="135px" style="max-height:73px;width:135px" alt="" src="{{asset('themes/cova_email.png')}}" class="CToWUd">-->
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <table cellpadding="0" cellspacing="0" style="line-height:25px" border="0" align="center">
                                    <tbody>
                                        <tr>
                                            <td colspan="3" height="30"></td>
                                        </tr>
                                        <tr>
                                            <td width="36"></td>
                                            <td width="454" align="left" style="color:#444444;border-collapse:collapse;font-size:11pt;font-family:proxima_nova,'Open Sans','Lucida Grande','Segoe UI',Arial,Verdana,'Lucida Sans Unicode',Tahoma,'Sans Serif';max-width:454px" valign="top">
                                                Hi {{$name}},<br/>
                                                {!!$email_forgot_password!!} <br/> 
To begin, please login with you'r credentials, given below :<br/>

UserName : {{$username}} <br/>
Password : <u>{{$password}}</u> <br/>
URL      : {{asset("/")}}
                                            
                                            </td>
                                            <td width="36"></td>
                                        </tr>
                                        <tr><td colspan="3" height="36"></td></tr>
                                    </tbody>
                                </table>              
                            </td>
                        </tr>
                    </tbody>
                </table>
                <table cellpadding="0" cellspacing="0" align="center" border="0">
                    <tbody>
                        <tr>
                            <td height="10"></td>
                        </tr>
                        <tr>
                            <td style="padding:0;border-collapse:collapse">
                                <table cellpadding="0" cellspacing="0" align="center" border="0">
                                    <tbody>
                                        <tr style="color:#a8b9c6;font-size:11px;font-family:proxima_nova,'Open Sans','Lucida Grande','Segoe UI',Arial,Verdana,'Lucida Sans Unicode',Tahoma,'Sans Serif'">
                                            <td width="400" align="left"></td>
                                            <td width="128" align="right">{{date('Y')}} COVA</td>
                                        </tr>
                                    </tbody>
                                </table>
                                    
                            </td>
                        </tr>
                    </tbody>
                </table>
                                            
            </td>
        </tr>
    </tbody>
</table>