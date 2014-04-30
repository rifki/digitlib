<?php
/**
 * Micro helper for PHP
 * @author Muhamad Rifki <rifki@rifkilabs.net>
 * @version 1.1.0
 */
require_once 'config.php';

class Helpers
{
    /**
     *  true
     */
    const LOGIN  = 1;

    /**
     *  false
     */
    const LOGOUT = 0;

    /**
     * Province list
     * @var array
     */
    private static $provinces = array(
        'aceh','bali','banten','bengkulu','gorontalo','jakarta','jambi','jawa barat',
        'jawa tengah','jawa timur','kalimantan barat','kalimantan selatan','kalimantan tengah',
        'kalimantan timur','kalimantan utara','kepulauan bangka belitung','kepulauan riau','lampung',
        'maluku','maluku utara','nusa tenggara barat','nusa tenggara timur','papua','papua barat','riau',
        'sulawesi barat','sulawesi selatan','sulawesi tengah','sulawesi tenggara','sulawesi utara',
        'sumatera barat','sumatera selatan','sumatera utara','yogyakarta',
    );

    /**
     * Country list
     * @var array
     */
    private static $country = array();

    /**
     * Login HTTP Authentication
     * @param type $username
     * @param type $password
     * @param type $realm
     * @param string $errorMsg
     */
    public static function httpAuth($username, $password, $realm = "SECURE AREA", $customeError = '')
    {
        if (!(isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])  && $_SERVER['PHP_AUTH_USER'] == $username && $_SERVER['PHP_AUTH_PW'] == $password ))
        {
            header('WWW-Authenticate: Basic realm="' .$realm. '"');
            header('Status: 401 Unauthorized');

            if (trim($customeError) == '') {
                $customeError  = "<h1>"."Authorization Required"."</h1>";
                $customeError .= "<p>"."You must enter a valid username and password"."</p>";
            }
            exit($customeError);
        }
    }

    /**
     * HTTP Authentication with Session
     * @param type $username
     * @param type $password
     * @param type $realm
     * @param type $customeError
     */
    public static function httpAuthSession($username, $password, $realm = "SECURE AREA", $customeError = '')
    {
        session_start();
        if (isset($_SESSION['login']) === self::LOGOUT) {
            $_SERVER['PHP_AUTH_USER'] = '';
            $_SERVER['PHP_AUTH_PW']   = '';
        }

        if (!(isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])  && $_SERVER['PHP_AUTH_USER'] == $username && $_SERVER['PHP_AUTH_PW'] == $password ))
        {
            $_SESSION['login'] = self::LOGIN;
            header('WWW-Authenticate: Basic realm="' .$realm. '"');
            header('Status: 401 Unauthorized');

            if (trim($customeError) === '') {
                $customeError  = "<h1>"."Authorization Required"."</h1>";
                $customeError .= "<p>"."You must enter a valid username and password"."</p>";
            }
            exit($customeError);
        }
    }

    /**
     * HTTP Logout. Clear session
     * @param type $redirectUrl string
     */
    public static function httpAuthSessionLogout($redirectUrl = '') {
        if (isset($_SERVER['login']) === self::LOGOUT) {
            session_destroy();
            if (trim($redirectUrl) != '')
                self::redirect($redirectUrl);
        }
        return false;
    }

    /**
     * Alert Javascript
     * @param string $messages
     * @return string
     */
    public static function jsAlert($message = '')
    {
        if (trim($message) != '')
            $alert = "<script>alert('{$message}'); history.go(-1)</script>";
        return $alert;
    }

    /**
     * Alert Javascript with redirect window location
     * @param string $messages
     * @param string $uri
     * @return string
     */
    public static function jsAlertLocation($messages, $url)
    {
        if (trim($messages) != '')
            $alert = "<script>alert('{$messages}'); window.location='{$url}';</script>";
        return $alert;
    }

    /**
     * redirect header
     * @param unknown_type $uri
     * @uses redirect_to('?act=foo') / redirect_to('foo.php?act=bar')
     */
    public static function redirect($url)
    {
        if (isset($url)) {
            header("Location: {$url}");
            exit();
        }
    }

    /**
     * Title page
     * @param string $base
     */
    public static function title($titleBase = '', $secondTitle = '')
    {
        if ($titleBase !== '') {
            return $titleBase." - ".$secondTitle;
        }
    }

    /**
     * Date format indonesia
     * @return type string
     */
    public static function dateFormatID() {
        $weekID     = array("Minggu","Senin","Selasa","Rabu","Kamis","Jumat","Sabtu");
        $weekEN     = date('w');
        $monthID    = array(1 => "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        $monthEN    = date('m');
        $day        = date('d');
        $year       = date('Y');

        return ($weekID[$weekEN].",\n".$day.' '.$monthID[$monthEN]."\n".$year);
    }

    /**
     * Send Mail
     * @param type $username
     * @param type $password
     * @param type $from
     * @param type $to
     * @param type $subject
     * @param type $body
     * @param type $debug
     * @param type $redirectSuccess
     * @param type $redirectFailed
     * @throws RuntimeException
     */
    public static function sendMail($username, $password, $from, $to, $subject, $body, $debug = false)/*$redirectSuccess = null, $redirectFailed = null*/
    {
        $path_phpmailer = SITE_PATH.'/lib/class.phpmailer.php';
        $path_smtp      = SITE_PATH.'/lib/class.smtp.php';

        if (file_exists($path_phpmailer) && file_exists($path_smtp)) {
            require_once $path_phpmailer;
            require_once $path_smtp;
            $mail = new PHPMailer();
            $mail->IsSMTP();
            $mail->Host         = "smtp.gmail.com";

            # val = 2. debug. default false if production.
            $mail->SMTPDebug    = $debug;
            $mail->SMTPSecure   = 'ssl';
            $mail->Port         = 465;
            $mail->SMTPKeepAlive= true;
            $mail->SMTPAuth     = true;
            $mail->CharSet      = 'utf-8';
            # SMTP account username
            $mail->Username     = $username;
            # SMTP account password
            $mail->Password     = $password;

            # set the email from 
            $mail->SetFrom($from);
            # set email subject
            $mail->Subject = $subject;
            $mail->AltBody = "To view the message, please use an HTML compatible email viewer!";

            //$body = eregi_replace("[\]", '', $body);
            # assign the message
            $mail->MsgHTML($body);
            # add recipient address   
            $mail->AddAddress($to);

            # send
            if (! $mail->Send()) {
                return;
            } 
            else {
                return;
            }
        } else {
            throw new RuntimeException('Class php mailer does exist');
        }
    }

    /**
     * Generate Province
     * @param type string $name
     * @param type string $optvalue
     * @param type string $required
     * @param type string $title
     * @return generate list province
     */
    public static function generateProvinces($name, $optvalue='provinsi', $required = null, $title = null, $style = null)
    {
        if (isset($required) != null || isset($title) != null || $style != null) {
            $data = "<select name='$name' $required title='$title' style='$style'>";
            $data .= "<option value>".ucwords($optvalue)."</option>";
        } else {
            $data .= "<select name='$name'>";
            $data .= "<option value>".ucwords($optvalue)."</option>";
        }

        foreach (self::$provinces as $key => $value) {
            $data .= "<option value='".$value."'>".ucwords($value)."</option>";
        }
        $data .= "</select>";

        return $data;
    }

    /**
     * Validate email address
     * @param type $email
     * @return boolean
     */
    public static function validateEmail($email)
    {
        if (function_exists('filter_var')) {
            if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
                return false;
            } else {
                return true;
            }
        } else {
            return preg_match('/^(?:[\w\!\#\$\%\&\'\*\+\-\/\=\?\^\`\{\|\}\~]+\.)*[\w\!\#\$\%\&\'\*\+\-\/\=\?\^\`\{\|\}\~]+@(?:(?:(?:[a-zA-Z0-9_](?:[a-zA-Z0-9_\-](?!\.)){0,61}[a-zA-Z0-9_-]?\.)+[a-zA-Z0-9_](?:[a-zA-Z0-9_\-](?!$)){0,61}[a-zA-Z0-9_]?)|(?:\[(?:(?:[01]?\d{1,2}|2[0-4]\d|25[0-5])\.){3}(?:[01]?\d{1,2}|2[0-4]\d|25[0-5])\]))$/', $email);
        }
    }

    /**
     * Random
     */
    public static function random() 
    {
        return substr(time(), -5);
    }

    /**
     * Convert object to assoc array
     * @param $data
     * @return array
     */
    public static function objectToArray($data)
    {
        if (is_array($data) || is_object($data)) {
            $result = array();
            foreach ($data as $key => $value) {
                $result[$key] = self::objectToArray($value);
            }
            return $result;
        }
        return $data;
    }

    /**
     * Character Limiter
     * @param   string
     * @param   integer
     * @param   string  the end character. Usually an ellipsis
     * @return  string
     */
    public static function wordLimiter($string, $limit = 100, $end_char = '&#8230;')
    {
        if (trim($string) == '') {
            return $string;
        }

        preg_match('/^\s*+(?:\S++\s*+){1,'.(int) $limit.'}/', $string, $matches);
        if (strlen($string) == strlen($matches[0])) {
            $end_char = '';
        }

        return rtrim($matches[0]).$end_char;
    }

    public static function timeSince($ptime)
    {
        $year = 'year';
        $month = 'month';
        $day = 'day';
        $hour = 'hour';
        $minute = 'minute';
        $second = 'second';
        $zeroSecond = '0 seconds';
        $ago = 'ago';

        $result = time() - $ptime;

        if ($result < 1) {
            return $zeroSecond;
        }

        $chunks = array(
            12 * 30 * 24 * 60 * 60  =>  $year, //year
            30 * 24 * 60 * 60       =>  $month, //month
            24 * 60 * 60            =>  $day, //day
            60 * 60                 =>  $hour, //hour
            60                      =>  $minute, //minute
            1                       =>  $second //second
        );

        foreach ($chunks as $key => $value) {
            $d = $result / $key;
            if ($d >= 1) {
                $r = round($d);
                return $r . ' ' . $value . ($r > 1 ? '' : '') . ' ' . $ago;
            }
        }
    }
}


