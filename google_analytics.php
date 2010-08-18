<?php

/**
 * google_analytics
 *
 * Bind google analytics script
 *
 * @version 1.0 - 24.11.2009
 * @author Roland 'rosali' Liebl
 * @website http://myroundcube.googlecode.com
 * @licence GNU GPL
 *
 **/

/**
 * Usage: http://mail4us.net/myroundcube/
 *
 **/ 

class google_analytics extends rcube_plugin
{
  function init()
  {
    if(file_exists("./plugins/google_analytics/config/config.inc.php"))
      $this->load_config('config/config.inc.php');
    else
      $this->load_config('config/config.inc.php.dist');
    $this->add_hook('render_page', array($this, 'add_script'));
  }

  function add_script($args){
    $rcmail = rcmail::get_instance();
    $exclude = array_flip($rcmail->config->get('google_analytics_exclude'));
    
    if(isset($exclude[$args['template']]))
      return $args;
    if($rcmail->config->get('google_analytics_privacy')){
      if(!empty($_SESSION['user_id']));
        return $args;
    }
    
    $script = '
<script type="text/javascript">
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src=\'" + gaJsHost + "google-analytics.com/ga.js\' type=\'text/javascript\'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
try {
var pageTracker = _gat._getTracker("' . $rcmail->config->get('google_analytics_id') . '");
pageTracker._setDomainName("' . $rcmail->config->get('google_analytics_tracker') . '");
pageTracker._trackPageview();
} catch(err) {}</script>    
    ';
    
    $rcmail->output->add_footer($script);
     
    return $args;
  }
}

?>
