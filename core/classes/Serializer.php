<?php

    class Serializer
    {
        public static function SerializeRequest()
        {
            $ks = array_keys($_REQUEST);

            $nvp = Array();

            for($i = 0; $i < count($ks); $i++)
            {
                $v = $_REQUEST[$ks[$i]];
                $n = $ks[$i];

                $nvp[$i] = new NameValuePair();
                $nvp[$i]->Name = $n;
                $nvp[$i]->Value = $v;
            }
            return $nvp;
        }
		
		
		public static function SerializeSession()
        {
            $ks = array_keys($_SESSION);

            $nvp = Array();

            for($i = 0; $i < count($ks); $i++)
            {
                $v = $_SESSION[$ks[$i]];
                $n = $ks[$i];

                $nvp[$i] = new NameValuePair();
                $nvp[$i]->Name = $n;
                $nvp[$i]->Value = $v;
            }
            return $nvp;
        }
		
		public static function GetKey($pref="")
        {
            $path = $pref."key.json";
            $count = 0;

            while(($count < 10))
            {
                if(!file_exists($path))
                {
                    $path = "../".$path;
                }
                else
                {
                    break;
                }
                $count++;
            }

            try{

                $file = fopen($path, "r");
                $content = fread($file, filesize($path));
                fclose($file);
                if(strpos($content, "{") === 0)
                {
                    $ret = json_decode($content);
                    return $ret->UserKey;
                }
                else
                {
                    return false;
                }
            }
            catch(Exception $e)
            {
                return false;
            }
        }
    }
	
	