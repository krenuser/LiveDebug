<?php

interface IFileStore {
    
    public function getFileList($user_id);
    public function getFileContents($user_id, $filename);
    public function setFileContents($user_id, $filename, $content);
    
}

class FileItem {
    private $name;
    private $scope;
    private $size;
    
    /** getName() **/
    /** getScope() **/
    /** getSize() **/
    
    /** setName() **/
    /** setScope() **/
    /** setSize() **/
    
    public function __construct($name, $scope, $size) {
        $this->name = $name;
        $this->scope = $scope;
        $this->size = $size;
    }
    
    public function getName(){ return $this->name; }
    public function setName($value){ $this->name = $value; }

    public function getScope(){ return $this->scope; }
    public function setScope($value){ $this->scope = $value; }

    public function getSize(){ return $this->size; }
    public function setSize($value){ $this->size = $value; }
    
}