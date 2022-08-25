<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office"><head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="x-apple-disable-message-reformatting">
  <!--[if !mso]><!--><meta http-equiv="X-UA-Compatible" content="IE=edge"><!--<![endif]-->
  <title>Suthar Enterprises</title>

    <style type="text/css">
      table, td { color: #000000; } a { color: #363675; text-decoration: none; }
@media only screen and (min-width: 620px) {
  .u-row {
    width: 600px !important;
  }
  .u-row .u-col {
    vertical-align: top;
  }

  .u-row .u-col-100 {
    width: 600px !important;
  }

}

@media (max-width: 620px) {
  .u-row-container {
    max-width: 100% !important;
    padding-left: 0px !important;
    padding-right: 0px !important;
  }
  .u-row .u-col {
    min-width: 320px !important;
    max-width: 100% !important;
    display: block !important;
  }
  .u-row {
    width: calc(100% - 40px) !important;
  }
  .u-col {
    width: 100% !important;
  }
  .u-col > div {
    margin: 0 auto;
  }
}
body {
  margin: 0;
  padding: 0;
}

table,
tr,
td {
  vertical-align: top;
  border-collapse: collapse;
}

p {
  margin: 0;
}

.ie-container table,
.mso-container table {
  table-layout: fixed;
}

* {
  line-height: inherit;
}

a[x-apple-data-detectors='true'] {
  color: inherit !important;
  text-decoration: none !important;
}

</style>
<!--[if !mso]><!--><link href="https://fonts.googleapis.com/css?family=Open+Sans:400,700" rel="stylesheet" type="text/css"><!--<![endif]-->

</head>

<body class="clean-body" style="margin: 0;padding: 0;-webkit-text-size-adjust: 100%;background-color: #ffffff;color: #000000">

<table style="font-family:'Open Sans',sans-serif;" role="presentation" cellpadding="0" cellspacing="0" width="100%" border="0">
    <tbody>
        <tr>
            <td style="overflow-wrap:break-word;word-break:break-word;padding:28px 33px 25px;font-family:'Open Sans',sans-serif;" align="left">

                <div style="color: #444444; line-height: 200%; text-align: center; word-wrap: break-word;">
                    <p style="font-size: 14px; line-height: 200%;"><span style="font-size: 22px; line-height: 44px;">Hi, {{ $contractor['name'] }}</span><br><span style="font-size: 16px; line-height: 32px;">Thank you again for choosing us. This email is registered on Suthar Enterprises. Please use below login details in Mobile App to access your account. </span></p>
                    <p style="font-size: 14px; line-height: 200%;"><span style="font-size: 16px; line-height: 32px;">Your account username is:  <strong>{{ $contractor['email'] }}</strong></span></p>
                    <p style="font-size: 14px; line-height: 200%;"><span style="font-size: 16px; line-height: 32px;">Your account Password is:  <strong>{{ $password }}</strong></span></p>
                </div>

            </td>
        </tr>
    </tbody>
</table>
</body></html>