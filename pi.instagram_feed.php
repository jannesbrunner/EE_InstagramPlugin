<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Instagram Feed
 *
 * @package     ExpressionEngine
 * @category    Plugin
 * @author      Jannes Brunner
 * @copyright   Copyright (c) 2018, Jannes Brunner
 * @link        http://www.jannesbrunner.de
 */
class Instagram_feed
{

    public static $name = 'Instagram Feed';
    public static $version = '1.0';
    public static $author = 'Jannes Brunner';
    public static $author_url = 'http://www.jannesbrunner.de';
    public static $description = 'Create instagram feed';
    public static $typography = false;

    // --------------------------------------------------------------------

    /**
     * Instagram Feed
     *
     *
     * @access  public
     * @return  string
     */

    public function __construct()
    {
        // connecting EE and the plugin
        $this->EE = &get_instance();
        // ... and the tags
        $this->tagdata = $this->EE->TMPL->tagdata;

        // Get token from Template
        $this->atoken = ee()->TMPL->fetch_param('at');

        $this->return_data = "Instagram Feed: Please check documentation";
    }

    
    // ========
    // SINGLE TAGS
    // ========
    public function self_picture() {
        return $this->get_userdata("picture");
    }

    public function self_username() {
        return $this->get_userdata("username");
    }

    public function self_fullname() {
        return $this->get_userdata("fullname");
    }

    public function self_bio() {
        return $this->get_userdata("bio");
    }

    public function self_website() {
        return $this->get_userdata("website_link");
    }

    public function self_profile() {
        return $this->get_userdata("profile_link");
    }

    public function self_totalposts() {
        return $this->get_userdata("total_posts");
    }

    public function self_totalfollower() {
        return $this->get_userdata("total_follower");
    }

    public function self_totalfollows() {
        return $this->get_userdata("total_follows");
    }

    private function get_userdata($argument) {
            $posts = $this->curl_data("https://api.instagram.com/v1/users/self/");
            if($posts == null) { return $this->ig_error; }
            switch ($argument) {
                case "picture":  return $posts["data"]["profile_picture"];
                case "username":  return $posts["data"]["username"];
                case "fullname": return $posts["data"]["full_name"];
                case "bio":  return $posts["data"]["bio"];
                case "website_link":  return $posts["data"]["website"];
                case "profile_link":  return "https://instagram.com/" . $posts["data"]["username"];
                case "total_posts":  return $posts["counts"]["media"];
                case "total_follower":  return $posts["counts"]["followed_by"];
                case "total_follows":  return $posts["counts"]["follows"];
                default:
                    return "Invalid type argument!";     
            }
    }

 

    // ========
    // TAG PAIRS
    // ========

    // Returns the recent IG posts of the User
    public function get_recent()
    {
        // fetch parameters from template
        $limit = intval(ee()->TMPL->fetch_param('limit', '20'));

        // Set a default limit of 20 if the user did not set a proper one
        if (!($this->isValidNumericTag($limit))) {
            $limit = 20;
        }
        // Check if user is authenticated

        // Get recent IG Posts, amount = $limit
        $posts = $this->curl_data("https://api.instagram.com/v1/users/self/media/recent/?count=" . $limit);

        if ($posts != null) {
            $tagdata = array();
            for ($i = 0; $i < $limit; $i++) {
                $tagdata[$i] = array(
                    "picture" => $posts["data"][$i]["images"]["thumbnail"]["url"],
                    "comments" => $posts["data"][$i]["comments"]["count"],
                    "likes" => $posts["data"][$i]["likes"]["count"],
                    "link" => $posts["data"][$i]["link"],
                );
            }
            return $this->EE->TMPL->parse_variables($this->tagdata, $tagdata);
        } // display error
        else {
            $tagdata[0] = array(
                "picture" => PATH_THIRD . "instagram_feed/warn.png",
                "comments" => $this->ig_error,
                "likes" => $this->ig_error,
                "link" => $this->ig_error,
            );

            return $this->EE->TMPL->parse_variables($this->tagdata, $tagdata);
        }
    }

