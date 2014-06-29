((window, $) ->
  class Sudoku
    defaults:
      main_div: null
      log: false
      runTime: false
      runSec: 0
      
    logfn: (data) ->
      if @options.log is true
        console.log "SUDOKU LOGGER:"
        console.log data

    constructor: (el, options) ->
      @options = $.extend {}, @defaults, options
      @options.main_id = '#' + @options.main_div
      @options.game_board_id = '#' + @options.main_div + '-body'
      @options.btn_reset_id = '#' + @options.btn_reset
      @options.btn_check_id = '#' + @options.btn_check
      @options.timer_id = '#' + @options.timer
      @options.msg_id = '#' + @options.msg
      @init()
      @reset()
      
    init: () ->
      @options.url_t = $(@options.main_id).data "url-t"
      @options.url_r = $(@options.main_id).data "url-r"
      @options.url_c = $(@options.main_id).data "url-c"
      $(@options.btn_reset_id).on "click", () =>
        @reset()
        return false
        
      $(@options.btn_check_id).on "click", () =>
        @check()

    check: () =>
      fields = $('input.number')
      data = "=";
      for field in fields
        row = $(field).data "row"
        col = $(field).data "col"
        val = $(field).val()
        data = data  + "#{row}|#{col}|#{val}="

      $.ajax '/game/check',
        type: 'POST'
        dataType: 'json'
        data: {data: data}
        success: (data, textStatus, jqXHR) =>
          if data.isValid is true
            @options.runTime = false
          @showMsg(data.msg)
        
        
    showMsg: (msg) =>
      $(@options.msg_id).text ""
      $(@options.msg_id).show()
      $(@options.msg_id).text msg
      msgId = @options.msg_id
      setTimeout ( ->
        $(msgId).hide()
      ), 5000
      

      
#      setTimeout(function() {
#          $('#results').hide();
#      }, 5000);
      
    reset: () ->
      @resetTimer()
      @reload()
      @startTimer()
      
    resetTimer: () ->
      @options.runTime = false
      window.clearInterval @options.interval
      @options.interval = false
      $(@options.timer_id).text '--:--:--'
      
    startTimer: () ->
      if @options.runTime is false
        @options.runSec = 0
        @options.runTime = true
        @options.interval = setInterval(@counter, 1000)
        return

    counter: () =>
      if @options.runTime is true
        @options.runSec++;
        min = Math.floor(@options.runSec / 60)
        hour = Math.floor(@options.runSec / 3600)
        sec = @options.runSec - (min * 60) - (hour * 3600)

        hourTxt = new String(hour)
        minTxt = new String(min)
        secTxt = new String(sec)

        if hourTxt.length < 2
          hourTxt = "0#{hourTxt}"
        if minTxt.length < 2 
          minTxt = "0#{minTxt}"
        if secTxt.length < 2 
          secTxt = "0#{secTxt}"

        $(@options.timer_id).text "#{hourTxt}:#{minTxt}:#{secTxt}"
      
    reload: () ->
      $(@options.game_board_id).load @options.url_r

  #define plugin
  $.fn.extend Sudoku: (option, args) ->
    @each ->
      $this = $(this)
      data = $this.data('Sudoku')
      
      if !data
        $this.data 'Sudoku', (data = new Sudoku(this, option))
      if typeof option == 'string'
        data[option].apply(data, args)
) window, window.$