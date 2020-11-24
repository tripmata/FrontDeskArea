<?php

    class Router
    {
        public $Page = "";
        public $Args = Array();

        public $ErrorPage = true;
        public $ErrorPagePath = "";

        private $paths = Array();
        private $routes = Array();

        private $redirects = Array();
        private $redirectPath = Array();

        private $home = "";

        function __construct($path=null)
        {
            if($path !== null)
            {
                $ds = explode("/", trim(trim($path), "/"));

                for ($i = 0; $i < count($ds); $i++)
                {
                    if ($i == 0)
                    {
                        $this->Page = $ds[0];
                    }
                    else
                    {
                        $this->Args[count($this->Args)] = $ds[$i];
                    }
                }
            }
            else
            {
                if((isset($_SERVER['PATH_INFO'])) || (isset($_SERVER['ORIG_PATH_INFO'])))
                {
                    $ds = isset($_SERVER['ORIG_PATH_INFO']) ? explode("/", trim($_SERVER['ORIG_PATH_INFO'], "/")) : explode("/", trim($_SERVER['PATH_INFO'], "/"));

                    for ($i = 0; $i < count($ds); $i++)
                    {
                        if ($i == 0)
                        {
                            $this->Page = $ds[0];
                        }
                        else
                        {
                            $this->Args[count($this->Args)] = $ds[$i];
                        }
                    }
                }
            }
        }

        public function AddHome($home)
        {
            $this->home = $home;
        }

        public function AddRoute($route, $path)
        {
            $this->paths[count($this->paths)] = $path;
            $this->routes[count($this->routes)] = $route;
        }

        public function Redirect($page, $to)
        {
            $this->redirects[count($this->redirects)] = $page;
            $this->redirectPath[count($this->redirectPath)] = $to;
        }

        public static function ResolvePath($path, $pathInfo=null)
        {
            $ret = $path;
            $prepend = "";
            $extra = false;

            if($pathInfo === null)
            {
                if((isset($_SERVER['PATH_INFO'])) ||(isset($_SERVER['ORIG_PATH_INFO'])))
                {
                    $tmp = isset($_REQUEST['ORIG_PATH_INFO']) ? explode("/", trim($_SERVER['ORIG_PATH_INFO'])) : explode("/", trim($_SERVER['PATH_INFO']));

                    if($tmp[(count($tmp) - 1)] == "")
                    {
                        $extra = true;
                    }

                    $ds = isset($_SERVER['ORIG_PATH_INFO']) ? explode("/", trim($_SERVER['ORIG_PATH_INFO']), "/") : explode("/", trim($_SERVER['PATH_INFO'], "/"));
                    for($i = 0; $i < (count($ds) - 1); $i++)
                    {
                        $prepend .= "../";
                    }
                }
            }
            else
            {
                if((isset($pathInfo)) && ($pathInfo != ""))
                {
                    $tmp = explode("/", $pathInfo);

                    if($tmp[(count($tmp) - 1)] == "")
                    {
                        $extra = true;
                    }

                    $ds = explode("/", trim($pathInfo, "/"));
                    for($i = 0; $i < (count($ds) - 1); $i++)
                    {
                        $prepend .= "../";
                    }
                }
            }


            if($extra)
            {
                $ret = "../".$ret;
            }

            return $prepend.$ret;
        }

        public static function URLify($arg)
        {
            $tr = explode(" ", strtolower(trim($arg)));

            $ret = "";

            for($i = 0; $i < count($tr); $i++)
            {
                if($ret == "")
                {
                    $ret .= $tr[$i];
                }
                else
                {
                    $ret .= "-". $tr[$i];
                }
            }

            return $ret;
        }

        public function MapRoutes()
        {
            if(($this->home == $this->Page) || ($this->Page == ""))
            {
                require_once ($this->home);

                if($this->home === "")
                {
                    echo "No home page have been added for routing";
                }
                return;
            }

            for($i = 0; $i < count($this->redirects); $i++)
            {
                if(trim(strtolower($this->Page) == trim(strtolower($this->redirects[$i]))))
                {
                    header("Location: ".$this->redirectPath[$i]);
                    return;
                }
            }
            for($i = 0; $i < count($this->routes); $i++)
            {
                if(trim(strtolower($this->Page) == trim(strtolower($this->routes[$i]))))
                {
                    if(file_exists($this->paths[$i]))
                    {
                        require_once ($this->paths[$i]);
                        return;
                    }
                    else
                    {
                        if($this->ErrorPage == true)
                        {
                            if($this->ErrorPagePath != "")
                            {
                                if(file_exists($this->ErrorPagePath))
                                {
                                    require_once ($this->ErrorPagePath);
                                    return;
                                }
                                else
                                {
                                    echo "Bad routing configuration. 404 Page Unavailable";
                                }
                            }
                            else
                            {
                                echo "
                                    <!DOCTYPE html>
                                    <html>
                                        <head>
                                            <title>Error : : Not Found</title>
                                        </head>
                                        <body style='text-align: center;'>
                                            <h1 style='color: dimgray; font-family: Arial; font-size: 3em;'>404</h1>
                                            <h3 style='color: lightgray; font-family: Segoe UI; font-weight: normal;'>
                                                The requested page was not found
                                            </h3>
                                        </body>
                                    </html>";
                            }
                        }
                    }
                }
            }
        }

        public function ArgString()
        {
            $ret = "";

            for($i = 0; $i < count($this->Args); $i++)
            {
                $ret .= "/".$this->Args[$i];
            }
            return $ret;
        }

        public static function BuildMeta($url)
        {
            $pre = explode(" ", strtolower(trim($url)));
            $meta = "";

            for($i = 0; $i < count($pre); $i++)
            {
                if($i == 0)
                {
                    $meta = $pre[0];
                }
                else
                {
                    $meta .= "-".$pre[$i];
                }
            }
            return $meta;
        }
    }