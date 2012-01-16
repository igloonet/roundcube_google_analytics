<?php

/**
 * google_analytics
 *
 * Bind google analytics script
 *
 * @version 1.4 - 26. 1. 2012
 * @author Roland 'rosali' Liebl
 * @modified_by Ondra 'Kepi' Kudlík
 * @website http://github.com/igloonet/roundcube_google_analytics
 * @licence GNU GPL
 *
 * ChangeLog
 *
 *    1.4 - avoid fstats with config file and support including
 *          instead of excluding
 *    1.1 - added support for async mode and empty tracker domain
 *
 **/

class google_analytics extends rcube_plugin
{
    function init()
    {
        $this->add_hook('render_page', array($this, 'add_script'));
    }

    function add_script($args)
    {
        $rcmail = rcmail::get_instance();

        // test if we have global_config plugin
        if ( !in_array('global_config', $plugins = $rcmail->config->get('plugins')) ) {
            $this->load_config('config/config.inc.php');
        }

        // do not allow logged users if privacy on
        if(!empty($_SESSION['user_id']) && $rcmail->config->get('google_analytics_privacy', FALSE))
            return $args;

        // excluding or including
        if ( $rcmail->config->get('google_analytics_excluding', TRUE) ) {
            if ( in_array($args['template'], $rcmail->config->get('google_analytics_exclude', array())) )
                return $args;
        }
        else {
            if ( !in_array($args['template'], $rcmail->config->get('google_analytics_include', array('login'))) )
                return $args;
        }

        $google_analytics_domain = $rcmail->config->get('google_analytics_domain', '');
        $set_domain_name = '';

        // async mode
        if ( $rcmail->config->get('google_analytics_async')){
            // set domain if not empty
            if ( !Empty($google_analytics_domain) )
                $set_domain_name = "  _gaq.push(['_setDomainName', '" . $rcmail->config->get('google_analytics_domain') . "']);";

            $script = "<script type=\"text/javascript\">
var _gaq = _gaq || [];
_gaq.push(['_setAccount', '" . $rcmail->config->get('google_analytics_id') . "']);
" . $set_domain_name . "
_gaq.push(['_trackPageview']);

(function() {
var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
})();

</script>
";
          // async mode add script to head of page
          $rcmail->output->add_header($script);
        // sync mode
        }
        else {
            // set domain if not empty
            if (!Empty($google_analytics_domain))
                $set_domain_name = 'pageTracker._setDomainName("' . $rcmail->config->get('google_analytics_domain') . '");';

                $script = '<script type="text/javascript">
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src=\'" + gaJsHost + "google-analytics.com/ga.js\' type=\'text/javascript\'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
try {
var pageTracker = _gat._getTracker("' . $rcmail->config->get('google_analytics_id') . '");
' . $set_domain_name . '
pageTracker._trackPageview();
} catch(err) {}</script>    
';
            // sync mode add script to end of page
            $rcmail->output->add_footer($script);
        }

        return $args;
    }
}

?>
