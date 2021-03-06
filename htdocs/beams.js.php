<?php

/**
 * Javascript helper for Beams.
 *
 * @category   apps
 * @package    beams
 * @subpackage javascript
 * @author     ClearCenter <developer@clearcenter.com>
 * @copyright  2015 Marine VSAT
 * @license    http://www.clearcenter.com/Company/terms.html ClearSDN license
 * @link       http://www.clearcenter.com/support/documentation/clearos/beams/
 */

///////////////////////////////////////////////////////////////////////////////
// B O O T S T R A P
///////////////////////////////////////////////////////////////////////////////

$bootstrap = getenv('CLEAROS_BOOTSTRAP') ? getenv('CLEAROS_BOOTSTRAP') : '/usr/clearos/framework/shared';
require_once $bootstrap . '/bootstrap.php';

clearos_load_language('beams');
clearos_load_language('base');

header('Content-Type: application/x-javascript');

?>

var lang_warning = '<?php echo lang('base_warning'); ?>';
var lang_info = '<?php echo lang('base_information'); ?>';
var lang_status = '<?php echo lang('beams_modem_status'); ?>';
var lang_network = '<?php echo lang('base_network'); ?>';
var lang_transmit = '<?php echo lang('beams_transmit'); ?>';
var lang_receive = '<?php echo lang('beams_receive'); ?>';

$(document).ready(function() {
    clearos_add_sidebar_pair(lang_receive, '<i id="beams_receive" class="fa fa-circle"></i>');
    clearos_add_sidebar_pair(lang_transmit, '<i id="beams_transmit" class="fa fa-circle"></i>');
    clearos_add_sidebar_pair(lang_network, '<i id="beams_network" class="fa fa-circle"></i>');
    clearos_add_sidebar_pair(lang_status, '<i id="beams_status" class="fa fa-circle"></i>');
    set_interface_fields();
    if ($('#net_name').length > 0) {
        toggle_network_type();
    }
    $('#command').on('click', function(e) {
        e.preventDefault();
        if ($('#command').val() == 0)
            $('#terminal_out').html('');
        else
            execute_command($('#command').val(), false);
    });
    $('#bootproto').change(function() {
        set_interface_fields();
    });
    get_modem_status();
    $('#lock_beam').on('click', function(e) {
        e.preventDefault();
        execute_command('beamselector lock', true);
    });
});

function execute_command(command, nonterminal) {
    var options = new Object;
    options.center = true;
    options.id = 'terminal_wait';

    $('#terminal_out').html(clearos_loading(options));
    $.ajax({
        dataType: 'json',
        url: '/app/beams/modem/execute',
        data: 'ci_csrf_token=' + $.cookie('ci_csrf_token') + '&command=' + command,
        type: 'POST',
        success: function(json) {
            if (nonterminal) {
                clearos_dialog_box('info', lang_info, json.toString());
                return;
            }
            $('#terminal_out').html('');
            $.each(json, function (id, line) {
                $('#terminal_out').append('<span>' + line + '</span>');
            });
        },
        error: function(xhr, text, err) {
            $('#terminal_out').html('');
            clearos_dialog_box('error', lang_warning, xhr.responseText.toString());
        }
    });
}

/**
 * Sets visibility of network interface fields.
 */

function set_interface_fields() {
    // Static
    $('#ipaddr_field').hide();
    $('#netmask_field').hide();
    $('#gateway_field').hide();
    $('#enable_dhcp_field').hide();

    // DHCP
    $('#hostname_field').hide();
    $('#dhcp_dns_field').hide();

    // PPPoE
    $('#username_field').hide();
    $('#password_field').hide();
    $('#mtu_field').hide();
    $('#pppoe_dns_field').hide();

    type = $('#bootproto').val();

    if (type == 'static') {
        $('#ipaddr_field').show();
        $('#netmask_field').show();
        $('#gateway_field').show();
        $('#enable_dhcp_field').show();
    } else if (type == 'dhcp') {
        $('#hostname_field').show();
        $('#dhcp_dns_field').show();
    } else if (type == 'pppoe') {
        $('#username_field').show();
        $('#password_field').show();
        $('#mtu_field').show();
        $('#pppoe_dns_field').show();
    }
}

function get_modem_status() {
    $.ajax({
        type: 'GET',
        dataType: 'json',
        url: '/app/beams/modem/status',
        success: function(json) {
            if (json != undefined) {
                // Status
                if (json.state == 1)
                    $('#beams_status').addClass('beams-status-green');
                else if (json.state == 3)
                    $('#beams_status').addClass('beams-status-red');
                // Network
                if (json.network == 1)
                    $('#beams_network').addClass('beams-status-green');
                else if (json.network == 3)
                    $('#beams_network').addClass('beams-status-red');
                // Transmit 
                if (json.transmit == 1)
                    $('#beams_transmit').addClass('beams-status-green');
                else if (json.transmit == 3)
                    $('#beams_transmit').addClass('beams-status-red');
                // Receive
                if (json.receive == 1)
                    $('#beams_receive').addClass('beams-status-green');
                else if (json.receive == 3)
                    $('#beams_receive').addClass('beams-status-red');
            }

            if ($(location).attr('href').match(/.*\/modem\/terminal/) == null)
                window.setTimeout(get_modem_status, 30000);
        }
    });
}

// vim: syntax=javascript ts=4
