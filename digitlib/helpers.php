<?php
/**
 * Micro helper for PHP
 * @author Muhamad Rifki <rifki@rifkilabs.net>
 * @version 1.0
 */
require_once 'config.php';
require_once 'database.php';

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
    public static function redirect($url = null)
    {
        if (is_null($url)) $url = $_SERVER['PHP_SELF'];
        header("Location: {$url}");
        exit();
    }

    /**
     * CURL get method.
     * @param null $url
     * @param null $data
     * @return mixed|null
     */
    public static function curlGet($url = null, $data = null)
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

    public static function curlPost($url = null, $data = null)
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
    public static function curlPut($url = null, $data = null)
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
    public static function curlDelete($url = null, $data = null)
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

    # continue...
    public function paginator($limit, $request) {
        if (empty($request)) {
            $posisi  = 0;
            $request = 1;
        } else {
            $posisi = ($request - 1) * $limit;
        }

        $db = new Database();
        $db->select($table, $where, $orderby);
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
     * Facebook meta
     */
    public static function facebookMeta() {}

    /**
     * Facebook Connect
     */
    public static function facebookConnect() {}

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
    public static function sendMail($username, $password, $from, $to, $subject, $body, $debug = false, $redirectSuccess = null, $redirectFailed = null)
    {
        require_once 'class.phpmailer.php';
        require_once 'class.smtp.php';

        if (file_exists('class.phpmailer.php') && file_exists('class.smtp.php')) {
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
                redirect($redirectSuccess);
            } else {
                redirect($redirectFailed);
            }
        } else {
            throw new RuntimeException('Class php mailer does exist');
        }
    }

    /**
     * Generate Province
     * @param type $name
     * @param type $optvalue
     * @param type $required
     * @param type $title
     * @return string
     */
    public static function generateProvinces($name, $optvalue='provinsi', $required = null, $title = null)
    {
        if (isset($required) != null || isset($title) != null) {
            $data = "<select name='$name' $required title='$title'>";
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
     * use getUpload($_FILE['file'], true)
     * true if use resize and false not resize image
     * @param $file
     * @param bool $resize
     * @return string
     */
    public function getUpload($file, $isResize = true)
    {
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

        if ($name != "")
            # Create directory uploads in root app (is not exist)
            if (!file_exists(SITE_PATH.'/uploads')) {
                mkdir(SITE_PATH.'/uploads');
                $move = move_uploaded_file($file['tmp_name'], SITE_PATH.'/uploads/'.$name);
            } else {
                $move = move_uploaded_file($file['tmp_name'], SITE_PATH.'/uploads/'.$name);
            }

        if ($move) {
            $temp = getimagesize(SITE_PATH.'/uploads/'. $name);
            // resize
            $scale = min(900/$temp[0], 450/$temp[1]);
            $this->resizeImage($file['type'], SITE_PATH.'/uploads/'. $name, $temp[0], $temp[1], $scale, $isResize);
            return $name;
        }

        return '';
    }

    /**
     * @param $type
     * @param $image
     * @param $width
     * @param $height
     * @param $scale
     */
    private  function resizeImage($type, $image, $width, $height, $scale, $isResize)
    {
        if ($isResize == true) {
            $newImageWidth = ceil($width * $scale);
            $newImageHeight = ceil($height * $scale);
            switch ($type) {
                case 'image/jpg': case 'image/jpeg':
                $newImage = imagecreatetruecolor($newImageWidth,$newImageHeight);
                $source = imagecreatefromjpeg($image);
                imagecopyresampled($newImage,$source,0,0,0,0,$newImageWidth,$newImageHeight,$width,$height);
                ob_start();
                imagejpeg($newImage,$image,90);
                ob_get_clean();
                break;
                case 'image/png':
                    $newImage = imagecreatetruecolor($newImageWidth,$newImageHeight);
                    $source = imagecreatefrompng($image);
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
                    $newImage = imagecreatetruecolor($newImageWidth,$newImageHeight);
                    $source = imagecreatefromgif($image);
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
        } else {
            return false;
        }
    }

    /**
     * @param $file
     * @return int|mixed
     */
    private  function get_mimetype($file)
    {
        // php 5.3x or higher only
        if (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE); // return mime type ala mimetype extension
            return finfo_file($finfo, $file);
        } else {
            // for image only, available in php 4.x or higher
            return exif_imagetype($file);
        }
    }

    /**
     * @param $file
     * @return bool
     */
    public function validation_file($file)
    {
        $permit = array('image/jpg','image/jpeg','image/gif','image/png', IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG);
        if(!in_array($this->get_mimetype($file['tmp_name']), $permit) || $file['size'] > 5097152 ){
            return false;
        } else {
            return true;
        }
    }
}
