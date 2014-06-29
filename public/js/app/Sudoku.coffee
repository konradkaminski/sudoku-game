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

    check: () ->
      fields = $('input.number')
      data = []
      for field in fields
        row = $(field).data "row"
        col = $(field).data "col"
        tmp = data[row] || []
        tmp[col] = $(field).val()
        data[col] = tmp
      $.ajax '/game/check',
        type: 'POST'
        dataType: 'json'
        data: data
        success: (data, textStatus, jqXHR) ->
          console.log data
        
        
    reset: () ->
      @resetTimer()
      @reload()
      @startTimer()
      
    resetTimer: () ->
      @options.runTime = false
      @options.interval = false
      $(@options.timer_id).text '--:--:--'
      
    startTimer: () ->
      if @options.runTime is false
        @options.runTime = true
        @options.interval = setInterval(@counter, 1000)
        return

    counter: () =>
      @options.runSec++;
      min = Math.floor(@options.runSec / 60)
      hour = Math.floor(@options.runSec / 3600)
      sec = @options.runSec - (min * 60) - (hour * 3600)
      
      if (hour   < 10) {hour   = "0"+hour;}
      if (min < 10) {min = "0"+min;}
      if (sec < 10) {sec = "0"+sec;}
      
      $(@options.timer_id).text hour + ":" + min + ":" + sec
      
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