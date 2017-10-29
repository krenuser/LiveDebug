<?php header('Content-Type: text/html; charset=utf-8');

require 'lib/config.inc';
require 'lib/ClassLoader.inc';

$ld = new LD();
$ld->processRequest($_REQUEST);

?><!DOCTYPE html>
<html class="h-100">
    <head>
        <title><?=$ld->getTranslatedCaption('txtAppTitle')?></title>
        <script src="js/jquery.min.js"></script>
        <script src="js/Popper.min.js"></script>
        <script src="js/bootstrap.min.js"></script>
        <script src="js/ace.js"></script>
        <script src="js/ace.mode-php.js"></script>
        <script src="js/app.js"></script>
        <link rel="shortcut icon" href="img/icons/inbox.svg" />
        <style>
            .icon16 {width: 16px; margin-top: -3px; fill: red; }
            .icon24 {width: 24px; margin-top: -3px; fill: red; }
            .icon32 {width: 32px; margin-top: -3px; fill: red; }
            .menuitem-icon {margin-left: -19px; margin-top: 5px; position: fixed; }
        </style>
        <link rel="stylesheet" href="css/bootstrap.css" />
    </head>
    <body class="h-100">
        <nav class="navbar navbar-expand-sm navbar-light fixed-top bg-light" style="background-color: #77dd77 !important">
            <a class="navbar-brand" href="."><?=$ld->getTranslatedCaption('txtNavbarTitle')?></a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapsePan" aria-controls="navbarCollapsePan" aria-expanded="false" aria-label="<?=$ld->getTranslatedCaption('txtToggleMenu')?>">
                <span class="navbar-toggler-icon"></span>
            </button>            
            <div class="collapse navbar-collapse" id="navbarCollapsePan">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item dropdown d-none">
                        <a class="nav-link dropdown-toggle" href="#" onclick="return false" id="navbarFileDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><img class="icon16" src="img/icons/menu.svg" /> <?=$ld->getTranslatedCaption('mnuFile')?></a>
                        <div class="dropdown-menu" aria-labelledby="navbarFileDropdown">
                            <a id="mnuFileNew" class="dropdown-item appMenuItem" href="#">
                                <img class="icon16 menuitem-icon" src="img/icons/new-message.svg" />
                                <?=$ld->getTranslatedCaption('mnuFileNew')?>
                            </a>
                            <a id="mnuFileLoad" data-toggle="modal" data-target="#modalFileList" class="dropdown-item appMenuItem" href="#">
                                <img class="icon16 menuitem-icon" src="img/icons/folder.svg" />
                                <?=$ld->getTranslatedCaption('mnuFileLoad')?>
                            </a>
                            <a id="mnuFileSave" class="dropdown-item appMenuItem" href="#">
                                <img class="icon16 menuitem-icon" src="img/icons/save.svg" />
                                <?=$ld->getTranslatedCaption('mnuFileSave')?>
                            </a>
                        </div>                    
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" onclick="return false" id="navbarRunModeDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <img class="icon16" src="img/icons/controller-play.svg" />
                            <?=$ld->getTranslatedCaption('mnuRunMode')?>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarRunModeDropdown">
                            <a id="mnuRunModeEval" class="dropdown-item appMenuItem" href="#">
                                <?php if(defined('RUN_MODE') && RUN_MODE == 'eval') { ?><img class="icon16 menuitem-icon runmode-forced" src="img/icons/warning.svg" /><?php } ?> 
                                <?=$ld->getTranslatedCaption('mnuRunModeEval')?>
                            </a>
                            <a id="mnuRunModeFile" class="dropdown-item appMenuItem" href="#">
                                <?php if(defined('RUN_MODE') && RUN_MODE == 'file') { ?><img class="icon16 menuitem-icon runmode-forced" src="img/icons/warning.svg" /><?php } ?> 
                                <?=$ld->getTranslatedCaption('mnuRunModeFile')?>
                            </a>
                        </div>                    
                    </li>
                    <li class="nav-item dropdown d-none">
                        <a class="nav-link dropdown-toggle" href="#" onclick="return false" id="navbarTemplateDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <img class="icon16" src="img/icons/documents.svg" />
                            <?=$ld->getTranslatedCaption('mnuTemplate')?>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarTemplateDropdown">
                            <a id="mnuTemplateAdd" class="dropdown-item appMenuItem" href="#">
                                <img class="icon16 menuitem-icon" src="img/icons/plus.svg" />
                                <?=$ld->getTranslatedCaption('mnuTemplateAdd')?>
                            </a>
                            <div class="dropdown-divider"></div>
                        </div>                    
                    </li>
                    <li class="nav-item"><a id="mnuRun" title="Выполнить код (F4)" class="nav-link appMenuItem" href="#"><strong><?=$ld->getTranslatedCaption('mnuRun')?></strong></a></li>
                </ul>
            </div>
        </nav>
        <div class="container h-100" style="padding-top: 80px; padding-bottom: 20px">
            <div class="row h-100">
                <div class="col-sm-6 h-100" style="border: 1px solid #777; padding-left: 0px">
                    <div id="code" class="w-100 h-100"></div>
                </div>
                <div class="col-sm-6 h-100" style="padding: 0px">
                    <iframe id="run_result" name="run_result" src="about:blank" class="w-100" style="border: 1px solid #777"></iframe>
                </div>
            </div>
        </div>
        
        
        <div id="modalFileList" class="modal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><?=$ld->getTranslatedCaption('txtFileList')?></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="<?=$ld->getTranslatedCaption('txtClose')?>">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <select class="w-100" size="10" id="select_file"></select>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary"><?=$ld->getTranslatedCaption('txtSave')?></button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal"><?=$ld->getTranslatedCaption('txtClose')?></button>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>