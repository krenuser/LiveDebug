<?php

class FileStore_FS implements IFileStore {
    
    public function getFileList($user_id) {
        $fl = array();
        
        // processing user file list
        $fltmp = array();
        if(!is_null($user_id) && !stripos($user_id, '..') && is_dir(LIVEDEBUG_FILES_PATH.'/'.$user_id)) {
            $fltmp = glob(LIVEDEBUG_FILES_PATH.'/'.$user_id.'/*');
                
            sort($fltmp);
            
            foreach($fltmp as $item) {
                $fl[] = new FileItem( basename($item), 'user', filesize($item) );
            }
        }
        
        // processing public file list
        $fltmp = array();
        if(is_dir(LIVEDEBUG_FILES_PATH.'/public')) {
            $fltmp = glob(LIVEDEBUG_FILES_PATH.'/public/*');
            
            sort($fltmp);
            
            foreach($fltmp as $item) {
                $fl[] = new FileItem( basename($item), 'public', filesize($item) );
            }
        }
        
        return $fl;
    }
    
    public function getFileContents($user_id, $filename) {
        if(is_null($user_id)){
            if(!stripos($filename, '../'))
                if(file_exists(LIVEDEBUG_FILES_PATH.'/public/'.$filename)) {
                    return file_get_contents(LIVEDEBUG_FILES_PATH.'/public/'.$filename);
                }
            else 
                return false;
        }
        else {
            if(!stripos($user_id, '..') && !stripos($filename, '../'))
                if(file_exists(LIVEDEBUG_FILES_PATH.'/'.$user_id.'/'.$filename)) {
                    return file_get_contents(LIVEDEBUG_FILES_PATH.'/'.$user_id.'/'.$filename);
                }
            return false;
        }
    }
    
    public function setFileContents($user_id, $filename, $content) {
        if(is_null($user_id)) {
            if(!stripos($filename, '../')){
                if(is_dir(LIVEDEBUG_FILES_PATH.'/public/')){
                    @mkdir(LIVEDEBUG_FILES_PATH);
                    @mkdir(LIVEDEBUG_FILES_PATH.'/public/');
                }
                
                return file_put_contents(LIVEDEBUG_FILES_PATH.'/public/'.$filename, $content) 
                    || strlen($content) == 0;       // return true even if empty file created successfully
            }
            else 
                return false;
        }
        else {
            if(!stripos($user_id, '..') && !stripos($filename, '../')) {
                if(!is_dir(LIVEDEBUG_FILES_PATH.'/'.$user_id)){
                    @mkdir(LIVEDEBUG_FILES_PATH);
                    @mkdir(LIVEDEBUG_FILES_PATH.'/'.$user_id);
                }

                return file_put_contents(LIVEDEBUG_FILES_PATH.'/'.$user_id.'/'.$filename, $content) 
                    || strlen($content) == 0;       // return true even if empty file created successfully
            }
            else 
                return false;
        }
    }
    

    
}