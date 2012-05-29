<?php

/*Copyright (c) 2012 Aditya Parab, http://neersys.com/

Permission is hereby granted, free of charge, to any person obtaining
a copy of this software and associated documentation files (the
"Software"), to deal in the Software without restriction, including
without limitation the rights to use, copy, modify, merge, publish,
distribute, sublicense, and/or sell copies of the Software, and to
permit persons to whom the Software is furnished to do so, subject to
the following conditions:

The above copyright notice and this permission notice shall be
included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.*/

generateNameSpaceCode('localhost', 'root', 'iamtheboss', 'saywtf');

function generateNameSpaceCode($host="localhost",$uname="root",$upass="",$databaseName=""){
    $phpCode="<?php
    namespace $databaseName{";
    $conn = mysql_connect($host,$uname,$upass);
    if($conn){
        $db=mysql_select_db($databaseName);
        if($db){
            $query = "SHOW TABLES";
            $result = mysql_query($query);
            while($row=  mysql_fetch_array($result)){
                $columnTypes = '';
                $columnNames = '';
                $tableName = $row[0];
                $phpCode.="

        class $tableName {";
                $phpCode.='
            public static $tableName = "'.$tableName.'";';
                $innerQuery="DESC $tableName";
                $innerResult = mysql_query($innerQuery);
                $i=0;
                $parameterList="";
                while ($innerRow = mysql_fetch_array($innerResult)){

                    $variableName = "c$i".$innerRow[0]."_";

                    $variable = $innerRow[0]; // column name
                    $dataType = $innerRow[1]; // data type
                    $PKStatus = $innerRow[3]; // is Primary key?
                    $extras = $innerRow[5]; // is auto_increment?

                    $pattern1="/\(/";
                    $pattern2="/\)/";
                    $replacement="";
                    
                    $columnNames.="$variable,";
                    
                    $t1 = preg_replace($pattern1, $replacement, $dataType);
                    $t2 = preg_replace($pattern2, $replacement, $t1);

                    $variableName.=$t2;

                    if($PKStatus === "PRI"){
                        $variableName.="_PK";
                    }
                    if($extras == "auto_increment"){
                        $variableName.="_AI";
                    }
                    $phpCode.= "
            public static $".$variableName."= \"$variable\";";
                    //$parameterList.='$'.$variable.',';

                    $pattern = "/INT/i";

                    if(!preg_match($pattern, $dataType)){
                        $columnTypes.='1,';
                        $parameterList.="$".$variable.'_VCH,';
                        
                    } else {
                        $columnTypes.='0,';
                        $parameterList.="$".$variable.'_INT,';
                    }
                    $i++;
                }
                $count = $i;

                $parameterList = substr($parameterList, 0,  strlen($parameterList)-1);
                $columnTypes = substr($columnTypes, 0,  strlen($columnTypes)-1);
                $columnNames = substr($columnNames, 0,  strlen($columnNames)-1);
                $columnNames = explode(',', $columnNames);
                $columnTypes = explode(',', $columnTypes);
                $phpCode.='
            public static $columNames = array(';
                for($i=0;$i<$count;$i++){
                    $phpCode.='
                "'.$columnNames[$i].'"=>'.$columnTypes[$i].',';
                }
                
                $phpCode.="
            );";
                $phpCode.='
            public static $colums = array(';
                for($i=0;$i<$count;$i++){
                    $phpCode.='
                '.$columnTypes[$i].',';
                }
                
                $phpCode.="
            );";
                $phpCode.='

            public static function columnStructure('.$parameterList.'){
                $num = func_num_args();
                $params = func_get_args();

                $returnValue = "";
                for($i=0;$i<$num;$i++){
                    if(self::$colums[$i]===1) {
                        $returnValue.="\'".$params[$i]."\',";
                    } else{
                        $returnValue.=$params[$i].",";
                    }
                    //$returnValue.=$params[$i].",";
                }
                $returnValue=substr($returnValue, 0,strlen($returnValue)-1);
                return $returnValue;
            }';

                $phpCode.='
        }';
            }
            $phpCode.="
    }";
            $fp=fopen("tables.php","wb+");
            $success = fwrite($fp,$phpCode);
            //echo "__| ".$success." |__<br/>";
            system('chmod 777 /var/www/queryOperations/tables.php');
            fclose($fp);
            if($success){
                return 1;
            } else {
                return 0;
            }
            
        } else {
            //database not found
            return 0;
        }
    } else {
        return 0;
        //can't connect to the database
    }
    return 0;
    //That is all :)
}