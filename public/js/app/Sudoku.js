var __bind = function(fn, me){ return function(){ return fn.apply(me, arguments); }; };

(function(window, $) {
  var Sudoku;
  Sudoku = (function() {
    Sudoku.prototype.defaults = {
      main_div: null,
      log: false,
      runTime: false,
      runSec: 0
    };

    Sudoku.prototype.logfn = function(data) {
      if (this.options.log === true) {
        console.log("SUDOKU LOGGER:");
        return console.log(data);
      }
    };

    function Sudoku(el, options) {
      this.counter = __bind(this.counter, this);
      this.showMsg = __bind(this.showMsg, this);
      this.check = __bind(this.check, this);
      this.options = $.extend({}, this.defaults, options);
      this.options.main_id = '#' + this.options.main_div;
      this.options.game_board_id = '#' + this.options.main_div + '-body';
      this.options.btn_reset_id = '#' + this.options.btn_reset;
      this.options.btn_check_id = '#' + this.options.btn_check;
      this.options.timer_id = '#' + this.options.timer;
      this.options.msg_id = '#' + this.options.msg;
      this.init();
      this.reset();
    }

    Sudoku.prototype.init = function() {
      var _this = this;
      this.options.url_t = $(this.options.main_id).data("url-t");
      this.options.url_r = $(this.options.main_id).data("url-r");
      this.options.url_c = $(this.options.main_id).data("url-c");
      $(this.options.btn_reset_id).on("click", function() {
        _this.reset();
        return false;
      });
      return $(this.options.btn_check_id).on("click", function() {
        return _this.check();
      });
    };

    Sudoku.prototype.check = function() {
      var col, data, field, fields, row, val, _i, _len,
        _this = this;
      fields = $('input.number');
      data = "=";
      for (_i = 0, _len = fields.length; _i < _len; _i++) {
        field = fields[_i];
        row = $(field).data("row");
        col = $(field).data("col");
        val = $(field).val();
        data = data + ("" + row + "|" + col + "|" + val + "=");
      }
      return $.ajax('/game/check', {
        type: 'POST',
        dataType: 'json',
        data: {
          data: data
        },
        success: function(data, textStatus, jqXHR) {
          if (data.isValid === true) {
            _this.options.runTime = false;
          }
          return _this.showMsg(data.msg);
        }
      });
    };

    Sudoku.prototype.showMsg = function(msg) {
      var msgId;
      $(this.options.msg_id).text("");
      $(this.options.msg_id).show();
      $(this.options.msg_id).text(msg);
      msgId = this.options.msg_id;
      return setTimeout((function() {
        return $(msgId).hide();
      }), 5000);
    };

    Sudoku.prototype.reset = function() {
      this.resetTimer();
      this.reload();
      return this.startTimer();
    };

    Sudoku.prototype.resetTimer = function() {
      this.options.runTime = false;
      window.clearInterval(this.options.interval);
      this.options.interval = false;
      return $(this.options.timer_id).text('--:--:--');
    };

    Sudoku.prototype.startTimer = function() {
      if (this.options.runTime === false) {
        this.options.runSec = 0;
        this.options.runTime = true;
        this.options.interval = setInterval(this.counter, 1000);
      }
    };

    Sudoku.prototype.counter = function() {
      var hour, hourTxt, min, minTxt, sec, secTxt;
      if (this.options.runTime === true) {
        this.options.runSec++;
        min = Math.floor(this.options.runSec / 60);
        hour = Math.floor(this.options.runSec / 3600);
        sec = this.options.runSec - (min * 60) - (hour * 3600);
        hourTxt = new String(hour);
        minTxt = new String(min);
        secTxt = new String(sec);
        if (hourTxt.length < 2) {
          hourTxt = "0" + hourTxt;
        }
        if (minTxt.length < 2) {
          minTxt = "0" + minTxt;
        }
        if (secTxt.length < 2) {
          secTxt = "0" + secTxt;
        }
        return $(this.options.timer_id).text("" + hourTxt + ":" + minTxt + ":" + secTxt);
      }
    };

    Sudoku.prototype.reload = function() {
      return $(this.options.game_board_id).load(this.options.url_r);
    };

    return Sudoku;

  })();
  return $.fn.extend({
    Sudoku: function(option, args) {
      return this.each(function() {
        var $this, data;
        $this = $(this);
        data = $this.data('Sudoku');
        if (!data) {
          $this.data('Sudoku', (data = new Sudoku(this, option)));
        }
        if (typeof option === 'string') {
          return data[option].apply(data, args);
        }
      });
    }
  });
})(window, window.$);
