<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Order Update</title>
</head>
<body style="font-family: Arial, sans-serif; background: #f7f7f7; padding: 20px;">
<table width="100%" cellpadding="0" cellspacing="0">
    <tr>
        <td align="center">
            <table width="600" cellpadding="0" cellspacing="0" style="background: #ffffff; padding: 20px; border-radius: 8px;">

                <tr>
                    <td style="text-align: center;">
                        <h2 style="margin-bottom: 10px;">Order Updated</h2>
                        <p>Hi {{$name}}</p>
                        <p style="color: #555;">Your order status has been updated. Its status is {{$orderStatus}} at the moment</p>
                        <p>Order No: {{$orderNum}}</p>
                    </td>
                </tr>
                <tr>
                    <td style="padding-top: 30px; text-align: center; color: #999; font-size: 12px;">
                        This is an automated message. Please do not reply.
                    </td>
                </tr>

            </table>
        </td>
    </tr>
</table>
</body>
</html>
