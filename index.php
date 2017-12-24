<?php require 'lib/config.php';

// -- run script command received 
if($_REQUEST['livedebug_cmd'] == 'run') {
    $script = $_REQUEST['livedebug_script'];
    include LIVEDEBUG_TMP_RUN_PATH.$script;
    unlink(LIVEDEBUG_TMP_RUN_PATH.$script);
    die;
}

require 'lib/ClassLoader.php';

$ld = new LD();
$ld->processRequest($_REQUEST);

$fs = $ld->getFSInstance();     // File Storage instance

header('Content-Type: text/html; charset=utf-8');
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
        <link rel="stylesheet" href="css/bootstrap.css" />
        <link rel="stylesheet" href="css/app.css" />
    </head>
    <body class="h-100">
        <nav class="navbar navbar-expand-sm navbar-light fixed-top bg-light" style="background-color: #77dd77 !important">
            <a class="navbar-brand" href="."><?=$ld->getTranslatedCaption('txtNavbarTitle')?></a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapsePan" aria-controls="navbarCollapsePan" aria-expanded="false" aria-label="<?=$ld->getTranslatedCaption('txtToggleMenu')?>">
                <span class="navbar-toggler-icon"></span>
            </button>            
            <div class="collapse navbar-collapse" id="navbarCollapsePan">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" onclick="return false" id="navbarFileDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><img class="icon16" src="img/icons/menu.svg" /> <?=$ld->getTranslatedCaption('mnuFile')?></a>
                        <div class="dropdown-menu" aria-labelledby="navbarFileDropdown">
                            <a id="mnuFileNew" class="dropdown-item appMenuItem" onclick="" href="#">
                                <img class="icon16 menuitem-icon" src="img/icons/new-message.svg" />
                                <?=$ld->getTranslatedCaption('mnuFileNew')?>
                            </a>
                            <a id="mnuFileLoad" class="dropdown-item appMenuItem" href="#">
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
                                <?php if(LIVEDEBUG_RUN_MODE == 'eval') { ?><img class="icon16 menuitem-icon runmode-forced" src="img/icons/pin.svg" /><?php } ?> 
                                <?=$ld->getTranslatedCaption('mnuRunModeEval')?>
                            </a>
                            <a id="mnuRunModeFile" class="dropdown-item appMenuItem" href="#">
                                <?php if(LIVEDEBUG_RUN_MODE == 'file') { ?><img class="icon16 menuitem-icon runmode-forced" src="img/icons/pin.svg" /><?php } ?> 
                                <?=$ld->getTranslatedCaption('mnuRunModeFile')?>
                            </a>
                        </div>                    
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" onclick="return false" id="navbarViewDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <img class="icon16" src="img/icons/eye.svg" />
                            <?=$ld->getTranslatedCaption('mnuView')?>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarViewDropdown">
                            <a id="mnuViewWideCode" class="dropdown-item appMenuItem" href="#">
                                <img class="icon16 menuitem-icon" src="img/icons/code.svg" />
                                <?=$ld->getTranslatedCaption('mnuViewWideCode')?>
                            </a>
                            <a id="mnuViewSplit" class="dropdown-item appMenuItem" href="#">
                                <img class="icon16 menuitem-icon" src="img/icons/copy.svg" />
                                <?=$ld->getTranslatedCaption('mnuViewSplit')?>
                            </a>
                            <a id="mnuViewWide" class="dropdown-item appMenuItem" href="#">
                                <img class="icon16 menuitem-icon" src="img/icons/document-landscape.svg" />
                                <?=$ld->getTranslatedCaption('mnuViewWide')?>
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
                    <li class="nav-item"><a id="mnuRun" title="<?=$ld->getTranslatedCaption('txtRunCodeHint') ?>" class="nav-link appMenuItem" href="#"><strong><?=$ld->getTranslatedCaption('mnuRun')?></strong></a></li>
                </ul>
            </div>
        </nav>
        
        <div class="container h-100" style="padding-top: 55px; padding-bottom: 20px">
            <div class="row h-100">
                <div id="code_container" class="col-sm-6 h-100" style="padding-left: 0px">
                    <div><?=$ld->getTranslatedCaption('txtPHPCode')?></div>
                    <div id="code" class="w-100" style="border: 1px solid #777"></div>
                </div>
                <div id="result_container" class="col-sm-6 h-100" style="padding: 0px">
                    <div><?=$ld->getTranslatedCaption('txtResult')?></div>
                    <iframe id="run_result" name="run_result" src="about:blank" class="w-100" style="border: 1px solid #777"></iframe>
                </div>
            </div>
        </div>
        
        
        <div id="modalFileList" class="modal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 id="txtSaveFile" class="modal-title"><?=$ld->getTranslatedCaption('txtSaveFile')?></h5>
                        <h5 id="txtLoadFile" class="modal-title"><?=$ld->getTranslatedCaption('txtLoadFile')?></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="<?=$ld->getTranslatedCaption('txtClose')?>">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>
                        <select class="form-control w-100" size="10" id="select_file" onclick="LiveDebugApp.onFileSelect(this)">
                            <?php
                                $fl = $fs->getFileList($ld->getUserID());
                                foreach($fl as $fileitem) {
                                    ?>
                                    <option value="<?=$fileitem->getScope().'/'.$fileitem->getName()?>"><?=$fileitem->getScope().'/'.$fileitem->getName()?></option>
                                    <?php
                                }
                            ?>
                        </select>
                        </p>
                        <p>
                            <input type="text" class="form-control w-100" name="filename" id="inpFileName" />
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" id="btnLoadFile" onclick="LiveDebugApp.btnLoadFileClick()" class="btn btn-primary"><?=$ld->getTranslatedCaption('txtLoad')?></button>
                        <button type="button" id="btnSaveFile" onclick="LiveDebugApp.btnSaveFileClick()" class="btn btn-primary"><?=$ld->getTranslatedCaption('txtSave')?></button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal"><?=$ld->getTranslatedCaption('txtClose')?></button>
                    </div>
                </div>
            </div>
        </div>
    </body>
    <!-- JQuery v3.2.1 used.    See "http://jquery.com/" for more details.          -->
    <!-- BootStrap v4 used.     See "https://getbootstrap.com/" for more details.   -->
    <!-- Entypo iconset used.   See "http://www.entypo.com/" for more details.      -->
</html>