/**
 * Upload file
 * @author Muhamad Rifki
 */
class File
{
    /**
     * use getUpload($_FILE['file'], true)
     * true if use resize and false not resize image
     * @param $file
     * @param bool $resize
     * @return string
     */
    public function upload($file, $isResize = true)
    {
        $path = SITE_PATH.'/uploads/';
        if ( ! file_exists($path)) {
            mkdir($path, 0777, true);
        }

        $name = "";

        switch($file['type']){
            case 'image/jpg':
                $name = time() . ".jpg";
                break;
            case 'image/jpeg':
                $name = time() . ".jpg";
                break;
            case 'image/png':
                $name = time() . ".png";
                break;
            case 'image/gif':
                $name = time() . ".gif";
                break;
        }

        if($name != "") $move = move_uploaded_file($file['tmp_name'], $path.$name);

        if ($move) {
            $temp = getimagesize($path . $name);
            // resize
            $scale = min(900 / $temp[0], 450 / $temp[1]);
            $this->resize($file['type'], $path.$name, $temp[0], $temp[1], $scale, $isResize);
            return $name;
        }

        return '';
    }

    /**
     * resize image
     * @param $type
     * @param $image
     * @param $width
     * @param $height
     * @param $scale
     */
    private  function resize($type, $image, $width, $height, $scale, $isResize)
    {
        if ($isResize == true) {
            $newImageWidth = ceil($width * $scale);
            $newImageHeight = ceil($height * $scale);
            switch ($type) {
                case 'image/jpg': case 'image/jpeg':
                    $newImage   = imagecreatetruecolor($newImageWidth,$newImageHeight);
                    $source     = imagecreatefromjpeg($image);
                    imagecopyresampled($newImage,$source,0,0,0,0,$newImageWidth,$newImageHeight,$width,$height);
                    ob_start();
                    imagejpeg($newImage,$image,90);
                    ob_get_clean();
                    break;
                case 'image/png':
                    $newImage   = imagecreatetruecolor($newImageWidth,$newImageHeight);
                    $source     = imagecreatefrompng($image);
                    imagealphablending($newImage, false);
                    imagesavealpha($newImage,true);
                    $transparent = imagecolorallocatealpha($newImage, 255, 255, 255, 127);
                    imagefilledrectangle($newImage, 0, 0, $newImageWidth, $newImageHeight, $transparent);
                    imagecopyresampled($newImage,$source,0,0,0,0,$newImageWidth,$newImageHeight,$width,$height);
                    ob_start();
                    imagepng($newImage, $image);
                    ob_get_clean();
                    break;
                case 'image/gif':
                    $newImage   = imagecreatetruecolor($newImageWidth,$newImageHeight);
                    $source     = imagecreatefromgif($image);
                    imagealphablending($newImage, false);
                    imagesavealpha($newImage,true);
                    $transparent = imagecolorallocatealpha($newImage, 255, 255, 255, 127);
                    imagefilledrectangle($newImage, 0, 0, $newImageWidth, $newImageHeight, $transparent);
                    imagecopyresampled($newImage,$source,0,0,0,0,$newImageWidth,$newImageHeight,$width,$height);
                    ob_start();
                    imagegif($newImage, $image);
                    ob_get_clean();
                    break;
            }
        } 
        else {
            return false;
        }
    }

