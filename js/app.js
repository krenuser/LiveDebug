var App = {
    editor: null,
    cFileName: '',
    cRunMode: 'eval',       // file | eval
    cRunID: null,
    
    init: function() {
        App.editor = ace.edit('code');
        App.editor.getSession().setMode('ace/mode/php');
        
        $(window).on('resize', App.onResize);
        App.onResize();
        
        $(window).on('keydown', App.onKeyDown);
        
        $('.appMenuItem')
            .on('click', function(){App.onMenuItemClick(this); })
            .on('click', function(e){e.preventDefault(); });
        
        App.cRunID = parseInt(Math.random() * 100000);
        
        if($('.runmode-forced').length == 0){
            if(App.cRunMode == 'eval'){
                $('#mnuRunModeEval').prepend('<img class="icon16 menuitem-icon runmode-user" src="img/icons/check.svg" />');
            }
            if(App.cRunMode == 'file'){
                $('#mnuRunModeFile').prepend('<img class="icon16 menuitem-icon runmode-user" src="img/icons/check.svg" />');
            }
        }
        else {
            $('#mnuRunModeEval').addClass('disabled');
            $('#mnuRunModeFile').addClass('disabled');
        }
        
        App.clearDocument();
    },
    
    onResize: function(){
        $('#code').height($(window).height() - $('.navbar').height() - 30);
        $('#run_result').height($('#code').height());
        App.editor.resize();
    },
    
    onKeyDown: function(e) {
        switch(e.which) {
            case 115:
                App.onMenuItemClick('mnuRun');
                e.preventDefault();
            break;
        }
    },
    
    onMenuItemClick: function(item) {
        var id;
        if(typeof item == 'object'){
            id = $(item).attr('id');
        }
        else if (typeof item == 'string'){
            id = item;
        }
        
        switch(id) {
            case 'mnuFileNew':
                App.clearDocument();
            break;
            case 'mnuFileOpen':
                
            break;
            case 'mnuRun':
                var tmpform = $('<form>');
                    tmpform.attr('target', 'run_result');
                var inp_act = $('<input>');
                    inp_act.attr({type: 'hidden', name: 'act', value: 'run'}).appendTo(tmpform);
                var inp_code = $('<textarea style="display: none">');
                    inp_code.attr({name: 'code'}).val(App.editor.getValue()).appendTo(tmpform);
                var inp_mode = $('<input>');
                    inp_mode.attr({type: 'hidden', name: 'mode', value: App.cRunMode}).appendTo(tmpform);
                var inp_runid = $('<input>');
                    inp_runid.attr({type: 'hidden', name: 'runid', value: App.cRunID}).appendTo(tmpform);
                tmpform.appendTo(document.body);
                tmpform.submit();
                tmpform.remove();
            break;
            case 'mnuRunModeEval':
                if($('.runmode-forced').length == 0) {
                    App.cRunMode = 'eval';
                    $('.runmode-user').remove();
                    $('#mnuRunModeEval').prepend('<img class="icon16 menuitem-icon runmode-user" src="img/icons/check.svg" />');
                }
            break;
            case 'mnuRunModeFile':
                if($('.runmode-forced').length == 0) {
                    App.cRunMode = 'file';
                    $('.runmode-user').remove();
                    $('#mnuRunModeFile').prepend('<img class="icon16 menuitem-icon runmode-user" src="img/icons/check.svg" />');
                }
            break;
            
        }
    },
    
    clearDocument: function() {
        App.editor.setValue('<'+'?php ');
        App.editor.getSelection().clearSelection();
        App.editor.focus();
        App.cFileName = '';
    }
    
};

$(App.init);