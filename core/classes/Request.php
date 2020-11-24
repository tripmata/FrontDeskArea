<?php

    class Request implements IRequest
    {
        private $Params = Array();
        private $Type = "Unknown";

        public $URL = "";

		function __construct($path)
		{
			$this->URL = $path;
		}

        public function AddParameter($name_valuePair, $value = null)
        {
            // TODO: Implement AddParameter() method.

            if(is_a($name_valuePair, "NameValuePair"))
            {
                $this->Params[$name_valuePair->Name] = $name_valuePair->Value;
                return true;
            }
            else
            {
                if($value != null)
                {
                    $this->Params[$name_valuePair] = $value;
                    return true;
                }
            }
        }

        public function AddRange($name_valuePair_array)
        {
            // TODO: Implement AddRange() method.

            if(is_array($name_valuePair_array))
            {
                for($i = 0; $i < count($name_valuePair_array); $i++)
                {
                    if(is_a($name_valuePair_array[$i], "NameValuePair"))
                    {
                        $this->Params[$name_valuePair_array[$i]->Name] = $name_valuePair_array[$i]->Value;
                    }
                }
            }
        }

        /**
         * @return Response
         * @throws Exception
         */
        public function Execute()
        {
            // TODO: Implement Execute() method.

			try {
				$curl = curl_init();
				curl_setopt($curl, CURLOPT_URL, $this->URL);
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($this->Params));
				curl_setopt($curl, CURLOPT_POST, true);
				curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
                
                // get url
                $urlConfiguration = Configuration::url();

                // add storage and host
                curl_setopt($curl, CURLOPT_HTTPHEADER, [
                    'x-url-config:' . json_encode([
                        'storage' => $urlConfiguration->storage,
                        'host'    => $urlConfiguration->host
                    ])
                ]);

                // make request
				$resp = curl_exec($curl);
				curl_close($curl);

				if((is_array(json_decode($resp))) || (is_object(json_decode($resp))))
				{
					$ret = json_decode($resp);

					if((isset($ret->Content)) && (isset($ret->Type)))
					{
						$response = new Response($ret->Content, $ret->Type, 1, "JSON");
					}
					else
					{
						$response = new Response($ret, "UNKNOWN", 1, "JSON");
					}
				}
				else
				{
					$ret = $resp;
					$response = new Response($ret, "UNKNOWN", 1, "TEXT");
				}
				return $response;
			}
			catch (Exception $e)
			{
				throw new Exception("Request failed");
			}
        }


        public function GetURL()
        {
            // TODO: Implement GetDomain() method.
            return $this->Path;
        }


        private function BuildPath()
        {
            if($this->Domain != "")
            {
                $this->URL = strtolower(strpos(strtolower($this->Domain . $this->Path), "http://")
                === 0 ? $this->Domain . $this->Path : "http://" . $this->Domain . $this->Path);

                return true;
            }
            else
            {
                return false;
            }
        }
    }