    /**
     * Mime
     * @param $file
     * @return int|mixed
     */
    private function mimetype($file)
    {
        if (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            return finfo_file($finfo, $file);
        } 
        else {
            // for image only, available in php 4.x or higher
            return exif_imagetype($file);
        }
    }

    /**
     * validate image
     * @param $file
     * @return bool
     */
    public function isValid($file)
    {
        $permit = array('image/jpg','image/jpeg','image/gif','image/png', IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG);
        if (!in_array($this->mimetype($file['tmp_name']), $permit) || $file['size'] > 8242880 ){
            return false;
        } 
        else {
            return true;
        }
    }
}

/**
 * Curl libraries
 * @author Muhamad Rifki
 */
class Curl
{
    /**
     * curl GET method
     * @param null $url
     * @param null $data
     * @return mixed|null
     */
    public static function get($url = null, $data = null)
    {
        $str = null;
        if (is_array($data)) {
            $str = '?';
            foreach ($data as $key => $value) {
                $str .= $key.'='.$value.'&';
            }
            rtrim($str,'&');
        }

        $ch = curl_init();

        // check valid login API
        if (API_USERNAME != null && API_PASSWORD != null) {
            curl_setopt($ch, CURLOPT_USERPWD, API_USERNAME.':'.API_PASSWORD);
        }

        curl_setopt_array($ch, array(
            CURLOPT_HEADER          => 0,
            CURLOPT_RETURNTRANSFER  => true,
            CURLOPT_URL             => $url.$str,
            CURLOPT_SSL_VERIFYPEER  => 0,
            CURLOPT_SSL_VERIFYHOST  => 0,
        ));

        //$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $data = curl_exec($ch);
        curl_close($ch);

        return $data;
    }