    // Returns user data of IG user
    public function get_user()
    {
       
        $posts = $this->curl_data("https://api.instagram.com/v1/users/self/");
        if ($posts != null) {
            $tagdata[0] = array(
                "picture" => $posts["data"]["profile_picture"],
                "username" => $posts["data"]["username"],
                "fullname" => $posts["data"]["full_name"],
                "bio" => $posts["data"]["bio"],
                "website_link" => $posts["data"]["website"],
                "profile_link" => "https://instagram.com/" . $posts["data"]["username"],
                "total_posts" => $posts["counts"]["media"],
                "total_follower" => $posts["counts"]["followed_by"],
                "total_follows" => $posts["counts"]["follows"],
            );
            return $this->EE->TMPL->parse_variables($this->tagdata, $tagdata);
        } else {
            // display error
            $tagdata[0] = array(
                "picture" => './system/user/addons/instagram_feed/warn.png',
                "username" => $this->ig_error,
                "fullname" => $this->ig_error,
                "bio" => $this->ig_error,
                "website_link" => $this->ig_error,
                "total_posts" => $this->ig_error,
                "total_follower" => $this->ig_error,
                "total_follows" => $this->ig_error,
            );
            return $this->EE->TMPL->parse_variables($this->tagdata, $tagdata);
        }
    }

    // Search for a hashtag, display count and name
    public function tag_search()
    {

        // fetch parameters from template
        $limit = intval(ee()->TMPL->fetch_param('limit', '20'));
        $hashtag = intval(ee()->TMPL->fetch_param('hashtag'));

        // Set a default limit of 20 if the user did not set any
        if (!($this->isValidNumericTag($limit))) {
            $limit = 20;
        }
        // check if user provides valid hashtag parameter
        if (!($this->isValidStringTag($hashtag))) {
            $hashtag = "hashtag";
        }

        $posts = $this->curl_data("https://api.instagram.com/v1/tags/search?q=" . $hashtag);
        if ($posts != null) {
            $tagdata = array();
            for ($i = 0; $i < $limit; $i++) {
                $tagdata[$i] = array(
                    "media_count" => $posts["data"][$i]["media_count"],
                    "name" => $posts["data"][$i]["name"],
                );
            }
            return $this->EE->TMPL->parse_variables($this->tagdata, $tagdata);
        } // Output error
        else {
            $tagdata[0] = array(
                "media_count" => $this->ig_error,
                "name" => $this->ig_error,
            );

            return $this->EE->TMPL->parse_variables($this->tagdata, $tagdata);

        }
    }

    // ========
    // Helper
    // ========

    // Pulls data from IG Api and returns data as JSON Object
    // Returns null and set error message if unsuccesfull
    private function curl_data($APIurl)
    {
        
        // check if token is present
        if (is_null($this->atoken) || empty($this->atoken)) {
            $this->ig_error = 'Please provide valid IG Access token!';
            return null;
        }

        $ch = curl_init();

        if (substr($APIurl, -1) === '/') {
            $APIurl .= "?access_token=" . $this->atoken;
        } else {
            $APIurl .= "&access_token=" . $this->atoken;
        }

        curl_setopt($ch, CURLOPT_URL, $APIurl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $return = curl_exec($ch);

        curl_close($ch);

        $result = json_decode($return, true);

        // Check IG response for error
        if ($result["meta"]["code"] != 200) {
            $this->ig_error = "Error:" . $result["meta"]["code"] . "-" . $result["meta"]["error_message"];
            return null;
        }

        return $result;
    }


    private function isValidNumericTag($toCheck)
    {
        if (is_null($toCheck) || empty($toCheck) || !is_numeric((int) $toCheck)) {
            return false;
        }
        return true;
    }

    private function isValidStringTag($toCheck)
    {
        if (is_null($toCheck) || empty($toCheck) || is_numeric((int) $toCheck)) {
            return false;
        }
        return true;
    }

    // --------------------------------------------------------------------

    /**
     * Usage
     *
     * This function describes how the plugin is used.
     *
     * @access  public
     * @return  string
     */
    public static function usage()
    {
        ob_start();?>

        Creates instagram feed


        <?php
$buffer = ob_get_contents();
        ob_end_clean();

        return $buffer;
    }

}

/* End of file pi.instagram_feed.php */
/* Location: ./system/user/addons/instagram_feed/pi.instagram_feed.php */
