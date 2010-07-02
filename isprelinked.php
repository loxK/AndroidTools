#!/usr/bin/php
<?php
# Copyright (C) 2010 Laurent Dinclaux <lox.dev at knc.nc>
#
# Licensed under the Apache License, Version 2.0 (the "License");
# you may not use this file except in compliance with the License.
# You may obtain a copy of the License at
#
#      http://www.apache.org/licenses/LICENSE-2.0
#
# Unless required by applicable law or agreed to in writing, software
# distributed under the License is distributed on an "AS IS" BASIS,
# WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
# See the License for the specific language governing permissions and
# limitations under the License.

umask(0);

$scriptPath = dirname(realpath($argv[0]));

$opt = $GLOBALS['argv'];

if(!isset($opt[1])) error('Please specify a rom directory');

$dir = $opt[1];
if(!is_dir($dir)) error('Please specify a valid rom directory');
if(!is_readable($dir)) error('Please verify permission on specified directory');

$ite=new RecursiveDirectoryIterator( realpath($dir) );

foreach (new RecursiveIteratorIterator($ite) as $filename=>$cur) {

    if( !preg_match('/\.so$/is', $filename) ) continue;
    
    $retval = shell_exec('isprelinked ' . realpath($filename));
    if( preg_match('/not prelinked$/is', $retval) ) continue;
      
    preg_match('/\: (.*)$/is', $retval, $regs);
    
    echo str_pad( basename($filename), 30) . "\t\t" . strtoupper(trim($regs[1])) ;
    
    $filesize=$cur->getSize();
    
    echo " # ". formatBytes($filesize);
    
    echo "\n";
}


function formatBytes($b,$p = null) {
    /**
     * 
     * @author Martin Sweeny
     * @version 2010.0617
     * 
     * returns formatted number of bytes. 
     * two parameters: the bytes and the precision (optional).
     * if no precision is set, function will determine clean
     * result automatically.
     * 
     **/
    $units = array("B","kB","MB","GB","TB","PB","EB","ZB","YB");
    $c=0;
    if(!$p && $p !== 0) {
        foreach($units as $k => $u) {
            if(($b / pow(1024,$k)) >= 1) {
                $r["bytes"] = $b / pow(1024,$k);
                $r["units"] = $u;
                $c++;
            }
        }
        return number_format($r["bytes"],2) . " " . $r["units"];
    } else {
        return number_format($b / pow(1024,$p)) . " " . $units[$p];
    }
}

?>