    /**
     * curl POST method
     * @param null $url
     * @param null $data
     * @return mixed
     */
    public static function post($url = null, $data = null)
    {
        $str = null;
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $str .= $key.'='.$value.'&';
            }
            rtrim($str,'&');
        }

        $ch = curl_init();

        // check valid login API
        if (API_USERNAME != null && API_PASSWORD != null) {
            curl_setopt($ch, CURLOPT_USERPWD, API_USERNAME.':'.API_PASSWORD);
        }

        curl_setopt_array($ch, array(
            CURLOPT_POST            => 1,
            CURLOPT_POSTFIELDS      => $str,
            CURLOPT_RETURNTRANSFER  => true,
            CURLOPT_URL             => $url,
            CURLOPT_SSL_VERIFYPEER  => 0,
            CURLOPT_SSL_VERIFYHOST  => 0,
        ));

        //$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }

    /**
     * CURL put method
     * @param null $url
     * @param null $data
     * @return mixed
     */
    public static function put($url = null, $data = null)
    {
        $str = null;
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $str .= $key.'='.$value.'&';
            }
            rtrim($str,'&');
        }

        $ch = curl_init();

        // check valid login API
        if (API_USERNAME != null && API_PASSWORD != null) {
            curl_setopt($ch, CURLOPT_USERPWD, API_USERNAME.':'.API_PASSWORD);
        }

        curl_setopt_array($ch, array(
            CURLOPT_PUT             => 1,
            CURLOPT_POSTFIELDS      => $str,
            CURLOPT_RETURNTRANSFER  => true,
            CURLOPT_URL             => $url,
            CURLOPT_SSL_VERIFYPEER  => 0,
            CURLOPT_SSL_VERIFYHOST  => 0,
        ));

        //$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }

    /**
     * CURL delete method
     * @param null $url
     * @param null $data
     * @return mixed|null
     */
    public static function delete($url = null, $data = null)
    {
        $str = null;
        if (is_array($data)) {
            $str = '?';
            foreach ($data as $key => $value) {
                $str .= $key.'='.$value.'&';
            }
            rtrim($str,'&');
        }

        $ch = curl_init();

        // check valid login API
        if (API_USERNAME != null && API_PASSWORD != null) {
            curl_setopt($ch, CURLOPT_USERPWD, API_USERNAME.':'.API_PASSWORD);
        }

        curl_setopt_array($ch, array(
            CURLOPT_CUSTOMREQUEST   => 'DELETE',
            CURLOPT_HEADER          => 0,
            CURLOPT_RETURNTRANSFER  => true,
            CURLOPT_URL             => $url.$str,
            CURLOPT_SSL_VERIFYPEER  => 0,
            CURLOPT_SSL_VERIFYHOST  => 0
        ));

        //$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $data = curl_exec($ch);
        curl_close($ch);

        return $data;
    }

    /**
     * JSON Decode
     * @param type $encodedValuem
     * @return type mixed
     * @throws RuntimeException
     */
    public static function jsonDecode($encodedValue)
    {
        $encodedValue = (string) $encodedValue;
        if (function_exists('json_decode')) {
            $decode = json_decode($encodedValue);

            # json_last_error only php 5.3 or later
            if (function_exists('json_last_error')) {
                switch (json_last_error()) {
                    case JSON_ERROR_NONE:
                        break;
                    case JSON_ERROR_DEPTH:
                        throw new RuntimeException('Decoding failed: Maximum stack depth exceeded');
                    case JSON_ERROR_CTRL_CHAR:
                        throw new RuntimeException('Decoding failed: Unexpected control character found');
                    case JSON_ERROR_SYNTAX:
                        throw new RuntimeException('Decoding failed: Syntax error');
                    default:
                        throw new RuntimeException('Decoding failed');
                }
            }
            return $decode;
        }

        return self::jsonDecode($encodedValue);
    }

    /**
     * JSON Encode
     * @param type $value
     * @return type mixed
     */
    public static function jsonEncode($value)
    {
        if (function_exists('json_encode')) {
            $encode = json_encode($value);
        } else {
            $encode = json_encode($value);
        }

        return $encode;
    }    
}


