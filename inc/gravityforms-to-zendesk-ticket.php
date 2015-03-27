<?php

/* GravityForms to Zendesk Ticket

GravityForms to Zendesk Ticket is a simple Wordpress functions.php filter to pass GravityForms fields to a Zendesk ticket, including attachments. 
It utilizes the Zendesk v2 API, PHP, and cURL.

Author: Christi Richards
URL: http://www.christirichards.com
Github: https://github.com/christirichards/gravityforms-to-zendesk-ticket
*/

add_action("gform_after_submission_3", "gform_create_zendesk_ticket", 10, 2); // Change '3' to your GravityForm form ID

add_action("gform_after_submission_3", "gform_disable_post_creation", 20, 2); // OPTIONAL: Do not create entries from submissions - Change '3' to your GravityForm form ID
    
    function gform_disable_post_creation( $entry, $form ) {
        GFAPI::delete_entry( $entry['id'] );
    } 

    define("ZDAPIKEY", "APIKEYHERE"); // Zendesk API Key
    define("ZDUSER", "user@email.com"); // Zendesk User
    define("ZDURL", "https://domain.zendesk.com/api/v2"); // Zendesk URL (Do not put a trailing slash!)

    function curlWrap($url, $json, $action) {
    
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10 );
        curl_setopt($ch, CURLOPT_URL, ZDURL.$url);
        curl_setopt($ch, CURLOPT_USERPWD, ZDUSER."/token:".ZDAPIKEY);
        switch($action){
            case "POST":
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
                break;
            case "GET":
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
                break;
            case "PUT":
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
                curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
                break;
            case "DELETE":
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
                break;
            default:
                break;
        }

        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
        curl_setopt($ch, CURLOPT_USERAGENT, "MozillaXYZ/1.0");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        
        $output = curl_exec($ch);
        
        curl_close($ch);
        
        $decoded = json_decode($output);

        // print $output; // DEBUG

        return $decoded;
    }

    function curlUpload($url, $binary, $filename) {
    
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10 );
        curl_setopt($ch, CURLOPT_URL, ZDURL.$url."?filename=".$filename);
        curl_setopt($ch, CURLOPT_USERPWD, ZDUSER."/token:".ZDAPIKEY);
        
        
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");

        curl_setopt($ch, CURLOPT_POSTFIELDS, $binary);

        $size = filesize(rgar( $entry, '15' ));  // Upload field ID
        curl_setopt($ch, CURLOPT_INFILESIZE, $size);
        
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/binary'));
        curl_setopt($ch, CURLOPT_USERAGENT, "MozillaXYZ/1.0");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        
        $output = curl_exec($ch);
        
        curl_close($ch);
        
        $decoded = json_decode($output, true);

        // print_r($output); // DEBUG
        
        return $decoded;
    }

    function gform_create_zendesk_ticket($entry, $form){

        $binaryFile = file_get_contents(rgar( $entry, '15' ));

        $ext = pathinfo(rgar( $entry, '15' ), PATHINFO_EXTENSION);  // Upload field ID
        $upload = curlUpload("/uploads.json", $binaryFile, 'screenshot.'.$ext);  // Attachments will have the prettyname screenshot.[$ext]

        $token = $upload['upload']['token'];
     
        $create = json_encode(
            array(
                'ticket' => array(
                    'subject' => rgar( $entry, '3' ), // Subject or form name ID - up to you!
                    'comment' => array(
                        'body' => rgar( $entry, '6' ), // Textarea field ID
                        'uploads' => $token,
                    ),
                    'requester' => array(
                        'name' => rgar( $entry, '1.3' ). ' ' .rgar( $entry, '1.6' ), // Name fields ID
                        'email' => rgar( $entry, '2' ) // E-mail field ID
                    )
                )
            )
        );

        return curlWrap("/tickets.json", $create, "POST");
    }    

?>