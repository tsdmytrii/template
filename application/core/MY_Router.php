<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * MY_Router Class
 *
 * @author        Damien K.
 */
class MY_Router extends CI_Router {
    // --------------------------------------------------------------------

    /**
     * OVERRIDE
     *
     * Validates the supplied segments.  Attempts to determine the path to
     * the controller.
     *
     * @access    private
     * @param    array
     * @return    array
     */
    function __construct() {
        parent::__construct();

        //	$this->check_auth();
    }

    function _set_routing() {


        // Are query strings enabled in the config file?  Normally CI doesn't utilize query strings
        // since URI segments are more search-engine friendly, but they can optionally be used.
        // If this feature is enabled, we will gather the directory/class/method a little differently
        $segments = array();
        if ($this->config->item('enable_query_strings') === TRUE AND isset($_GET[$this->config->item('controller_trigger')])) {
            if (isset($_GET[$this->config->item('directory_trigger')])) {
                $this->set_directory(trim($this->uri->_filter_uri($_GET[$this->config->item('directory_trigger')])));
                $segments[] = $this->fetch_directory();
            }

            if (isset($_GET[$this->config->item('controller_trigger')])) {
                $this->set_class(trim($this->uri->_filter_uri($_GET[$this->config->item('controller_trigger')])));
                $segments[] = $this->fetch_class();
            }

            if (isset($_GET[$this->config->item('function_trigger')])) {
                $this->set_method(trim($this->uri->_filter_uri($_GET[$this->config->item('function_trigger')])));
                $segments[] = $this->fetch_method();
            }
        }

        // Load the routes.php file.
        if (defined('ENVIRONMENT') AND is_file(APPPATH . 'config/' . ENVIRONMENT . '/routes' . EXT)) {
            include(APPPATH . 'config/' . ENVIRONMENT . '/routes' . EXT);
        } elseif (is_file(APPPATH . 'config/routes' . EXT)) {
            include(APPPATH . 'config/routes' . EXT);
        }

        $this->routes = (!isset($route) OR !is_array($route)) ? array() : $route;

        unset($route);

        include(APPPATH . 'config/database' . EXT);

        $conn = mysqli_connect($db['default']['hostname'], $db['default']['username'], $db['default']['password'], $db['default']['database']);
        $sql = sprintf("SELECT link, server_method FROM links");
        $query = mysqli_query($conn, $sql);

        while($row = mysqli_fetch_array($query)){
            if(!empty($row['link'])){

                $this->routes[$row['link']] = $row['server_method'];
            }
        }

        mysqli_close($conn);

        //$CI =& get_instance();
        //echo 'lala';
        //require_once( BASEPATH .'database/DB'. EXT );
        //$db = DB();
        //$query = $db->get( 'app_routes' );
        //$result = $query->result();
        // Set the default controller so we can display it in the event
        // the URI doesn't correlated to a valid controller.
        $this->default_controller = (!isset($this->routes['default_controller']) OR $this->routes['default_controller'] == '') ? FALSE : strtolower($this->routes['default_controller']);

        // Were there any query string segments?  If so, we'll validate them and bail out since we're done.
        if (count($segments) > 0) {
            return $this->_validate_request($segments);
        }

        // Fetch the complete URI string
        $this->uri->_fetch_uri_string();

        // Is there a URI string? If not, the default controller specified in the "routes" file will be shown.
        if ($this->uri->uri_string == '') {
            return $this->_set_default_controller();
        }

        // Do we need to remove the URL suffix?
        $this->uri->_remove_url_suffix();

        // Compile the segments into an array
        $this->uri->_explode_segments();

        // Parse any custom routing that may exist
        $this->_parse_routes();

        // Re-index the segment array so that it starts with 1 rather than 0
        $this->uri->_reindex_segments();
    }

}