/**
 * @author Muhamad Rifki
 * Get Youtube ID, Get embed and displaying thumbnail
 * @version 1.0.1
 */
class Video
{
    /**
     * Get thumbnail image 
     * @param $id
     * @param int $thumb(0,1,2,3)
     * @param string $size
     * @return bool
     */
    public static function getThumbs($id, $size='medium')
    {
        if (isset($id)) {
            if (self::isValidID($id)) {
                $id = self::isValidID($id);
            }

            switch (strtolower($size)) {
                case 'medium':
                    $images = 'http://i1.ytimg.com/vi/'.$id.'/mqdefault.jpg';
                    break;
                case 'large':
                    $images = 'http://i1.ytimg.com/vi/'.$id.'/hqdefault.jpg';
                    break;
                case 'extra':
                    $images = 'http://i1.ytimg.com/vi/'.$id.'/sddefault.jpg';
                    break;
                default: $images = '';
            }

            return $images;
        }
    }

    /**
     * Get Video ID
     * @param $url
     * @return bool
     */
    public static function getVideoID($url)
    {
        if (self::isValidURL($url)) {
            $part = parse_url($url);
            if ( strpos($url, trim('youtube')) ) {
                if ( strpos($url, 'v=') ) {
                    return substr( $part['query'],  strpos($part['query'], 'v=') + 2, 11 );
                }
                elseif ( strpos($url, '/v/') ) {
                    return substr( $part['path'], strpos($part['path'], '/v/') + 3 , 11 );
                }
                elseif ( strpos($url, '/vi/') ) {
                    return substr( $part['path'], strpos($part['path'], '/vi/') + 4, 11 );
                }
                elseif ( strpos($url, trim('youtu.be') ) || strpos($url, trim('www.youtu.be')) ) {
                    if (strpos($url, '/'))
                        return substr( $part['path'], strpos($part['path'], '/') + 1, 11 );
                }
                elseif (strpos($url, '/embed/')) {
                    return substr( $part['path'], strpos($part['path'], '/embed/') + 7, 11);
                }
                else {
                    return false;
                }
            }
        } else {
            return false;
        }
    }

