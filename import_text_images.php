<?php
/*
 * This script copy the CDN images from articles
 * 
 */
require_once('wp-config.php');

// Check if a remote file exists  
function checkRemoteFile($url)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    // don't download content
    curl_setopt($ch, CURLOPT_NOBODY, 1);
    curl_setopt($ch, CURLOPT_FAILONERROR, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    if (curl_exec($ch) !== FALSE) {
        return true;
    } else {
        return false;
    }
}

// Print Array for debug
if (!function_exists("preprint")) {
    function preprint($s, $return = false)
    {
        $x = "<pre>";
        $x .= print_r($s, 1);
        $x .= "</pre>";
        if ($return) return $x;
        else print $x;
    }
}

// Set database server access variables:
if ($_SERVER['REMOTE_ADDR'] == '10.190.254.11') {
    $host = "HOST";
    $user = "USER";
    $pwd = "PASSWORD";
    $db = "DATABASE";
} else {
    $host = "HOST";
    $user = "USER";
    $pwd = "PASSWORD";
    $db = "DATABASE";
}

// open connection
$connection = mysql_connect($host, $user, $pwd) or die("Unable to connect!");

mysql_select_db($db) or die("Unable to select database!");

$query = "
SELECT ID, post_title, post_content, post_date,
convert(m1.meta_value,unsigned) AS 'meta_value_story_id',
convert(m2.meta_value,unsigned)  AS 'meta_value_media_id',
m3.meta_value AS 'meta_value_type'
FROM wp_posts
INNER JOIN wp_postmeta m1
  ON ( wp_posts.ID = m1.post_id)
INNER JOIN wp_postmeta m2
  ON ( wp_posts.ID = m2.post_id )
INNER JOIN wp_postmeta m3
  ON ( wp_posts.ID = m3.post_id )
WHERE
wp_posts.post_type = 'post'
AND  (convert(m1.meta_value,unsigned)  BETWEEN '0' AND '10000')
AND ( m1.meta_key = 'story_id' )
AND ( m2.meta_key = 'media_id' )
AND ( m3.meta_key = 'type' )
GROUP BY wp_posts.ID
ORDER BY wp_posts.ID ASC
";

#$query = "select ID. title from wp_options";
#final: 17319
// execute query
$result = mysql_query($query) or die("Error in query: $query. " . mysql_error());

// see if any rows were returned
if (mysql_num_rows($result) > 0) {

    $cont = 0;

    while ($row = mysql_fetch_assoc($result)) {
        echo $row['meta_value_story_id'] . " | ";
        echo $row['post_title'] . "<br/>";

        $dom = new domDocument;

        /*** load the html into the object ***/
        $dom->loadHTML($row['post_content']);

        /*** discard white space ***/
        $dom->preserveWhiteSpace = false;

        $images = $dom->getElementsByTagName('img');

        foreach ($images as $img) {
            $url = $img->getAttribute('src');
            #$alt = $img->getAttribute('alt');
            #echo "Title: $alt<br>$url<br>";
            #echo $url."<br/>";


            if (strpos($url, '/cdn/') !== false) {
                echo $url . "<br/>";

                //var_dump(parse_url($url));

                $path_parts = pathinfo($url);
                //echo "dirname: " . $path_parts['dirname'], "<br/>";

                #echo "basename: " . $path_parts['basename'], "<br/><br/>";
                //echo $path_parts['extension'], "<br/>";
                //echo "filename: " . $path_parts['filename'], "<br/>"; // since PHP 5.2.0

                $localhost = "http://127.0.0.1/her.ie/";


                $source_file = str_replace($localhost, "", $url);
                $new_file = str_replace("/cdn/", "/cdn2/", $source_file);
                $destiny_path_temp = str_replace($localhost, "", $path_parts['dirname']);
                $destiny_path = str_replace("/cdn/", "/cdn2/", $destiny_path_temp);

                echo "new file >>>" . str_replace($localhost, "", $new_file) . "<br/>";
                echo "destiny path >>> " . $destiny_path . "<br/>";
                //echo $path_parts['extension'], "<br/>";

                mkdir($destiny_path, 0777, TRUE);
                if (copy($source_file, $new_file)) {
                    echo "Copy success!";
                } else {
                    echo "Copy failed.";
                }


            }


        }


        $cont++;

    }

    unset($my_post);
    echo "<strong>TOTAL POSTS</strong>: " . $cont . "<br/>";
} else {
    // Print status message
    echo "No rows found!";
}

mysql_close($connection);

?>