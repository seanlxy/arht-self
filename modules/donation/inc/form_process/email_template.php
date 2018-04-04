<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <!-- Facebook sharing information tags -->
    <meta property="og:title" content="{subject}">
    <!-- <link href='http://fonts.googleapis.com/css?family=Fresca' rel='stylesheet' type='text/css'> -->
    <title>{subject}</title>

    <style type="text/css">
        body {
            width: 100% !important;
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
            margin: 0;
            padding: 0;
            background: #f1f1f1;
        }
        a {
            color: #005EB0;
        }
        h1, h2, h3, p {
            margin: 1em 0;
        }
        pre {
            overflow-x: scroll;
            max-width: 455px;
            background-color: #f1f1f1;
            padding: 15px;
        }
        img{
            border:0;
            height:auto;
            line-height:100%;
            outline:none;
            text-decoration:none;
        }
        table td{
            border-collapse:collapse;
        }
        #backgroundTable{
            height:100% !important;
            margin:0;
            padding:0;
            width:100% !important;
        }

        .stripped tr td{
            border-bottom: 1px solid #EFEFEF;
        }
    
        .stripped td.label{
            font-weight: 700;
            background-color: #EFEFEF;
            border-bottom: 1px solid #fff;
            
        }
        @media only screen and (min-device-width: 601px) {
            .content {
                width: 800px !important;
            }
        }
    </style>
</head>

<body>

    <!--[if (gte mso 9)|(IE)]>
  <table width="750" cellpadding="10" cellspacing="0" border="0" align="center">
    <tr>
      <td>
<![endif]-->

    <!-- Outer Table -->
    <table style="width: 100%; background-color: #f1f1f1;" cellpadding="10" cellspacing="0" border="0" align="center">
        <tr>
            <td style="font-family: Helvetica, Arial, sans-serif; font-size: 14px; color: #333333;" align="center">
                <table class="content" style="width: 100%; max-width: 800px; background-color: #ffffff; border-width: 1px; border-color: #d1d1d1;" cellpadding="20" cellspacing="0" align="center">
                    <tr>
                        <td>
                            <!-- Email header -->
                            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td valign="middle" width="180">
                                        <a href="{root}">
                                            <img src="{root}/graphics/logo.png" alt="{company}" style="display:block;width:100%;">
                                        </a>
                                    </td>
                                    <td valign="middle">
                                        <h4 style="text-align:right;text-transform:uppercase;color:#000000;font-size:18px;margin:0;">{subject}</h4>
                                    </td>
                                </tr>
                            </table>
                            <!-- Email header end -->
                        </td>
                    </tr>
                    <tr>
                        <td style="font-family: Helvetica, Arial, sans-serif; font-size: 14px; line-height: 25px; padding-top: 0; color: #000000;" align="left">
                            <!-- Message Body -->
                            <p>Donation have been received. Please see the details below:</p>
                            <table cellpadding="5" cellspacing="0" width="100%" class="stripped">
                                <tr>
                                    <td width="60" style="background-color: #EFEFEF; border-bottom: 1px solid #fff;"><strong>Donation Type:</strong></td>
                                    <td width="90">{donation_type_name}</td>
                                </tr>
                                <tr>
                                    <td width="60" style="background-color: #EFEFEF; border-bottom: 1px solid #fff;"><strong>Donation Amount:</strong></td>
                                    <td width="90">${amount}</td>
                                </tr>
                                <tr>
                                    <td width="60" style="background-color: #EFEFEF; border-bottom: 1px solid #fff;"><strong>Full name:</strong></td>
                                    <td width="90">{full_name}</td>
                                </tr>
                                <tr>
                                    <td width="60" style="background-color: #EFEFEF; border-bottom: 1px solid #fff;"><strong>Email address:</strong></td>
                                    <td width="90"><a href="mailto:{email_address}">{email}</a></td>
                                </tr>
                                <tr>
                                    <td width="60" style="background-color: #EFEFEF; border-bottom: 1px solid #fff;"><strong>Phone/Mobile #:</strong></td>
                                    <td width="90">{phone_number}</td>
                                </tr>                                
                                <tr>
                                    <td width="60" style="background-color: #EFEFEF; border-bottom: 1px solid #fff;"><strong>Address:</strong></td>
                                    <td width="90">
                                        {full_address}
                                    </td>
                                </tr>
                                <tr>
                                    <td width="60" style="background-color: #EFEFEF; border-bottom: 1px solid #fff;"><strong>Reference Number:</strong></td>
                                    <td width="90">
                                        {ref_number}
                                    </td>
                                </tr>
                                <tr>
                                    <td width="60" style="background-color: #EFEFEF; border-bottom: 1px solid #fff;"><strong>Newsletter Subscription:</strong></td>
                                    <td width="90">{subscribe}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
                
                <!-- End Outer Table -->
            </td>
        </tr>
    </table>
    <!--[if (gte mso 9)|(IE)]>
  </td>
</tr>
</table>
<![endif]-->
</body>

</html>