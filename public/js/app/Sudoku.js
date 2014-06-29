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
      this.options = $.extend({}, this.defaults, options);
      this.options.main_id = '#' + this.options.main_div;
      this.options.game_board_id = '#' + this.options.main_div + '-body';
      this.options.btn_reset_id = '#' + this.options.btn_reset;
      this.options.btn_check_id = '#' + this.options.btn_check;
      this.options.timer_id = '#' + this.options.timer;
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
      var col, data, field, fields, row, tmp, _i, _len;
      fields = $('input.number');
      data = [];
      for (_i = 0, _len = fields.length; _i < _len; _i++) {
        field = fields[_i];
        row = $(field).data("row");
        col = $(field).data("col");
        tmp = data[row] || [];
        tmp[col] = $(field).val();
        data[col] = tmp;
      }
      return $.ajax('/game/check', {
        type: 'POST',
        dataType: 'json',
        data: data,
        success: function(data, textStatus, jqXHR) {
          return console.log(data);
        }
      });
    };

    Sudoku.prototype.reset = function() {
      this.resetTimer();
      this.reload();
      return this.startTimer();
    };

    Sudoku.prototype.resetTimer = function() {
      this.options.runTime = false;
      this.options.interval = false;
      return $(this.options.timer_id).text('--:--:--');
    };

    Sudoku.prototype.startTimer = function() {
      if (this.options.runTime === false) {
        this.options.runTime = true;
        this.options.interval = setInterval(this.counter, 1000);
      }
    };

    Sudoku.prototype.counter = function() {
      var hour, min, sec;
      this.options.runSec++;
      min = Math.floor(this.options.runSec / 60);
      hour = Math.floor(this.options.runSec / 3600);
      sec = this.options.runSec - (min * 60) - (hour * 3600);
      return $(this.options.timer_id).text(hour + ":");
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
