<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>客户端对象</title>
  <!-- 最新版本的 Bootstrap 核心 CSS 文件 -->
  <link rel="stylesheet" href="https://cdn.bootcss.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
  <!-- 最新的 Bootstrap 核心 JavaScript 文件 -->
  <script
    src="https://code.jquery.com/jquery-3.4.1.min.js"
    integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
    crossorigin="anonymous"></script>
  <script src="https://cdn.bootcss.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
</head>
<body>
<div class="well well-sm">当前用户数：
  <b id="PeopleNum" style="color: darkcyan">0</b>
</div>
<div id="msg">

</div>
<input type="text" id="text">
<input type="submit" value="发送数据" onclick="song()">
</body>
<script>
  //注册键盘事件
  document.onkeydown = function(e) {
    //捕捉回车事件
    var ev = (typeof event!= 'undefined') ? window.event : e;
    if(ev.keyCode == 13) {
      song();
    }
  };
  var msg = document.getElementById("msg");
  var PeopleNum = document.getElementById("PeopleNum");
  var wsServer = 'ws://192.168.10.10:18308/chat';
  //调用websocket对象建立连接：
  //参数：ws/wss(加密)：//ip:port （字符串）
  var websocket = new WebSocket(wsServer);
  //onopen监听连接打开
  websocket.onopen = function (evt) {
    // console.log(evt)
    //websocket.readyState 属性：
    /*
    CONNECTING  0   The connection is not yet open.
    OPEN    1   The connection is open and ready to communicate.
    CLOSING 2   The connection is in the process of closing.
    CLOSED  3   The connection is closed or couldn't be opened.
    */
    if(websocket.readyState == 1){
      msg.innerHTML = "链接已建立！<br/>"
    }else{
      msg.innerHTML = "Something is Wrong !<br/>";
    }
    let data = {
      'cmd': 'home.bind',
      'data': {
        "token": '<?=$token;?>'
      },
      "ext": {
        "ip": '127.0.0.1'
      }
    }
    // 将uid推送到服务端，与fd进行绑定
    websocket.send(JSON.stringify(data));
  };
  function song(){
    var text = document.getElementById('text').value;
    document.getElementById('text').value = '';
    //向服务器发送数据
    var msg = {
      "cmd": "home.echo",
      "data": {
        "token": "<?= $token;?>",
        "msg": text,
        'channel_id': "<?= $channel_id;?>"
      },
      "ext": {
        "ip": '127.0.0.1'
      }
    }
    console.log(JSON.stringify(msg))
    websocket.send(JSON.stringify(msg));
  }
  //监听连接关闭
   websocket.onclose = function (evt) {
     msg.innerHTML += "链接已关闭！<br/>"
   };
  //onmessage 监听服务器数据推送
  websocket.onmessage = function (evt) {
    // console.log(evt)
    if (isJsonString(evt.data)) {
      var jsonData = JSON.parse(evt.data);
      console.log(jsonData,1);
      msg.innerHTML += jsonData.me===true?'我：':'用户'+jsonData.user_id+"："
      msg.innerHTML += jsonData.msg + "<br/>"
      // if(jsonData.data){
      //   msg.innerHTML += jsonData.data +'<br>';
      // }else{
      //   PeopleNum.innerHTML = jsonData.ppp ;
      // }
    } else {
      if(evt.data){
        msg.innerHTML += evt.data +'<br>';
      }else{
        PeopleNum.innerHTML = evt.ppp ;
      }
    }
  };
  //监听连接错误信息
     websocket.onerror = function (evt, e) {
       websocket.close()
     };
</script>
<script>
  function isJsonString(str) {
    try {
      if (typeof JSON.parse(str) == "object") {
        return true;
      }
    } catch(e) {
    }
    return false;
  }
  // window.onbeforeunload = function (event) {
  //   websocket.close()
  //   event.returnValue = "...";
  // };
</script>
</html>
