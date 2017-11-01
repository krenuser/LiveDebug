var LiveDebugApp = {
    
    editor: null,           // Ace editor instance
    cFileName: '',          // current file name
    cRunMode: 'file',       // file | eval
    cRunID: null,           // random 6-digit value to avoid overwriting of script file by multiple LiveDebug tabs in one browser
    
    // -- init LiveDebug JS function
    init: function() {
        LiveDebugApp.editor = ace.edit('code');
        LiveDebugApp.editor.getSession().setMode('ace/mode/php');
        
        $(window).on('resize', LiveDebugApp.onResize);
        // -- call resize handler right now
        LiveDebugApp.onResize();
        
        $(window).on('keydown', LiveDebugApp.onKeyDown);
        
        // -- setting app handlers for all a.appMenuItem's` 
        $('.appMenuItem')
            .on('click', function(){LiveDebugApp.onMenuItemClick(this); })
            .on('click', function(e){e.preventDefault(); });
        
        // -- setting cRunID once, when tab open/reloaded
        LiveDebugApp.cRunID = parseInt(Math.random() * 100000);
        
        // -- setting "user runmode icon" if no "forced by config.ini runmode" icon found on page
        if($('.runmode-forced').length == 0) {
            if(LiveDebugApp.cRunMode == 'eval'){
                $('#mnuRunModeEval').prepend('<img class="icon16 menuitem-icon runmode-user" src="img/icons/check.svg" />');
            }  
            else { // if(LiveDebugApp.cRunMode == 'file') {
                $('#mnuRunModeFile').prepend('<img class="icon16 menuitem-icon runmode-user" src="img/icons/check.svg" />');
                LiveDebugApp.cRunMode = 'file';
            }
        }
        else {
            $('#mnuRunModeEval').addClass('disabled');
            $('#mnuRunModeFile').addClass('disabled');
        }
        
        // -- clear Ace editor
        LiveDebugApp.clearDocument();
    },
    
    // -- window resize handler
    onResize: function(){
        $('#code').height($(window).height() - $('.navbar').height() - 60);
        $('#run_result').height($('#code').height());
        // exec Ace.resize handler
        LiveDebugApp.editor.resize();
    },
    
    // -- window.keyDown handler
    onKeyDown: function(e) {
        switch(e.which) {
            // -- Run
            case 115:   // -- F4
                LiveDebugApp.onMenuItemClick('mnuRun');
                e.preventDefault();
            break;
        }
    },
    
    // -- navbar menu item click handler
    onMenuItemClick: function(item /* @item = menu item id or menu item object */) {
        var id;
        if(typeof item == 'object') {
            id = $(item).attr('id');
        }
        else if (typeof item == 'string') {
            id = item;
        }
        
        switch(id) {
            // -- File > New
            case 'mnuFileNew':
                LiveDebugApp.clearDocument();
            break;
            // -- File > Open
            case 'mnuFileLoad':
                $('#btnLoadFile').show();
                $('#txtLoadFile').show();
                $('#btnSaveFile').hide();
                $('#txtSaveFile').hide();
                LiveDebugApp.reloadFileList();
                $('#modalFileList').modal('show');
                $('#inpFileName').focus().select();
            break;
            // -- File > Save
            case 'mnuFileSave':
                $('#btnLoadFile').hide();
                $('#txtLoadFile').hide();
                $('#btnSaveFile').show();
                $('#txtSaveFile').show();
                LiveDebugApp.reloadFileList();
                $('#modalFileList').modal('show');
                $('#inpFileName').focus().select();
            break;
            // -- Run code
            case 'mnuRun':
                var tmpform = $('<form>');
                    tmpform.attr('target', 'run_result');
                var inp_act = $('<input>');
                    inp_act.attr({type: 'hidden', name: 'act', value: 'run'}).appendTo(tmpform);
                var inp_code = $('<textarea style="display: none">');
                    inp_code.attr({name: 'code'}).val(LiveDebugApp.editor.getValue()).appendTo(tmpform);
                var inp_mode = $('<input>');
                    inp_mode.attr({type: 'hidden', name: 'mode', value: LiveDebugApp.cRunMode}).appendTo(tmpform);
                var inp_runid = $('<input>');
                    inp_runid.attr({type: 'hidden', name: 'runid', value: LiveDebugApp.cRunID}).appendTo(tmpform);
                tmpform.appendTo(document.body);
                tmpform.submit();
                tmpform.remove();
            break;
            // -- Run mode > Eval mode
            case 'mnuRunModeEval':
                if($('.runmode-forced').length == 0) {
                    LiveDebugApp.cRunMode = 'eval';
                    $('.runmode-user').remove();
                    $('#mnuRunModeEval').prepend('<img class="icon16 menuitem-icon runmode-user" src="img/icons/check.svg" />');
                }
            break;
            // -- Run mode > File mode
            case 'mnuRunModeFile':
                if($('.runmode-forced').length == 0) {
                    LiveDebugApp.cRunMode = 'file';
                    $('.runmode-user').remove();
                    $('#mnuRunModeFile').prepend('<img class="icon16 menuitem-icon runmode-user" src="img/icons/check.svg" />');
                }
            break;
            // -- View > Wide mode
            case 'mnuViewWide':
                $('#result_container').removeClass('col-sm-6').addClass('col-sm-12');
                $('#code_container').removeClass('col-sm-6').addClass('d-none');
            break;
            // -- View > Split mode
            case 'mnuViewSplit':
                $('#result_container').removeClass('col-sm-12').addClass('col-sm-6');
                $('#code_container').removeClass('d-none').addClass('col-sm-6');
            break;
        }
    },
    
    // -- clear Ace.editor and put base < ? p h p   construction
    clearDocument: function() {
        LiveDebugApp.editor.setValue('<'+'?php ');
        LiveDebugApp.editor.getSelection().clearSelection();
        LiveDebugApp.editor.focus();
        LiveDebugApp.setCurrentFileName('');
    },
    
    onFileSelect: function(e) {
        $('#inpFileName').val( $(e).val() );
    },
    
    btnLoadFileClick: function() {
        var fileName = $('#inpFileName').val();
        $.getJSON('?', {act: 'get_file_contents', filename: fileName}, function(json, status, jqxhr){
            if(json.status == 'ok'){
                var fileContent = json.data['content'];
                LiveDebugApp.editor.setValue(fileContent);
                LiveDebugApp.editor.getSelection().clearSelection();
                LiveDebugApp.editor.focus();
                LiveDebugApp.setCurrentFileName(fileName);
                
                $('#modalFileList').modal('hide');
            }
        });
    },
    
    btnSaveFileClick: function() {
        var fileName = $('#inpFileName').val();
        var content = LiveDebugApp.editor.getValue();
        $.postJSON('?', {act: 'set_file_contents', filename: fileName, content: content}, function(json, status, jqxhr) {
            if(json.status == 'ok'){
                LiveDebugApp.setCurrentFileName(json.data['filename']);
                $('#modalFileList').modal('hide');
            }
        });
    },
    
    reloadFileList: function() {
        
    },
    
    setCurrentFileName: function(filename) { LiveDebugApp.cFileName = filename; },
    getCurrentFileName: function() {return LiveDebugApp.cFileName; }
    
};

// -- call LiveDebugApp.init function when page loaded
$(LiveDebugApp.init);