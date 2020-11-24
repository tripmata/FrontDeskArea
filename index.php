<?php
	session_start();

	// load autoloader
	require_once ("core/autoloader.php");
	
	// load configuration
	require_once "config.php";

    $router = new Router();
	$path = "";
	$request = null;

	// get url configuration
	$urlConfiguration = Configuration::url();

	if (isset($_SERVER['PATH_INFO'])) 
	{
		$path = $_SERVER['PATH_INFO'];
	}

	if (strtolower(trim($router->Page)) == "logout") 
	{
		session_destroy();
		echo "{\"status\":\"success\"}";
		exit();
	}

	if (strtolower(trim($router->Page)) == "mail") {

        if ((isset($_REQUEST['userkey'])) && (isset($_REQUEST['intent'])))
		{
            if ($_REQUEST['userkey'] == Serializer::GetKey()) 
			{
                if ($_REQUEST['intent'] == "send-mail") 
				{
					$mail = Mail::MapRequest();
					
                    if ($mail !== null) {echo Mail::Send($mail);}
                }
                else 
				{
                    echo "{\"status\":\"available\"}";
                }
            }
            else 
			{
                echo "{\"status\":\"failed\",\"message\":\"invalid-user-key\"}";
            }
        }
        else 
		{
            echo "{\"status\":\"failed\",\"message\":\"invalid-parameters-passed\"}";
		}
		
        exit();
	}
	
	if(count($router->Args) > 0) 
	{
		if($router->Args[(count($router->Args) - 1)] == "logout") 
		{
			session_destroy();
			echo "{\"status\":\"success\"}";
			exit();
		}

		if (($router->Args[(count($router->Args) - 1)] == "worker") || ($router->Args[(count($router->Args) - 1)] == "worker.php")) 
		{
			$request = new Request($urlConfiguration->worker);
		}
		elseif($router->Args[(count($router->Args) - 1)] == "upload") 
		{
			$upload = new Upload($_FILES['file']);
			
			$newN = md5(mt_rand(1000,9999).mt_rand(1000,9999).mt_rand(1000,9999).mt_rand(1000,9999));
			
			while (file_exists("files/".$newN.".".$upload->Extension)) {
				$newN = md5(mt_rand(1000,9999).mt_rand(1000,9999).mt_rand(1000,9999).mt_rand(1000,9999));
			}

			$upload->Save("files", $newN.".".$upload->Extension);
			
			$ret = new stdClass();
			$ret->Status = "success";
			$ret->Data = $newN.".".$upload->Extension;
			
			echo json_encode($ret);
			exit();
		}
		else 
		{
			$request = new Request($urlConfiguration->page);
		}
	}
	else
	{
		$request = new Request($urlConfiguration->page);
	}
	
	try
	{
		$request->AddParameter("path", $path);
		$request->AddParameter("key", Serializer::GetKey());
		$request->AddRange(Serializer::SerializeRequest());
		$request->AddRange(Serializer::SerializeSession());
		$response = $request->Execute();
		
		if($response->GetFormat() == "text") 
		{
			echo $response->Content;
		}
		else 
		{
			if($response->Type == "page") 
			{
				print_r($response->Content);
			}
			else if($response->Type == "set") 
			{
				if($response->Content->Data->setMethod == "session") 
				{
					$_SESSION[$response->Content->Data->setName] = $response->Content->Data->setValue;
				}
				else if($response->Content->Data->setMethod == "cookie") 
				{
					$_COOKIE[$response->Content->Data->setName] = $response->Content->Data->setValue;
					setcookie($response->Content->Data->setName, $response->Content->Data->setValue, (3600 * 50), "..");
				}
				$response->Content->Data->setName = "";
				$response->Content->Data->setValue = "";
				echo json_encode($response->Content);
			}
			else 
			{
				echo json_encode($response->Content);
			}
		}
	}
	catch (Exception $e)
	{
		echo $e->getMessage();
	}