    /**
     * Get Youtube embed
     *
     * @param string Youtube id
     * @param int width
     * @param int height
     * @param boolean old embed (flash/iframe)
     * @param boolean suggested video
     * @param boolean privacy mode
     * @return string generate code
     */
    public static function getEmbed($id, $width='', $height='', $old_embed=false, $sugested=false, $privacy_mode=true) 
    {
        if (isset($id)) {
            if (self::isValidID($id)) {
                $id = self::isValidID($id);
            }

            // Only support Flash.
            if ($old_embed) {
                $embed = '<object width="'.$width.'" height="'.$height.'">';
                //Show suggested videos when the video finishes
                $sugested = ($sugested === true ? '&amp;rel=0' : '');
                //Enabling this option means that YouTube won’t store information about visitors on your web page unless they play the video.
                $privacy_mode = ($privacy_mode === true ? '//www.youtube-nocookie.com/v/' : '//www.youtube.com/v/');

                if ($privacy_mode) {
                    $embed .= '<param name="movie" value="'.$privacy_mode.$id.'"?version=3&amp;hl=en_US'.$sugested;
                }
                else {
                    $embed .= '<param name="movie" value="'.$privacy_mode.$id.'"?version=3&amp;hl=en_US'.$sugested;
                }

                $embed .= '<param name="allowFullScreen" value="true"></param>';
                $embed .= '<param name="allowscriptaccess" value="always"></param>';
                $embed .= '<embed src="'.$privacy_mode.$id.'"?version=3&amp;hl=en_US"'.$sugested.'" type="application/x-shockwave-flash" width="'.$width.'" height="'.$height.'" allowscriptaccess="always" allowfullscreen="true"></embed>';
                $embed .= '</object>';
            }
            //supports both Flash and HTML5 video
            else {
                $embed = '<iframe width="'.$width.'" height="'.$height.'" ';
                //Show suggested videos when the video finishes
                $sugested = ($sugested === true ? '&rel=0' : '');
                //Enabling this option means that YouTube won’t store information about visitors on your web page unless they play the video.
                $privacy_mode = ($privacy_mode === true ? '//www.youtube-nocookie.com/embed/' : '//www.youtube.com/embed/');

                if ($privacy_mode) {
                    $embed .= 'src="'.$privacy_mode.$id.'" frameborder="0" allowfullscreen></iframe>';
                }
                else {
                    $embed .= 'src="'.$privacy_mode.$id.'" frameborder="0" allowfullscreen></iframe>';
                }
            }

            return $embed;
        }
    }

    /**
     * Validate URL
     * @param string $url
     * @return bool
     */
    public static function isValidURL($url)
    {
        if (parse_url($url, PHP_URL_SCHEME)) {
            return true;
        }
        return false;
    }

    /**
     * Validate Video ID
     * @param Youtube id string $id
     */
    public static function isValidID($id)
    {
        $header = get_headers('http://gdata.youtube.com/feeds/api/videos/'.$id);
        # HTTP/1.0 200 OK
        if (strpos($header[0], 200)) {
            return true;
        }
        return false;
    }
}
