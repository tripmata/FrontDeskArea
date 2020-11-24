<?php

   require_once ("Interfaces/IBase.php");
   require_once ("Interfaces/IRequest.php");
   require_once ("Interfaces/IResponse.php");

   require_once ("classes/Serializer.php");
   require_once ("classes/NameValuePair.php");
   require_once ("classes/Response.php");
   require_once ("classes/Request.php");
   require_once ("classes/Router.php");
   require_once ("classes/Upload.php");
   require_once ("classes/Mail.php");

    //Load PHP mailer
    require_once ("phpmailer/src/PHPMailer.php");
    require_once ("phpmailer/src/Exception.php");
    require_once ("phpmailer/src/SMTP.php");