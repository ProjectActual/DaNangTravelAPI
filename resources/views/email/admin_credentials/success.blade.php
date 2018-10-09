<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title></title>
  <link rel="stylesheet" href="">
  <style>
  * {
    font-family: 'Helvetica Neue', 'Arial';
  }
</style>
</head>
<body>
  <div class="gmail_quote">
    <div style="margin:0px;background-color:#f4f3f4;font-family:Helvetica,Arial,sans-serif;font-size:12px" text="#444444" bgcolor="#F4F3F4" link="#21759B" alink="#21759B" vlink="#21759B" marginheight="0" marginwidth="0">
      <table border="0" width="100%" cellspacing="0" cellpadding="0" bgcolor="#F4F3F4">
        <tbody>
          <tr>
            <td style="padding:15px">
              <center>
                <table width="550" cellspacing="0" cellpadding="0" align="center" bgcolor="#ffffff">
                  <tbody>
                    <tr>
                      <td align="left"><div style="border:none">
                        <table style="line-height:1.6;font-size:12px;font-family:Helvetica,Arial,sans-serif;border:solid 1px #ffffff;color:#444" border="0" width="100%" cellspacing="0" cellpadding="0" bgcolor="#ffffff">
                          <tbody>
                            <tr style="background:#2e3291">
                              <td style="line-height:25px;padding:10px 20px;text-align:center">
                                <h1 style="color:#fff;font-size:30px;text-align:center">{{ $info['message'] }}</h1>
                              </td>
                            </tr>
                          </tbody>
                        </table>
                        <table style="margin-right:30px;margin-left:30px;color:#444;line-height:1.6;font-size:12px;font-family:Arial,sans-serif" border="0" width="490" cellspacing="0" cellpadding="0" bgcolor="#ffffff">
                          <tbody>
                            <tr>
                              <td colspan="2">
                                <div style="line-height:1.6">
                                  <div style="font-size:16px; display:block;">
                                    <p style="text-align: justify;">Bạn nhận được mail này vì Quản Trị Viên đã phê duyệt cho bạn thành công.</p>
                                    @if(!empty($info['reason']))
                                      <p style="text-align: justify; color:green;">Lí do: {{ $info['reason'] }}</p>
                                    @endif
                                  </div>
                                </div>
                              </td>
                            </tr>
                          </tbody>
                        </table>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </center>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</div>
</body>
</html>
