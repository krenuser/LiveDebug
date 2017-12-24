<?php

class LD {
    private $is_translation_loaded = false;
    private $translation_array = array();
    private $flclass;
    private $fs;
    private $user_id = null;
    
    public function __construct() {
        $this->fsclass = "FileStore_".LIVEDEBUG_FILESTORAGE_TYPE;
        $this->fs = new $this->fsclass();
        $this->loadTranslation();
    }
    
    private function loadTranslation() {
        $this->translation_array = parse_ini_file(LIVEDEBUG_LANG_INI_FILE);
        $this->is_translation_loaded = true;
    }

    public function getUserID() { return $this->user_id; }
    public function setUserID($value) { $this->user_id = $value; }

    /**
     * Get File Storage class name
    */
    public function getFSClass() {
        return $this->fsclass;
    }
    
    /**
     * Get File Storage instance
    */
    public function getFSInstance() {
        return $this->fs;
    }
    
    /**
     * Get caption by DOM element id
    */
    public function getTranslatedCaption($id) {
        if(!$this->is_translation_loaded) {
            $this->loadTranslation();
        }
        return $this->translation_array[$id] ? $this->translation_array[$id] : $id;
    }
    
    /**
     * Process request (most likely, HTTP GET/POST)
    */
    public function processRequest($request) {
        switch($request['act']) {
            // -- save code to PHP file and redirect to it / eval immediately
            case 'run':
                // -- eval mode (forced by config.ini or passed as HTTP request param)
                if( LIVEDEBUG_RUN_MODE == 'eval' || (LIVEDEBUG_RUN_MODE == 'user' && $request['mode'] == 'eval') ) {
                    if(substr($request['code'], 0, 5) != '<?php') {
                        $request['code'] = '?>'.$request['code'];
                    }
                    eval(preg_replace('/^<\\?php/i', '', $request['code']));
                    die;
                }
                // -- php file mode (forced by config.ini or passed as HTTP request param)
                if( LIVEDEBUG_RUN_MODE == 'file' || (LIVEDEBUG_RUN_MODE == 'user' && $request['mode'] == 'file') ) {
                    // random 6-digit value to avoid overwriting of script file by multiple LiveDebug tabs in one browser 
                    $runid = floatval($request['runid']);
                    $filename = $_SERVER['REMOTE_ADDR'].'_'.date('Y-m-d-h-i').'_'.$runid.'.php';
                    
                    // writing code to script file
                    file_put_contents(LIVEDEBUG_TMP_RUN_PATH.$filename, $request['code']);
                    
                    // redirecting
                    header("Location: ?livedebug_cmd=run&livedebug_script=".$filename, true);
                    die;
                }
            break;
            
            case 'get_file_list':
                $fl = $this->getFSInstance()->getFileList($this->getUserID());
                $rs = array();
                foreach( $fl as $fileitem ) {
                    $rs[] = array(
                        'name'  => $fileitem->getName(),
                        'scope' => $fileitem->getScope(),
                        'size'  => $fileitem->getSize(),
                    );
                }
                echo json_encode($rs);
                die;
            break;
            
            case 'get_file_contents':
                $user_id    = $this->getUserID();
                $filename   = $request['filename'];
                $s = explode('/', $filename);
                
                if($s[0] == 'public') {
                    $user_id = null;
                }
                $filename = $s[1];
                if($content = $this->getFSInstance()->getFileContents($user_id, $filename)){
                    die(json_encode(array('status'=>'ok', 'message'=>'ok', 'data'=>array('content'=>$content))));
                }
                else {
                    die(json_encode(array('status'=>'error', 'message'=>'Can\'t open file', 'data'=>array('content'=>''))));
                }
            break;
            
            case 'set_file_contents':
                $user_id    = $this->getUserID();
                $filename   = $request['filename'];
                $content    = $request['content'];
                
                $s = explode('/', $filename);
                if($s[0] == 'public') {
                    $user_id = null;
                }
                $filename = $s[count($s) - 1];
                
                if($this->getFSInstance()->setFileContents($user_id, $filename, $content)) {
                    die(json_encode(array('status'=>'ok', 'message'=>'File saved', 'data'=>array('filename'=>(is_null($user_id) ? 'public/' : 'user/').$filename))));
                }
                else {
                    die(json_encode(array('status'=>'error', 'message'=>'Can\'t save file', 'data'=>array())));
                }
            break;
            
            default:
                // when opening main page
                return;
            break;
        }
        die();
    }
}