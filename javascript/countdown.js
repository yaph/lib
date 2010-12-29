var Countdown = {
  setContainerId: function(id) {
    Countdown.container_id = id;
  },
  setMessage: function(message) {
    Countdown.message = message;
  },
  start: function(y, m, d, h, mi, s, ms) {
    if ("undefined" == typeof h) h = 0;
    if ("undefined" == typeof mi) mi = 0;
    if ("undefined" == typeof s) s = 0;
    if ("undefined" == typeof ms) ms = 0;
    var targetUTC = new Date.UTC(y, m, d, h, mi, s, ms);
    var now = new Date();
    // new Date(year, month, date [, hour, minute, second, millisecond ])
    var nowUTC = new Date.UTC(now.getFullYear(), now.getMonth(), now.getDate(),
        now.getHours(), now.getMinutes(), now.getSeconds(), now.getMilliseconds());
    if (nowUTC < targetUTC) {
      var dd = targetUTC - nowUTC;
      var sec = 1000;
      var min = sec * 60;
      var hour = min * 60;
      var day = hour * 24;
      var mday = dd % day;
      var mhour = mday % hour;
      var dday = Math.floor(dd / day);
      var dhour = Math.floor(mday / hour);
      var dmin = Math.floor(mhour / min);
      var dsec = Math.floor((mhour % min) / sec);
      day_text = dday == 1 ? 'day' : 'days';
      var html = '<div id="cd"><div id="cdday">'+dday+' '+day_text+'</div><div id="cdtime">'
        +Countdown.leadingZero(dhour)+':'+Countdown.leadingZero(dmin)+':'+Countdown.leadingZero(dsec)+'</div></div>';
      document.getElementById(Countdown.container_id).innerHTML=html;
      setTimeout(function(){Countdown.start(y, m, d, h, mi, s, ms);},1000);
    } else {
      document.getElementById(Countdown.container_id).innerHTML='<div id="cd"><h3 id="cdh3">'+Countdown.message+'</h3></div>';
    }
  },
  leadingZero: function(num) {
    str = '' + num;
    if (str.length < 2) {
      str = 0 + str;
    }
    return str;
  }
};